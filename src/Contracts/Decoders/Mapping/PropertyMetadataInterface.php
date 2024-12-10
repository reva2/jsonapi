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
    public function getPropertyName(): string;

    /**
     * Returns path to data. Equals to property name by default.
     *
     * @return string
     */
    public function getDataPath(): string;

    /**
     * Returns property data type
     *
     * @return string
     */
    public function getDataType(): string;

    /**
     * Returns property data type additional parameters
     * 
     * @return mixed
     */
    public function getDataTypeParams(): mixed;

    /**
     * Returns name of setter
     *
     * @return string|null
     */
    public function getSetter(): ?string;

    /**
     * Returns converter for value
     *
     * @return string|null
     */
    public function getConverter(): ?string;

    /**
     * Returns serialization groups
     *
     * @return string[]
     */
    public function getGroups(): array;

    /**
     * Returns loaders
     *
     * @return array
     */
    public function getLoaders(): array;
}
