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

use Neomerx\JsonApi\Document\Error;
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
    public $discField;

    /**
     * @var array
     * @internal
     */
    public $discMap;

    /**
     * @var string
     * @internal
     */
    public $discError;

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
     * @param PropertyMetadataInterface|null $field
     * @return $this
     */
    public function setDiscriminatorField(PropertyMetadataInterface $field = null)
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
     * @return string
     */
    public function getDiscriminatorError()
    {
        return $this->discError;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setDiscriminatorError($error)
    {
        $this->discError = $error;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorClass($value)
    {
        if (!array_key_exists($value, $this->discMap)) {
            $error = new Error(
                rand(),
                null,
                422,
                self::INVALID_DISCRIMINATOR_VALUE,
                str_replace('{{value}}',  (string) $value, $this->discError)
            );

            throw new JsonApiException($error, 422);
        }

        return $this->discMap[$value];
    }

    /**
     * @inheritdoc
     */
    public function mergeMetadata($metadata)
    {
        // Nothing to do here
    }
}
