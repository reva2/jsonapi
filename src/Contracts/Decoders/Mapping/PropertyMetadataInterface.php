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
 * ApiObject property metadata
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface PropertyMetadataInterface extends GenericMetadataInterface
{
    /**
     * Returns name of property that store value
     *
     * @return string
     */
    public function getPropertyName();

    /**
     * Returns path to data. By default equals to property name.
     *
     * @return string
     */
    public function getDataPath();

    /**
     * Returns property data type
     *
     * @return string
     */
    public function getDataType();

    /**
     * Returns property data type additional parameters
     * 
     * @return string|array
     */
    public function getDataTypeParams();

    /**
     * Returns name of setter
     *
     * @return string|null
     */
    public function getSetter();
}
