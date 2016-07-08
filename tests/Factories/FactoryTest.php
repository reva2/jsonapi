<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Factories;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Reva2\JsonApi\Encoder\Encoder;
use Reva2\JsonApi\Factories\Factory;
use Reva2\JsonApi\Http\Headers\HeadersChecker;

/**
 * Tests for JSON API factory
 *
 * @package Reva2\JsonApi\Tests\Factories
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldCreateCustomEncoder()
    {
        $factory = new Factory();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $encoder = $factory->createEncoder($container);

        $this->assertInstanceOf(Encoder::class, $encoder);
    }

    /**
     * @test
     */
    public function shouldCreateCustomHeadersChecker()
    {
        $factory = new Factory();
        $matcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();

        $checker = $factory->createHeadersChecker($matcher);

        $this->assertInstanceOf(HeadersChecker::class, $checker);
    }
}
