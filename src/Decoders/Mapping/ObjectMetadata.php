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

use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use RuntimeException;

/**
 * ObjectMetadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ObjectMetadata extends ClassMetadata implements ObjectMetadataInterface
{
    /**
     * @var PropertyMetadataInterface[]
     */
    public array $properties = [];

    /**
     * Returns object properties metadata
     *
     * @return PropertyMetadataInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Adds property metadata
     *
     * @param PropertyMetadataInterface $metadata
     * @return $this
     */
    public function addProperty(PropertyMetadataInterface $metadata): self
    {
        $this->properties[$metadata->getPropertyName()] = $metadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata($metadata = null): self
    {
        if (null !== $metadata) {
            if (!$metadata instanceof ObjectMetadataInterface) {
                /* @var $metadata GenericMetadataInterface */

                throw new RuntimeException(sprintf(
                    "Failed to merge metadata from parent class %s",
                    $metadata->getClassName()
                ));
            }

            $this->properties = array_merge($this->properties, $metadata->getProperties());
        }

        return $this;
    }
}
