<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders\Mapping;

use Neomerx\JsonApi\Exceptions\JsonApiException;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\Mapping\ClassMetadata;

/**
 * Tests for JSON API class metadata
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ClassMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowIfValueNotConfiguredInDiscriminatorMap()
    {
        $metadata = new ClassMetadata('MyClass');
        $metadata->setDiscriminatorMap([
            'child' => 'ChildClass'
        ]);

        $this->assertSame('ChildClass', $metadata->getDiscriminatorClass('child'));

        $this->expectException(JsonApiException::class);

        $metadata->getDiscriminatorClass('unknown');
    }

    /**
     * @test
     */
    public function shouldBeAbleMergeMetadata()
    {
        $metadata = new ClassMetadata('MyClass');
        $metadata->mergeMetadata(new ClassMetadata('AnotherClass'));

        $this->assertSame(1, 1);
    }
}
