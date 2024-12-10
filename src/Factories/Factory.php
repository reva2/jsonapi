<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Factories;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersParserInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Factories\Factory as BaseFactory;
use Neomerx\JsonApi\Http\Headers\HeaderParametersParser;
use Reva2\JsonApi\Codec\CodecMatcher;
use Reva2\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Encoder\Encoder;
use Reva2\JsonApi\Http\Headers\HeadersChecker;
use Reva2\JsonApi\Http\Query\QueryParametersParser;
use Reva2\JsonApi\Http\Request;
use Reva2\JsonApi\Schema\Container;
use Reva2\JsonApi\Services\Environment;

/**
 * JSON API factory
 *
 * @package Reva2\JsonApi\Factories
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Factory extends BaseFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createEncoder(SchemaContainerInterface $container): EncoderInterface
    {
        return new Encoder($this, $container);
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @return HeadersChecker
     */
    public function createHeadersChecker(CodecMatcherInterface $codecMatcher): HeadersChecker
    {
        return new HeadersChecker($codecMatcher);
    }

    /**
     * @inheritdoc
     */
    public function createEnvironment(array $config = null): EnvironmentInterface
    {
        return new Environment($config);
    }

    /**
     * @inheritdoc
     */
    public function createRequest(EnvironmentInterface $environment): RequestInterface
    {
        return new Request($environment);
    }

    /**
     * @inheritdoc
     */
    public function createQueryParametersParser()
    {
        return new QueryParametersParser();
    }

    /**
     * @inheritdoc
     */
    public function createContainer(array $providers = [])
    {
        return new Container($this, $providers);
    }

    public function createCodecMatcher(): CodecMatcherInterface
    {
        return new CodecMatcher();
    }

    public function createHeaderParametersParser(): HeaderParametersParserInterface
    {
        return new HeaderParametersParser($this);
    }
}
