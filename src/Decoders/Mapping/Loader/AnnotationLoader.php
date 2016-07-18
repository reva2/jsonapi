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
use Reva2\JsonApi\Annotations\Property;
use Reva2\JsonApi\Annotations\Relationship;
use Reva2\JsonApi\Annotations\Resource as ApiResource;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Loader\LoaderInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\DocumentMetadata;
use Reva2\JsonApi\Decoders\Mapping\ObjectMetadata;
use Reva2\JsonApi\Decoders\Mapping\PropertyMetadata;
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
            return $this->loadObjectMetadata($class);
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
                    $metadata->addAttribute($this->loadPropertyMetadata($annotation, $property));
                } elseif ($annotation instanceof Relationship) {
                    $metadata->addRelationship($this->loadPropertyMetadata($annotation, $property));
                }
            }
        }

        if (false !== ($parent = $class->getParentClass())) {
            $parentMetadata = $this->loadClassMetadata($parent);
            if (!$parentMetadata instanceof ResourceMetadataInterface) {
                throw new \RuntimeException(sprintf(
                    "Failed to merge metadata from parent class '%s'",
                    $parent->name
                ));
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

    /**
     * @param \ReflectionClass $class
     * @return ObjectMetadata
     */
    private function loadObjectMetadata(\ReflectionClass $class)
    {
        $metadata = new ObjectMetadata($class->name);

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Property) {
                    $metadata->addProperty($this->loadPropertyMetadata($annotation, $property));
                }
            }
        }

        if (false !== ($parent = $class->getParentClass())) {
            $parentMetadata = $this->loadClassMetadata($parent);
            if (!$parentMetadata instanceof ObjectMetadataInterface) {
                throw new \RuntimeException(sprintf(
                    "Failed to merge metadata from parent class '%s'",
                    $parent->name
                ));
            }

            $metadata->mergeMetadata($parentMetadata);
        }

        return $metadata;
    }

    /**
     * Parse property metadata
     *
     * @param Property $annotation
     * @param \ReflectionProperty $property
     * @return PropertyMetadata
     */
    private function loadPropertyMetadata(Property $annotation, \ReflectionProperty $property)
    {
        $metadata = new PropertyMetadata($property->name, $property->class);

        list($dataType, $dataTypeParams) = $this->parseDataType($annotation, $property);
        $metadata
            ->setDataType($dataType)
            ->setDataTypeParams($dataTypeParams);

        if ($annotation->setter) {
            $metadata->setSetter($annotation->setter);
        } elseif (false === $property->isPublic()) {
            $setter = 'set' . ucfirst($property->name);
            if (false == $property->getDeclaringClass()->hasMethod($setter)) {
                throw new \RuntimeException(sprintf(
                    "Couldn't find setter for non public property: %s:%s",
                    $property->class,
                    $property->name
                ));
            }

            $metadata->setSetter($setter);
        }

        return $metadata;
    }

    /**
     * Parse property data type
     *
     * @param Property $annotation
     * @param \ReflectionProperty $property
     * @return array
     */
    private function parseDataType(Property $annotation, \ReflectionProperty $property)
    {
        if (!empty($annotation->parser)) {
            if (!$property->getDeclaringClass()->hasMethod($annotation->parser)) {
                throw new \InvalidArgumentException(sprintf(
                    "Custom parser function %s:%s() for property '%s' does not exist",
                    $property->class,
                    $annotation->parser,
                    $property->name
                ));
            }
            return ['custom', $annotation->parser];
        } elseif (!empty($annotation->type)) {
            return $this->parseDataTypeString($annotation->type);
        } elseif (preg_match('~@var\s(.*?)\s~si', $property->getDocComment(), $matches)) {
            return $this->parseDataTypeString($matches[1]);
        } else {
            return ['raw', null];
        }
    }

    /**
     * Parse data type string
     *
     * @param string $type
     * @return array
     */
    private function parseDataTypeString($type)
    {
        $params = null;

        if ($this->isScalarDataType($type)) {
            $dataType = 'scalar';
            $params = $type;
        } elseif (preg_match('~^DateTime(<(.*?)>)?$~', $type, $matches)) {
            $dataType = 'datetime';
            if (3 === count($matches)) {
                $params = $matches[2];
            }
        } elseif (
            (preg_match('~Array(<(.*?)>)?$~si', $type, $matches)) ||
            (preg_match('~^(.*?)\[\]$~si', $type, $matches))
        ) {
            $dataType = 'array';
            if (3 === count($matches)) {
                $params = $this->parseDataTypeString($matches[2]);
            } elseif (2 === count($matches)) {
                $params = $this->parseDataTypeString($matches[1]);
            } else {
                $params = ['raw', null];
            }
        } else {
            $type = ltrim($type, '\\');

            if (!class_exists($type) && !interface_exists($type)) {
                throw new \InvalidArgumentException(sprintf(
                    "Unknown object type '%s' specified",
                    $type
                ));
            }

            $dataType = 'object';
            $params = $type;
        }

        return [$dataType, $params];
    }

    /**
     * Returns true if specified type scalar. False otherwise.
     *
     * @param string $type
     * @return bool
     */
    private function isScalarDataType($type)
    {
        return in_array($type, ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'double']);
    }
}