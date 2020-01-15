<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Services;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Services\JsonApiRegistry;
use RuntimeException;

/**
 * Test for JSON API decoders/encoders registry
 *
 * @package Reva2\JsonApi\Tests\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class JsonApiRegistryTest extends TestCase
{
    /**
     * @var JsonApiRegistry
     */
    protected $registry;

    /**
     * @test
     */
    public function shouldRegisterDecoder()
    {
        $decoder = $this->getMockBuilder(DecoderInterface::class)->getMock();
        $factory = function () use ($decoder) {
            return $decoder;
        };

        $this->registry->registerDecoder('test', $factory);

        $this->assertSame($factory, $this->registry->getDecoder('test'));
    }

    /**
     * @test
     */
    public function shouldThrowIfDecoderNotRegistered()
    {
        $this->expectException(RuntimeException::class);

        $this->registry->getDecoder('unknown');
    }

    /**
     * @test
     */
    public function shouldRegisterEncoder()
    {
        $encoder = $this->getMockBuilder(EncoderInterface::class)->getMock();
        $factory = function () use ($encoder) {
            return $encoder;
        };

        $this->registry->registerEncoder('test', $factory);

        $this->assertSame($factory, $this->registry->getEncoder('test'));
    }

    /**
     * @test
     */
    public function shouldThrowIfEncoderNotRegistered()
    {
        $this->expectException(RuntimeException::class);

        $this->registry->getEncoder('unknown');
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->registry = new JsonApiRegistry();
    }
}
