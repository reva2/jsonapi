<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Decoders;

use Reva2\JsonApi\Contracts\Decoders\DocumentDecoderInterface;
use Reva2\JsonApi\Contracts\Decoders\QueryParamsDecoderInterface;
use Reva2\JsonApi\Contracts\Decoders\ResourceDecoderInterface;
use Reva2\JsonApi\Decoders\DecodersFactory;

/**
 * Test for decoders factory
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class DecodersFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldRegisterResourceDecoders()
    {
        $decoder = $this->getMockBuilder(ResourceDecoderInterface::class)->getMock();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $decoderClass = get_class($decoder);

        $factory = new DecodersFactory();
        $factory
            ->registerResourceDecoder('resource1', $closure)
            ->registerResourceDecoder('resource2', $decoderClass);

        $this->assertSame($decoder, $factory->getResourceDecoder('resource1'));
        $this->assertInstanceOf($decoderClass, $factory->getResourceDecoder('resource2'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowExceptionIfResourceDecoderNotRegistered()
    {
        $factory = new DecodersFactory();
        $factory->getResourceDecoder('resource1');
    }

    /**
     * @test
     * @expectedException  \LogicException
     * @expectedExceptionMessageRegExp #Resource decoder must implement .+ interface#
     */
    public function shouldThrowExceptionForInvalidResourceDecoder()
    {
        $decoder = new \stdClass();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $factory = new DecodersFactory();
        $factory->registerResourceDecoder('resource1', $closure)->getResourceDecoder('resource1');
    }

    /**
     * @test
     */
    public function shouldRegisterQueryParamsDecoders()
    {
        $decoder = $this->getMockBuilder(QueryParamsDecoderInterface::class)->getMock();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $decoderClass = get_class($decoder);

        $factory = new DecodersFactory();
        $factory
            ->registerQueryParamsDecoder('params1', $closure)
            ->registerQueryParamsDecoder('params2', $decoderClass);

        $this->assertSame($decoder, $factory->getQueryParamsDecoder('params1'));
        $this->assertInstanceOf($decoderClass, $factory->getQueryParamsDecoder('params2'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowExceptionIfQueryParamsDecoderNotRegistered()
    {
        $factory = new DecodersFactory();
        $factory->getQueryParamsDecoder('params1');
    }

    /**
     * @test
     * @expectedException  \LogicException
     * @expectedExceptionMessageRegExp #Query parameters decoder must implement .+ interface#
     */
    public function shouldThrowExceptionForInvalidQueryParamsDecoder()
    {
        $decoder = new \stdClass();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $factory = new DecodersFactory();
        $factory->registerQueryParamsDecoder('params1', $closure)->getQueryParamsDecoder('params1');
    }

    /**
     * @test
     */
    public function shouldRegisterDocumentDecoders()
    {
        $decoder = $this->getMockBuilder(DocumentDecoderInterface::class)->getMock();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $decoderClass = get_class($decoder);

        $factory = new DecodersFactory();
        $factory
            ->registerDocumentDecoder('doc1', $closure)
            ->registerDocumentDecoder('doc2', $decoderClass);

        $this->assertSame($decoder, $factory->getDocumentDecoder('doc1'));
        $this->assertInstanceOf($decoderClass, $factory->getDocumentDecoder('doc2'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowExceptionIfDocumentDecoderNotRegistered()
    {
        $factory = new DecodersFactory();
        $factory->getDocumentDecoder('doc1');
    }

    /**
     * @test
     * @expectedException  \LogicException
     * @expectedExceptionMessageRegExp #Document decoder must implement .+ interface#
     */
    public function shouldThrowExceptionForInvalidDocumentDecoder()
    {
        $decoder = new \stdClass();
        $closure = function () use ($decoder) {
            return $decoder;
        };

        $factory = new DecodersFactory();
        $factory->registerDocumentDecoder('doc1', $closure)->getDocumentDecoder('doc1');
    }
}
