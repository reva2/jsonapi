<?php

namespace Reva2\JsonApi\Contracts\Codec;

use Closure;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\AcceptMediaTypeInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;

interface CodecMatcherInterface
{
    /**
     * Register encoder.
     *
     * @param MediaTypeInterface $mediaType
     * @param Closure $encoderClosure
     *
     * @return void
     */
    public function registerEncoder(MediaTypeInterface $mediaType, Closure $encoderClosure): void;

    /**
     * Register decoder.
     *
     * @param MediaTypeInterface $mediaType
     * @param Closure $decoderClosure
     *
     * @return void
     */
    public function registerDecoder(MediaTypeInterface $mediaType, Closure $decoderClosure): void;

    /**
     * Get encoder.
     *
     * @return EncoderInterface|null
     */
    public function getEncoder(): ?EncoderInterface;

    /**
     * Set encoder.
     *
     * @param EncoderInterface|Closure $encoder
     *
     * @return void
     */
    public function setEncoder(mixed $encoder): void;

    /**
     * Get decoder.
     *
     * @return DecoderInterface|null
     */
    public function getDecoder(): ?DecoderInterface;

    /**
     * Set decoder.
     *
     * @param DecoderInterface|Closure $decoder
     * @return void
     */
    public function setDecoder(mixed $decoder): void;

    /**
     * Find the best encoder match for 'Accept' header.
     *
     * @param AcceptMediaTypeInterface[] $acceptMediaTypes
     *
     * @return void
     */
    public function matchEncoder(iterable $acceptMediaTypes): void;

    /**
     * Find the best decoder match for 'Content-Type' header.
     *
     * @param MediaTypeInterface $contentType
     *
     * @return void
     */
    public function matchDecoder(MediaTypeInterface $contentType): void;

    /**
     * Get media type from 'Accept' header that matched to one of the registered encoder media types.
     *
     * @return AcceptMediaTypeInterface|null
     */
    public function getEncoderHeaderMatchedType(): ?AcceptMediaTypeInterface;

    /**
     * Get media type that was registered for matched encoder.
     *
     * @return MediaTypeInterface|null
     */
    public function getEncoderRegisteredMatchedType(): ?MediaTypeInterface;

    /**
     * Get media type from 'Content-Type' header that matched to one of the registered decoder media types.
     *
     * @return MediaTypeInterface|null
     */
    public function getDecoderHeaderMatchedType(): ?MediaTypeInterface;

    /**
     * Get media type that was registered for matched decoder.
     *
     * @return MediaTypeInterface|null
     */
    public function getDecoderRegisteredMatchedType(): ?MediaTypeInterface;
}