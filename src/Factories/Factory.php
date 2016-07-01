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
use Reva2\JsonApi\Encoder\Encoder;
use Reva2\JsonApi\Http\Headers\HeadersChecker;

/**
 * JSON API factory
 *
 * @package Reva2\JsonApi\Factories
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Factory extends BaseFactory
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
}