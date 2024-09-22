<?php

namespace Reva2\JsonApi\Tests\Decoders\Mapping\Cache;

use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\Mapping\Cache\PsrCache;
use Reva2\JsonApi\Decoders\Mapping\GenericMetadata;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class PsrCacheTest extends TestCase
{
    /**
     * @test
     */
    public function testCache()
    {
        $pool = new ArrayAdapter();
        $cache = new PsrCache($pool);

        $class = 'MyNamespace\MyClass';
        $anotherClass = 'MyNamespace\AnotherClass';
        $metadata = new GenericMetadata($class);

        $cache->write($metadata);

        $this->assertTrue($cache->has($class));
        $this->assertEquals($metadata, $cache->read($class));

        $this->assertFalse($cache->has($anotherClass));
        $this->assertFalse($cache->read($anotherClass));
    }
}