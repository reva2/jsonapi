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

use Reva2\JsonApi\Decoders\Mapping\ObjectMetadata;
use Reva2\JsonApi\Decoders\Mapping\ResourceMetadata;

/**
 * Tests for JSON API resource metadata
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResourceMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowOnInvalidMetadataDuringMerge()
    {
        $resMetadata = new ResourceMetadata('MyResource');
        $objMetadata = new ObjectMetadata('MyObject');

        $resMetadata->mergeMetadata($objMetadata);
    }
}
