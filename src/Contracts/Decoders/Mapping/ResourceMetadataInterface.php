<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Decoders\Mapping;

/**
 * JSON API resource metadata
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ResourceMetadataInterface extends GenericMetadataInterface
{

    /**
     * Returns resource name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns metadata resource attributes
     *
     * @return AttributeMetadataInterface[]
     */
    public function getAttributes();

    /**
     * Returns metadata for resource relationships
     *
     * @return RelationshipMetadataInterface
     */
    public function getRelationships();

    /**
     * Returns name of field that store discriminator value
     *
     * @return string|null
     */
    public function getDiscriminatorField();

    /**
     * Returns class name that corresponds to specified
     * discriminator value
     *
     * @param string $value
     * @return string
     */
    public function getDiscriminatorClass($value);

    /**
     * Merge metadata from parent resources
     *
     * @param mixed $metadata
     * @return mixed
     */
    public function mergeMetadata($metadata);
}