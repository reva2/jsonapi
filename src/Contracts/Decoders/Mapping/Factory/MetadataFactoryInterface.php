<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders\Mapping\Factory;

/**
 * Metadata factory
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping\Factory
 */
interface MetadataFactoryInterface
{
    /**
     * Returns metadata for specified value 
     * 
     * @param string $value
     */
    public function getMetadataFor(string $value): mixed;

    /**
     * Returns whether we have metadata for specified value
     *
     * @param mixed $value
     * @return bool
     */
    public function hasMetadataFor(mixed $value): bool;
}
