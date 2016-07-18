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
     * Merge resource metadata
     *
     * @param ResourceMetadataInterface $metadata
     * @return $this
     */
    public function mergeMetadata(ResourceMetadataInterface $metadata = null)
    {
        if (null === $metadata) {
            return $this;
        }

        $this->attributes = array_merge($this->attributes, $metadata->getAttributes());
        $this->relationships = array_merge($this->relationships, $metadata->getRelationships());

        return $this;
    }
}