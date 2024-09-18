<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Decoders\Mapping\Factory;

use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Cache\CacheInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Loader\LoaderInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\Factory\LazyMetadataFactory;
use Reva2\JsonApi\Decoders\Mapping\Loader\AttributeLoader;
use Reva2\JsonApi\Tests\Fixtures\Resources\Cat;

/**
 * Tests for lazy metadata factory
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping\Factory
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class LazyMetadataFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldProperlyHandleInvalidParameters()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();

        $factory = new LazyMetadataFactory($loader);
        $this->assertFalse($factory->hasMetadataFor(false));

        try {
            $factory->getMetadataFor(false);

            $this->fail("Should throw exception for invalid parameter");
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }

        try {
            $factory->getMetadataFor('Reva2\JsonApi\UnknownClass');

            $this->fail("Should throw exception if specified class doesn't exists");
        } catch (\Exception $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
        }
    }

    /**
     * @test
     */
    public function shouldLoadMetadataForClass()
    {
        $cache = $this->getMockBuilder(CacheInterface::class)->getMock();

        $cache
            ->expects($this->atLeastOnce())
            ->method('read')
            ->withAnyParameters()
            ->willReturn(false);

        $cache
            ->expects($this->atLeastOnce())
            ->method('write')
            ->withAnyParameters();

        $loader = new AttributeLoader();

        $factory = new LazyMetadataFactory($loader, $cache);

        $metadata = $factory->getMetadataFor(Cat::class);
        $this->assertInstanceOf(ResourceMetadataInterface::class, $metadata);

        /* @var $metadata ResourceMetadataInterface */

        $this->assertSame('pets', $metadata->getName());

        $attributes = $metadata->getAttributes();
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('family', $attributes);

        $relationships = $metadata->getRelationships();
        $this->assertIsArray($relationships);
        $this->assertArrayHasKey('store', $relationships);

        $this->assertSame($metadata, $factory->getMetadataFor(Cat::class));
    }

    /**
     * @test
     */
    public function shouldBeAbleLoadMetadataIfClassExists()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();

        $factory = new LazyMetadataFactory($loader);

        $this->assertTrue($factory->hasMetadataFor(Cat::class));
        $this->assertFalse($factory->hasMetadataFor('Reva2\JsonApi\UnknownClass'));
    }
}
