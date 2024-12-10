<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders\Mapping\Cache;

use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;

/**
 * Persists ClassMetadata instances in cache
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping\Cache
 */
interface CacheInterface
{
    /**
     * Returns whether metadata for given class exists in the cache
     *
     * @param string $class Class name
     * @return bool
     */
    public function has(string $class): bool;

    /**
     * Returns metadata for the given class from cache
     *
     * @param string $class Class name
     * @return GenericMetadataInterface|false A ClassMetadata instance or false on miss
     */
    public function read(string $class): mixed;

    /**
     * Stores a class metadata in the cache
     *
     * @param GenericMetadataInterface $metadata
     */
    public function write(GenericMetadataInterface $metadata);
}
