<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use Reva2\JsonApi\Annotations\Attribute;
use Reva2\JsonApi\Annotations\Document as ApiDocument;
use Reva2\JsonApi\Annotations\Relationship;
use Reva2\JsonApi\Annotations\Resource as ApiResource;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Loader\LoaderInterface;
use Reva2\JsonApi\Decoders\Mapping\AttributeMetadata;
use Reva2\JsonApi\Decoders\Mapping\DocumentMetadata;
use Reva2\JsonApi\Decoders\Mapping\GenericMetadata;
use Reva2\JsonApi\Decoders\Mapping\ResourceMetadata;

/**
 * Loads JSON API metadata using a Doctrine annotations
 *
 * @package Reva2\JsonApi\Decoders\Mapping\Loader
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class AnnotationLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function loadClassMetadata(\ReflectionClass $class)
    {
        if (null !== ($resource = $this->reader->getClassAnnotation($class, ApiResource::class))) {
            return $this->loadResourceMetadata($resource, $class);
        } elseif (null !== ($document = $this->reader->getClassAnnotation($class, ApiDocument::class))) {
            return $this->loadDocumentMetadata($document, $class);
        } else {
            return new GenericMetadata($class->getName());
        }
    }

    /**
     * Parse JSON API resource metadata
     *
     * @param ApiResource $resource
     * @param \ReflectionClass $class
     * @return ResourceMetadata
     */
    private function loadResourceMetadata(ApiResource $resource, \ReflectionClass $class)
    {
        $metadata = new ResourceMetadata($resource->name, $class->getName());

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Attribute) {
                    $metadata->addAttribute($this->parseAttribute($annotation, $property));
                } elseif ($annotation instanceof Relationship) {
                    $metadata->addRelationship($this->parseRelationship($annotation, $property));
                }
            }
        }

        return $metadata;
    }

    /**
     * Parse JSON API document metadata
     *
     * @param ApiDocument $document
     * @param \ReflectionClass $class
     * @return DocumentMetadata
     */
    private function loadDocumentMetadata(ApiDocument $document, \ReflectionClass $class)
    {
        $metadata = new DocumentMetadata($class->getName());

        return $metadata;
    }

    private function parseAttribute(Attribute $attribute, \ReflectionProperty $property)
    {
        $metadata = new AttributeMetadata($property->getName(), $property->class);
        if ($attribute->type) {
            $this->parseAttributeType($attribute->type, $metadata);
        } else {
            $this->parsePropertyDataType($property, $metadata);
        }
        
    }
}