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

use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\Mapping\ObjectMetadata;
use Reva2\JsonApi\Decoders\Mapping\ResourceMetadata;
use RuntimeException;

/**
 * Test for JSON API objects metadata
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ObjectMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowOnInvalidMetadataDuringMerge()
    {
        $objMetadata = new ObjectMetadata('MyObject');
        $resMetadata = new ResourceMetadata('MyResource');

        $this->expectException(RuntimeException::class);

        $objMetadata->mergeMetadata($resMetadata);
    }
}
