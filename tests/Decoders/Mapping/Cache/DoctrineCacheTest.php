<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders\Mapping\Cache;

use Doctrine\Common\Cache\Cache;
use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\Cache\DoctrineCache;
use Reva2\JsonApi\Decoders\Mapping\GenericMetadata;

/**
 * Test for metadata cache implementation that use doctrine cache
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping\Cache
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DoctrineCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldUseDoctrineCache()
    {
        $class = 'MyClass';
        $anotherClass = 'AnotherClass';
        $metadata = new GenericMetadata($class);

        $cache = $this->getMockBuilder(Cache::class)->getMock();

        $cache
            ->expects($this->once())
            ->method('save')
            ->with($class, $metadata);

        $cache
            ->expects($this->any())
            ->method('contains')
            ->withConsecutive([$class], [$anotherClass])
            ->willReturnOnConsecutiveCalls(true, false);

        $cache
            ->expects($this->any())
            ->method('fetch')
            ->withConsecutive([$class], [$anotherClass])
            ->willReturnOnConsecutiveCalls($metadata, false);

        $metaCache = new DoctrineCache($cache);
        $metaCache->write($metadata);

        $this->assertTrue($metaCache->has($class));
        $this->assertSame($metadata, $metaCache->read($class));

        $this->assertFalse($metaCache->has($anotherClass));
        $this->assertFalse($metaCache->read($anotherClass));
    }
}
