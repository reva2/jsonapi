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
 * JSON API resource relationship metadata
 * 
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface RelationshipMetadataInterface extends ReferenceMetadataInterface
{
    /**
     * Returns whether relationship contains ORM entity
     * 
     * @return bool
     */
    public function isOrmEntity();

    /**
     * Returns name of class that represent ORM entity
     *
     * @return string|null
     */
    public function getOrmEntityClass();
}