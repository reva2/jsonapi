<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Services;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Reva2\JsonApi\Contracts\Decoders\QueryParamsDecoderInterface;

/**
 * JSON API request environment
 *
 * @package Reva2\JsonApi\Contracts\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface EnvironmentInterface
{
    /**
     * Returns response encoder
     *
     * @return EncoderInterface
     */
    public function getEncoder();

    /**
     * Returns encoder media type
     *
     * @return MediaTypeInterface
     */
    public function getEncoderMediaType();

    /**
     * Returns request decoder
     *
     * @return DecoderInterface
     */
    public function getDecoder();

    /**
     * Returns decoder media type
     *
     * @return MediaTypeInterface
     */
    public function getDecoderMediaType();

    /**
     * Encoder schemas container
     *
     * @return ContainerInterface
     */
    public function getSchemaContainer();

    /**
     * Returns prefix for URLs
     *
     * @return string
     */
    public function getUrlPrefix();

    /**
     * Returns decoder for request query parameters
     *
     * @return QueryParamsDecoderInterface
     */
    public function getQueryParamsDecoder();
}
