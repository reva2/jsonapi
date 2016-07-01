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
 * Interface for objects that represent JSON API document
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Data
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DocumentInterface
{
    /**
     * Sets document data
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * Returns document data
     *
     * @return mixed
     */
    public function getData();
}