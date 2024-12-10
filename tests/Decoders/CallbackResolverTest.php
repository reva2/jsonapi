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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\CallbackResolver;

/**
 * CallbackResolverTest
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class CallbackResolverTest extends TestCase
{
    /**
     * @var CallbackResolver
     */
    protected $resolver;

    /**
     * @test
     */
    public function shouldResolveStaticMethods()
    {
        $class = 'Reva2\JsonApi\Tests\Fixtures\Converters\StoreConverter';
        $method = 'convert';
        $name = $class . '::' . $method;

        $callback = $this->resolver->resolveCallback($name);

        $this->assertIsArray($callback);
        $this->assertCount(2, $callback);
        $this->assertSame($class, $callback[0]);
        $this->assertSame($method, $callback[1]);
    }

    /**
     * @test
     */
    public function shouldResolveSimpleFunctions()
    {
        $name = 'intval';

        $callback = $this->resolver->resolveCallback($name);

        $this->assertSame($name, $callback);
    }

    /**
     * @test
     */
    public function shouldThrowIfNameIsNotCallable()
    {
        $name = 'Reva2\JsonApi\Tests\Fixtures\Converters\InvalidConverter::convert';

        $this->expectException(InvalidArgumentException::class);

        $this->resolver->resolveCallback($name);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->resolver = new CallbackResolver();
    }
}
