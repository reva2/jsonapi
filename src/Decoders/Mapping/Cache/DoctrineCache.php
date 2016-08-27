<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping\Cache;

use Doctrine\Common\Cache\Cache;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Cache\CacheInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;

/**
 * Adapts doctrine cache to CacheInterface
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping\Cache
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DoctrineCache implements CacheInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function has($class)
    {
        return $this->cache->contains($class);
    }

    /**
     * @inheritdoc
     */
    public function read($class)
    {
        return $this->cache->fetch($class);
    }

    /**
     * @inheritdoc
     */
    public function write(GenericMetadataInterface $metadata)
    {
        $this->cache->save($metadata->getClassName(), $metadata);
    }
}
