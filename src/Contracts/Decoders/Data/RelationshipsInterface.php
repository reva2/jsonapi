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
 * Interface for objects that represent relationships
 * of JSON API resources
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Data
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface RelationshipsInterface
{
    /**
     * Returns true if request contained specified
     * relationship. False otherwise.
     *
     * @param string $relationship
     * @return bool
     */
    public function contains($relationship);
}