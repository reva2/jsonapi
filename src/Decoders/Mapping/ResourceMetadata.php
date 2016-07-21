<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping;

use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;

/**
 * JSON API resource metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResourceMetadata extends ClassMetadata implements ResourceMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    public $name;

    /**
     * @var PropertyMetadataInterface
     */
    public $idMetadata;

    /**
     * @var PropertyMetadataInterface[]
     * @internal
     */
    public $attributes = [];

    /**
     * @var PropertyMetadataInterface[]
     * @internal
     */
    public $relationships = [];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PropertyMetadataInterface
     */
    public function getIdMetadata()
    {
        return $this->idMetadata;
    }

    /**
     * @param PropertyMetadataInterface $idMetadata
     * @return $this
     */
    public function setIdMetadata($idMetadata)
    {
        $this->idMetadata = $idMetadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add resource attribute metadata
     *
     * @param PropertyMetadataInterface $attribute
     * @return $this
     */
    public function addAttribute(PropertyMetadataInterface $attribute)
    {
        $this->attributes[$attribute->getPropertyName()] = $attribute;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Add resource relationship metadata
     *
     * @param PropertyMetadataInterface $relationship
     * @return $this
     */
    public function addRelationship(PropertyMetadataInterface $relationship)
    {
        $this->relationships[$relationship->getPropertyName()] = $relationship;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata($metadata = null)
    {
        if (null !== $metadata) {
            if (!$metadata instanceof ResourceMetadataInterface) {
                /* @var $metadata \Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface */

                throw new \RuntimeException(sprintf(
                    "Failed to merge metadata from parent class %s",
                    $metadata->getClassName()
                ));
            }

            if (empty($this->name)) {
                $this->name = $metadata->getName();
            }

            if (null === $this->idMetadata) {
                $this->idMetadata = $metadata->getIdMetadata();
            }

            $this->attributes = array_merge($this->attributes, $metadata->getAttributes());
            $this->relationships = array_merge($this->relationships, $metadata->getRelationships());
        }

        return $this;
    }
}