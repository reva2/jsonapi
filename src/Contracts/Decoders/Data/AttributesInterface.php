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
 * Interface for objects that represent attributes
 * of JSON API resources
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Data
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface AttributesInterface
{
    /**
     * Returns true if request contains specified attribute.
     * False otherwise.
     *
     * @param string $attribute
     * @return bool
     */
    public function contains($attribute);
}