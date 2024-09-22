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
        return $this->pool->getItem($this->getCacheKey($class))->isHit();
    }

    public function read($class)
    {
        $item = $this->pool->getItem($this->getCacheKey($class));
        if ($item->isHit()) {
            return $item->get();
        }

        return false;
    }

    public function write(GenericMetadataInterface $metadata)
    {
        $item = $this->pool->getItem($this->getCacheKey($metadata->getClassName()));
        $item->set($metadata);

        $this->pool->save($item);
    }

    private function getCacheKey(string $class): string
    {
        return str_replace('\\', '_', $class);
    }
}