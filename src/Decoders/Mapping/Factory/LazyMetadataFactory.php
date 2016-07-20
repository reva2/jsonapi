<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping\Factory;

use Reva2\JsonApi\Contracts\Decoders\Mapping\Cache\CacheInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Factory\MetadataFactoryInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Loader\LoaderInterface;

/**
 * JSON API metadata factory
 *
 * @package Reva2\JsonApi\Decoders\Mapping\Factory
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class LazyMetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Loaded metadata indexed by class name
     *
     * @var GenericMetadataInterface[]
     */
    protected $loadedClasses = [];

    /**
     * Constructor
     *
     * @param LoaderInterface $loader
     * @param CacheInterface|null $cache
     */
    public function __construct(LoaderInterface $loader, CacheInterface $cache = null)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function getMetadataFor($value)
    {
        if (!is_object($value) && !is_string($value)) {
            throw new \InvalidArgumentException(sprintf(
                "Cannot create metadata for non-objects. Got: %s",
                gettype($value)
            ));
        }
        
        $class = ltrim(((is_object($value)) ? get_class($value) : $value), '\\');
        
        if (array_key_exists($value, $this->loadedClasses)) {
            return $this->loadedClasses[$class];
        }
        
        if ((null !== $this->cache) && (false !== ($this->loadedClasses[$class] = $this->cache->read($class)))) {
            return $this->loadedClasses[$class];
        }
        
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf(
                "The class '%s' doesn't exist",
                $class
            ));
        }
        
        $reflection = new \ReflectionClass($class);
        
        $metadata = $this->loader->loadClassMetadata($reflection);
        if (($metadata instanceof ClassMetadataInterface) && (false !== ($parent = $reflection->getParentClass()))) {
            $metadata->mergeMetadata($this->getMetadataFor($parent->getName()));
        }

        if (null !== $this->cache) {
            $this->cache->write($metadata);
        }

        return $this->loadedClasses[$class] = $metadata;
    }

    /**
     * @inheritdoc
     */
    public function hasMetadataFor($value)
    {
        if (!is_object($value) && !is_string($value)) {
            return false;
        }

        $class = ltrim(((is_object($value)) ? get_class($value) : $value), '\\');

        if (class_exists($class)) {
            return true;
        }

        return false;
    }
}