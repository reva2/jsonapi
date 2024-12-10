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
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use RuntimeException;

/**
 * JSON API resource metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResourceMetadata extends ClassMetadata implements ResourceMetadataInterface
{
    /**
     * @var string|null
     * @internal
     */
    public ?string $name = null;

    /**
     * @var string|null
     * @internal
     */
    public ?string $loader = null;

    /**
     * @var PropertyMetadataInterface|null
     */
    public ?PropertyMetadataInterface $idMetadata = null;

    /**
     * @var PropertyMetadataInterface[]
     * @internal
     */
    public array $attributes = [];

    /**
     * @var PropertyMetadataInterface[]
     * @internal
     */
    public array $relationships = [];

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PropertyMetadataInterface|null
     */
    public function getIdMetadata(): ?PropertyMetadataInterface
    {
        return $this->idMetadata;
    }

    /**
     * @param PropertyMetadataInterface|null $idMetadata
     * @return $this
     */
    public function setIdMetadata(PropertyMetadataInterface $idMetadata = null): self
    {
        $this->idMetadata = $idMetadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Add resource attribute metadata
     *
     * @param PropertyMetadataInterface $attribute
     * @return $this
     */
    public function addAttribute(PropertyMetadataInterface $attribute): self
    {
        $this->attributes[$attribute->getPropertyName()] = $attribute;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    /**
     * Add resource relationship metadata
     *
     * @param PropertyMetadataInterface $relationship
     * @return $this
     */
    public function addRelationship(PropertyMetadataInterface $relationship): self
    {
        $this->relationships[$relationship->getPropertyName()] = $relationship;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLoader(): ?string
    {
        return $this->loader;
    }

    /**
     * @param string|null $loader
     * @return $this
     */
    public function setLoader(string $loader = null): self
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata(mixed $metadata = null): self
    {
        if (null !== $metadata) {
            if (!$metadata instanceof ResourceMetadataInterface) {
                /* @var $metadata GenericMetadataInterface */

                throw new RuntimeException(sprintf(
                    "Failed to merge metadata from parent class %s",
                    $metadata->getClassName()
                ));
            }

            if (empty($this->name)) {
                $this->name = $metadata->getName();
            }

            if (empty($this->loader)) {
                $this->loader = $metadata->getLoader();
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
