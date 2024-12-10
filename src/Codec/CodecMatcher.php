<?php

namespace Reva2\JsonApi\Codec;

use Closure;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Reva2\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;

/**
 * @package Neomerx\JsonApi
 */
class CodecMatcher implements CodecMatcherInterface
{
    /**
     * @var array Supported JSON API media types with extensions and their combinations for responses.
     *
     * Servers may support multiple media types at any endpoint. For example, a server may choose to
     * support text/html in order to simplify viewing content via a web browser.
     *
     * JSON API specifications says that input requests might ask output in combination of formats (e.g. "ext1,ext2")
     * which means it should be formatted according to the extensions "ext1" and "ext2".
     *
     * Note: Since extensions can contradict one another or have interactions that can be resolved in many
     * equally plausible ways, it is the responsibility of the server to decide which extensions are compatible,
     * and it is the responsibility of the designer of each implementation of this specification to describe
     * extension interoperability rules which are applicable to that implementation.
     */
    private array $outputMediaTypes = [];

    /**
     * @var array Supported JSON API extensions and their combinations for requests.
     * Similar to supported media types for responses.
     */
    private array $inputMediaTypes = [];

    /**
     * @var EncoderInterface|Closure|null
     */
    private mixed $foundEncoder = null;

    /**
     * @var DecoderInterface|Closure|null
     */
    private mixed $foundDecoder = null;

    /**
     * @var AcceptMediaTypeInterface|null
     */
    private ?AcceptMediaTypeInterface $encoderHeaderMatchedType = null;

    /**
     * @var MediaTypeInterface|null
     */
    private ?MediaTypeInterface $encoderRegisteredMatchedType = null;

    /**
     * @var MediaTypeInterface|null
     */
    private ?MediaTypeInterface $decoderHeaderMatchedType = null;

    /**
     * @var MediaTypeInterface|null
     */
    private ?MediaTypeInterface $decoderRegisteredMatchedType = null;

    /**
     * @inheritdoc
     */
    public function registerEncoder(MediaTypeInterface $mediaType, Closure $encoderClosure): void
    {
        $this->outputMediaTypes[] = [$mediaType, $encoderClosure];
    }

    /**
     * @inheritdoc
     */
    public function registerDecoder(MediaTypeInterface $mediaType, Closure $decoderClosure): void
    {
        $this->inputMediaTypes[] = [$mediaType, $decoderClosure];
    }

    /**
     * @inheritdoc
     */
    public function getEncoder(): ?EncoderInterface
    {
        if ($this->foundEncoder instanceof Closure) {
            $closure = $this->foundEncoder;
            $this->foundEncoder = $closure();
        }

        return $this->foundEncoder;
    }

    /**
     * @inheritdoc
     */
    public function setEncoder(mixed $encoder): void
    {
        $this->foundEncoder = $encoder;
    }

    /**
     * @inheritdoc
     */
    public function getDecoder(): ?DecoderInterface
    {
        if ($this->foundDecoder instanceof Closure) {
            $closure = $this->foundDecoder;
            $this->foundDecoder = $closure();
        }

        return $this->foundDecoder;
    }

    /**
     * @inheritdoc
     */
    public function setDecoder(mixed $decoder): void
    {
        $this->foundDecoder = $decoder;
    }

    /**
     * @param AcceptMediaTypeInterface[] $acceptMediaTypes
     * @return void
     */
    public function matchEncoder(iterable $acceptMediaTypes): void
    {
        foreach ($acceptMediaTypes as $headerMediaType) {
            /* @var MediaTypeInterface $headerMediaType */
            // if quality factor 'q' === 0 it means this type is not acceptable (RFC 2616 #3.9)
            if ($headerMediaType->getQuality() > 0) {
                /** @var MediaTypeInterface $registeredType */
                foreach ($this->outputMediaTypes as list($registeredType, $closure)) {
                    if ($registeredType->matchesTo($headerMediaType) === true) {
                        $this->encoderHeaderMatchedType = $headerMediaType;
                        $this->encoderRegisteredMatchedType = $registeredType;
                        $this->foundEncoder = $closure;

                        return;
                    }
                }
            }
        }

        $this->encoderHeaderMatchedType = null;
        $this->encoderRegisteredMatchedType = null;
        $this->foundEncoder = null;
    }

    /**
     * Find decoder with media type equal to media type in 'Content-Type' header.
     *
     * @param MediaTypeInterface $contentType
     *
     * @return void
     */
    public function matchDecoder(MediaTypeInterface $contentType): void
    {
        foreach ($this->inputMediaTypes as list($registeredType, $closure)) {
            if ($registeredType->equalsTo($contentType) === true) {
                $this->decoderHeaderMatchedType = $contentType;
                $this->decoderRegisteredMatchedType = $registeredType;
                $this->foundDecoder = $closure;

                return;
            }
        }

        $this->decoderHeaderMatchedType = null;
        $this->decoderRegisteredMatchedType = null;
        $this->foundDecoder = null;
    }

    /**
     * @inheritdoc
     */
    public function getEncoderHeaderMatchedType(): ?AcceptMediaTypeInterface
    {
        return $this->encoderHeaderMatchedType;
    }

    /**
     * @inheritdoc
     */
    public function getEncoderRegisteredMatchedType(): ?MediaTypeInterface
    {
        return $this->encoderRegisteredMatchedType;
    }

    /**
     * @inheritdoc
     */
    public function getDecoderHeaderMatchedType(): ?MediaTypeInterface
    {
        return $this->decoderHeaderMatchedType;
    }

    /**
     * @inheritdoc
     */
    public function getDecoderRegisteredMatchedType(): ?MediaTypeInterface
    {
        return $this->decoderRegisteredMatchedType;
    }
}