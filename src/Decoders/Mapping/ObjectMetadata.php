<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping;

use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;

/**
 * ObjectMetadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ObjectMetadata extends ClassMetadata implements ObjectMetadataInterface
{
    /**
     * @var PropertyMetadataInterface[]
     */
    public $properties = [];

    /**
     * Returns object properties metadata
     *
     * @return PropertyMetadataInterface[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Adds property metadata
     *
     * @param PropertyMetadataInterface $metadata
     * @return $this
     */
    public function addProperty(PropertyMetadataInterface $metadata)
    {
        $this->properties[$metadata->getPropertyName()] = $metadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata(ObjectMetadataInterface $metadata = null)
    {
        if (null === $metadata) {
            return $this;
        } elseif (!$metadata instanceof ObjectMetadataInterface) {
            throw new \InvalidArgumentException(sprintf(
                "Couldn't merge metadata from %s instance",
                get_class($metadata)
            ));
        }

        $this->properties = array_merge($this->properties, $metadata->getProperties());

        return $this;
    }
}