<?php

namespace Reva2\JsonApi\Decoders\Mapping\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Cache\CacheInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;

class PsrCache implements CacheInterface
{
    protected CacheItemPoolInterface $pool;

    /**
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function has($class)
    {
        return $this->pool->getItem($class)->isHit();
    }

    public function read($class)
    {
        $item = $this->pool->getItem($class);
        if ($item->isHit()) {
            return $item->get();
        }

        return false;
    }

    public function write(GenericMetadataInterface $metadata)
    {
        $item = $this->pool->getItem($metadata->getClassName());
        $item->set($metadata);

        $this->pool->save($item);
    }
}