<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders\Mapping;

use Reva2\JsonApi\Decoders\Mapping\ClassMetadata;

/**
 * Tests for JSON API class metadata
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowIfValueNotConfiguredInDiscriminatorMap()
    {
        $metadata = new ClassMetadata('MyClass');
        $metadata->setDiscriminatorMap([
            'child' => 'ChildClass'
        ]);

        $this->assertSame('ChildClass', $metadata->getDiscriminatorClass('child'));

        $metadata->getDiscriminatorClass('unknown');
    }
}
