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

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Factories\Factory as BaseFactory;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Encoder\Encoder;
use Reva2\JsonApi\Http\Headers\HeadersChecker;
use Reva2\JsonApi\Http\Query\QueryParametersParser;
use Reva2\JsonApi\Http\Request;
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
    public function createEncoder(ContainerInterface $container, EncoderOptions $encoderOptions = null)
    {
        $encoder = new Encoder($this, $container, $encoderOptions);
        $encoder->setLogger($this->logger);

        return $encoder;
    }

    /**
     * @inheritdoc
     */
    public function createHeadersChecker(CodecMatcherInterface $codecMatcher)
    {
        return new HeadersChecker($codecMatcher);
    }

    /**
     * @inheritdoc
     */
    public function createEnvironment(array $config = null)
    {
        return new Environment($config);
    }

    /**
     * @inheritdoc
     */
    public function createRequest(EnvironmentInterface $environment)
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
}
