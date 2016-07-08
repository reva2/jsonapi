<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders\Mapping\Loader;

/**
 * Loads JSON API metadata into ClassMetadata instance
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping\Loader
 */
interface LoaderInterface
{
    /**
     * Load JSON API metadata into a ClassMetadata instance
     *
     * @param \ReflectionClass $class
     * @return bool Whether the loader succeeded
     */
    public function loadClassMetadata(\ReflectionClass $class);
}