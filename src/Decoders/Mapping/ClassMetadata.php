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

use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;

/**
 * Generic class metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ClassMetadata extends GenericMetadata implements ClassMetadataInterface
{
    const INVALID_DISCRIMINATOR_VALUE = '19cf8396-0fe1-487f-bd32-d5d34e2b4f68';

    /***
     * @var PropertyMetadataInterface|null
     * @internal
     */
    public ?PropertyMetadataInterface $discField = null;

    /**
     * @var array|null
     * @internal
     */
    public ?array $discMap = null;

    /**
     * @var string|null
     * @internal
     */
    public ?string $discError = null;

    /**
     * @inheritdoc
     */
    public function getDiscriminatorField(): ?PropertyMetadataInterface
    {
        return $this->discField;
    }

    /**
     * Sets discriminator field
     *
     * @param PropertyMetadataInterface|null $field
     * @return $this
     */
    public function setDiscriminatorField(PropertyMetadataInterface $field = null): self
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
    public function setDiscriminatorMap(array $map): self
    {
        $this->discMap = $map;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDiscriminatorError(): ?string
    {
        return $this->discError;
    }

    /**
     * @param string|null $error
     * @return $this
     */
    public function setDiscriminatorError(string $error = null): self
    {
        $this->discError = $error;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorClass(mixed $value): string
    {
        if (!array_key_exists($value, $this->discMap)) {
            $error = new Error(
                idx: rand(),
                status: '422',
                code: self::INVALID_DISCRIMINATOR_VALUE,
                title: str_replace('{{value}}',  (string) $value, $this->discError)
            );

            throw new JsonApiException($error, 422);
        }

        return $this->discMap[$value];
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata(mixed $metadata): self
    {
        // Nothing to do here
        return $this;
    }
}
