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

use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;

/**
 * Generic class metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ClassMetadata extends GenericMetadata implements ClassMetadataInterface
{
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
     * @inheritdoc
     */
    public function mergeMetadata($metadata)
    {
        throw new \RuntimeException('Not implemented');
    }
}