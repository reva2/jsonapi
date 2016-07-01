<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders\Data;

/**
 * Interface for classes that represent JSON API resources
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Data
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ResourceInterface
{
    /**
     * Sets resource ID
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Sets resource attributes
     *
     * @param AttributesInterface $attributes
     * @return $this
     */
    public function setAttributes(AttributesInterface $attributes);

    /**
     * Sets resource relationship
     *
     * @param RelationshipsInterface $relationship
     * @return $this
     */
    public function setRelationships(RelationshipsInterface $relationship);
}