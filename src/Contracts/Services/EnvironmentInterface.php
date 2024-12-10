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

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;

/**
 * JSON API request environment
 *
 * @package Reva2\JsonApi\Contracts\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface EnvironmentInterface
{
    /**
     * Returns type of expected query parameters
     *
     * @return string|null
     */
    public function getQueryType();

    /**
     * Returns type of excepted body content
     *
     * @return string|null
     */
    public function getBodyType();

    /**
     * Returns codec matcher configuration
     *
     * @return array
     */
    public function getMatcherConfiguration();

    /**
     * Returns URLs prefix
     *
     * @return string
     */
    public function getUrlPrefix();

    /**
     * Returns list of serialization groups
     *
     * @return string[]
     */
    public function getSerializationGroups();

    /**
     * Returns list of validation groups that should be checked
     *
     * @return string[]|null
     */
    public function getValidationGroups();

    /**
     * Sets request decoders
     *
     * @param DecoderInterface $decoder
     * @return $this
     */
    public function setDecoder(DecoderInterface $decoder);

    /**
     * Returns request decoder
     *
     * @return DecoderInterface|null
     */
    public function getDecoder();

    /**
     * Sets response encoder
     *
     * @param EncoderInterface $encoder
     * @return $this
     */
    public function setEncoder(EncoderInterface $encoder);

    /**
     * Returns response encoder
     *
     * @return EncoderInterface|null
     */
    public function getEncoder();

    /**
     * Sets response encoder media type
     *
     * @param MediaTypeInterface $mediaType
     * @return $this
     */
    public function setEncoderMediaType(MediaTypeInterface $mediaType);

    /**
     * Returns response encoder media type
     *
     * @return MediaTypeInterface|null
     */
    public function getEncoderMediaType(): ?MediaTypeInterface;
}
