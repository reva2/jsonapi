<?php
/*
 * This file is part of the jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Decoders;

use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\Context;
use Reva2\JsonApi\Tests\Fixtures\Resources\Dog;
use RuntimeException;

/**
 * ContextTest
 *
 * @author Sergey Revenko <dedsemen@gmail.com>
 * @package Reva2\JsonApi\Tests\Decoders
 */
class ContextTest extends TestCase
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new Context();
    }

    /**
     * @test
     */
    public function shouldRegisterResource()
    {
        $type = 'resource';

        $res = new Dog();
        $res->id = 'test';

        $this->assertEquals($this->context, $this->context->registerResource($type, $res->id, $res));
        $this->assertEquals($res, $this->context->getResource($type, $res->id));
    }

    /**
     * @test
     */
    public function shouldReturnNullIfResourceNotRegistered()
    {
        $this->assertNull($this->context->getResource('resource', 'test'));
    }

    /**
     * @test
     */
    public function shouldThrowIfResourceRegisteredAlready()
    {
        $type = 'resource';

        $res = new Dog();
        $res->id = 'test';

        $this->expectException(RuntimeException::class);

        $this->context->registerResource($type, $res->id, $res);
        $this->context->registerResource($type, $res->id, $res);
    }
}
