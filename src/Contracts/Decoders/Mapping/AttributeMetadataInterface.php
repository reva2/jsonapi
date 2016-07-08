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
 * JSON API resource attribute metadata
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface AttributeMetadataInterface extends PropertyMetadataInterface
{
    /**
     * Returns data type
     *
     * @return string
     */
    public function getDataType();

    /**
     * Returns format for datetime values
     *
     * @return string
     */
    public function getDatetimeFormat();

    /**
     * Return data type for array items
     *
     * @return string
     */
    public function getItemType();
}