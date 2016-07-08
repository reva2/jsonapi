<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders;

use Doctrine\Common\Proxy\Exception\OutOfBoundsException;
use Reva2\JsonApi\Contracts\Decoders\Data\AttributesInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\RelationshipsInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\ResourceInterface;
use Reva2\JsonApi\Contracts\Decoders\DecodersFactoryInterface;
use Reva2\JsonApi\Decoders\DataParser;
use Reva2\JsonApi\Decoders\ResourceDecoder;

/**
 * Test for resource decoder
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ResourceDecoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldDecodeResource()
    {
        $attributes = new \stdClass();
        $relationships = new \stdClass();

        $data = new \stdClass();
        $data->id = '100';
        $data->type = 'resource1';
        $data->attributes = $attributes;
        $data->relationships = $relationships;

        $resourceAttributes = $this->getMockBuilder(AttributesInterface::class)->getMock();
        $resourceRelationships = $this->getMockBuilder(RelationshipsInterface::class)->getMock();

        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $resource
            ->expects($this->once())
            ->method('setAttributes')
            ->with($resourceAttributes)
            ->willReturnSelf();

        $resource
            ->expects($this->once())
            ->method('setRelationships')
            ->with($resourceRelationships)
            ->willReturnSelf();

        $decoder = $this->getDecoder();

        $decoder
            ->expects($this->once())
            ->method('getResourceType')
            ->willReturn('resource1');

        $decoder
            ->expects($this->once())
            ->method('createResource')
            ->willReturn($resource);

        $decoder
            ->expects($this->once())
            ->method('parseAttributes')
            ->with($attributes)
            ->willReturn($resourceAttributes);

        $decoder
            ->expects($this->once())
            ->method('parseRelationships')
            ->with($relationships)
            ->willReturn($resourceRelationships);

        $this->assertSame($resource, $decoder->decode($data, $this->getParser()));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 409
     */
    public function shouldThrowExceptionOnResourceTypeMismatch()
    {
        $data = new \stdClass();
        $data->type = 'resource2';

        $decoder = $this->getDecoder();
        $decoder->expects($this->atLeastOnce())->method('getResourceType')->willReturn('resource1');
        $decoder->expects($this->any())->method('createResource')->willReturn(null);
        $decoder->expects($this->any())->method('parseAttributes')->willReturn(null);
        $decoder->expects($this->any())->method('parseRelationships')->willReturn(null);

        $decoder->decode($data, $this->getParser());
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp #Resource object must implement .+ interface#
     */
    public function shouldThrowExceptionOnInvalidResourceObject()
    {
        $data = new \stdClass();
        $data->type = 'resource1';

        $resource = new \stdClass();

        $decoder = $this->getDecoder();
        $decoder->expects($this->any())->method('getResourceType')->willReturn('resource1');
        $decoder->expects($this->once())->method('createResource')->willReturn($resource);
        $decoder->expects($this->any())->method('parseAttributes')->willReturn(null);
        $decoder->expects($this->any())->method('parseRelationships')->willReturn(null);

        $decoder->decode($data, $this->getParser());
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp #Attributes object must implement .+ interface#
     */
    public function shouldThrowExceptionOnInvalidAttributesObject()
    {
        $data = new \stdClass();
        $data->type = 'resource1';
        $data->attributes = new \stdClass();

        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $attributes = new \stdClass();

        $decoder = $this->getDecoder();
        $decoder->expects($this->once())->method('getResourceType')->willReturn('resource1');
        $decoder->expects($this->once())->method('createResource')->willReturn($resource);
        $decoder->expects($this->once())->method('parseAttributes')->withAnyParameters()->willReturn($attributes);
        $decoder->expects($this->any())->method('parseRelationships')->willReturn(null);

        $decoder->decode($data, $this->getParser());
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp #Relationships object must implement .+ interface#
     */
    public function shouldThrowExceptionOnInvalidRelationshipsObject()
    {
        $data = new \stdClass();
        $data->type = 'resource1';
        $data->relationships = new \stdClass();

        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $relationships = new \stdClass();

        $decoder = $this->getDecoder();
        $decoder->expects($this->once())->method('getResourceType')->willReturn('resource1');
        $decoder->expects($this->once())->method('createResource')->willReturn($resource);
        $decoder->expects($this->any())->method('parseAttributes')->willReturn(null);
        $decoder->expects($this->once())->method('parseRelationships')->willReturn($relationships);

        $decoder->decode($data, $this->getParser());
    }

    /**
     * @return DataParser
     */
    private function getParser()
    {
        $factory = $this->getMockBuilder(DecodersFactoryInterface::class)->getMock();

        return new DataParser($factory);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceDecoder
     */
    private function getDecoder()
    {
        return $this
            ->getMockBuilder(ResourceDecoder::class)
            ->setMethods(['getResourceType', 'createResource', 'parseAttributes', 'parseRelationships'])
            ->getMock();
    }
}
