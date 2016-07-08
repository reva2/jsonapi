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

use Reva2\JsonApi\Contracts\Decoders\Mapping\AttributeMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\RelationshipMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;

/**
 * JSON API resource metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResourceMetadata extends GenericMetadata implements ResourceMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    public $name;

    /**
     * @var AttributeMetadataInterface[]
     * @internal
     */
    public $attributes = [];

    /**
     * @var RelationshipMetadataInterface[]
     * @internal
     */
    public $relationships = [];

    /***
     * @var string|null
     * @internal
     */
    public $discField;

    /**
     * @var array
     * @internal
     */
    public $discMap;

    /**
     * Constructor
     *
     * @param string $name
     * @param $className
     */
    public function __construct($name, $className)
    {
        parent::__construct($className);

        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
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
     * @param AttributeMetadataInterface $attribute
     * @return $this
     */
    public function addAttribute(AttributeMetadataInterface $attribute)
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
     * @param RelationshipMetadataInterface $relationship
     * @return $this
     */
    public function addRelationship(RelationshipMetadataInterface $relationship)
    {
        $this->relationships[$relationship->getPropertyName()] = $relationship;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorField()
    {
        return $this->discField;
    }

    /**
     * Sets discriminator field
     *
     * @param string|null $field
     * @return $this
     */
    public function setDiscriminatorField($field = null)
    {
        $this->discField = $field;

        return $this;
    }

    /**
     * Sets discriminator map
     *
     * @param array $map
     * @return $this
     */
    public function setDiscriminatorMap(array $map)
    {
        $this->discMap = $map;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorClass($value)
    {
        if (!array_key_exists($value, $this->discMap)) {
            throw new \InvalidArgumentException(sprintf(
                "Discriminator class for value '%s' not specified",
                $value
            ));
        }

        return $this->discMap[$value];
    }

    /**
     * Merge resource metadata
     *
     * @param mixed $metadata
     * @return $this
     */
    public function mergeMetadata($metadata)
    {
        if (null === $metadata) {
            return $this;
        } elseif (!$metadata instanceof ResourceMetadataInterface) {
            throw new \InvalidArgumentException(sprintf(
                "Couldn't merge metadata from %s instance",
                get_class($metadata)
            ));
        }

        $this->attributes = array_merge($this->attributes, $metadata->getAttributes());
        $this->relationships = array_merge($this->relationships, $metadata->getRelationships());

        return $this;
    }
}