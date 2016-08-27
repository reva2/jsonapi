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

use Reva2\JsonApi\Decoders\CallbackResolver;

/**
 * CallbackResolverTest
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class CallbackResolverTest extends \PHPUnit_Framework_TestCase
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

        $this->assertInternalType('array', $callback);
        $this->assertCount(2, $callback);
        $this->assertSame($class, $callback[0]);
        $this->assertSame($method, $callback[1]);
    }

    /**
     * @test
     */
    public function shouldResolveSimpleFuncations()
    {
        $name = 'intval';

        $callback = $this->resolver->resolveCallback($name);

        $this->assertSame($name, $callback);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowIfNameIsNotCallable()
    {
        $name = 'Reva2\JsonApi\Tests\Fixtures\Converters\InvalidConverter::convert';

        $this->resolver->resolveCallback($name);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->resolver = new CallbackResolver();
    }
}
