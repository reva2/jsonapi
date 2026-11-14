<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Http\Headers;

use Neomerx\JsonApi\Contracts\Http\Headers\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Codec\CodecMatcherInterface;

/**
 * Headers checker
 *
 * @package Reva2\JsonApi\Http\Headers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class HeadersChecker
{
    const INVALID_CONTENT_TYPE_ERROR = 'ebb239c5-221d-42d2-a2f7-ca4bbc3d476f';
    const UNSUPPORTED_CONTENT_TYPE_ERROR = 'aa389b54-1b33-4b0f-a5f4-1d680ca5f6d3';
    const UNSUPPORTED_ACCEPT_ERROR = '6df3b139-d9ef-4576-8d0a-c0ce2e803827';
    const INVALID_ACCEPT_ERROR = '3b0c8a1e-5f0e-4f6b-9c63-0c1c4d7d2a55';

    /**
     * @var CodecMatcherInterface
     */
    protected CodecMatcherInterface $matcher;

    /**
     * Constructor
     *
     * @param CodecMatcherInterface $matcher
     */
    public function __construct(CodecMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @param MediaTypeInterface|null $contentType Content type (null - request has no body, skip check)
     * @param AcceptMediaTypeInterface[] $acceptedContentTypes
     * @return void
     */
    public function checkHeaders(?MediaTypeInterface $contentType, iterable $acceptedContentTypes): void
    {
        if (null !== $contentType) {
            $this->checkContentTypeHeader($contentType);
        }

        $this->checkAcceptHeader($acceptedContentTypes);
    }

    /**
     * Create exception for missing or malformed content type header
     *
     * @param string $title
     * @return JsonApiException
     */
    public static function createInvalidContentTypeException(string $title = 'Invalid content type'): JsonApiException
    {
        return new JsonApiException(
            self::createApiError(
                JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                self::INVALID_CONTENT_TYPE_ERROR,
                $title
            ),
            JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE
        );
    }

    /**
     * Create exception for unsupported content type
     *
     * @param string $title
     * @return JsonApiException
     */
    public static function createUnsupportedContentTypeException(
        string $title = 'Unsupported content type'
    ): JsonApiException {
        return new JsonApiException(
            self::createApiError(
                JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                self::UNSUPPORTED_CONTENT_TYPE_ERROR,
                $title
            ),
            JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE
        );
    }

    /**
     * Create exception for malformed accept header
     *
     * @param string $title
     * @return JsonApiException
     */
    public static function createInvalidAcceptException(string $title = 'Invalid accept header'): JsonApiException
    {
        return new JsonApiException(
            self::createApiError(
                JsonApiException::HTTP_CODE_NOT_ACCEPTABLE,
                self::INVALID_ACCEPT_ERROR,
                $title
            ),
            JsonApiException::HTTP_CODE_NOT_ACCEPTABLE
        );
    }

    /**
     * Check content type header
     *
     * @param MediaTypeInterface $contentType
     */
    private function checkContentTypeHeader(MediaTypeInterface $contentType)
    {
        $this->matcher->matchDecoder($contentType);
        if (null === $this->matcher->getDecoderHeaderMatchedType()) {
            throw self::createUnsupportedContentTypeException();
        }
    }

    /**
     * Check accept header
     *
     * @param AcceptMediaTypeInterface[] $acceptedMediaTypes
     */
    private function checkAcceptHeader(iterable $acceptedMediaTypes): void
    {
        $this->matcher->matchEncoder($acceptedMediaTypes);

        if (null === $this->matcher->getEncoderHeaderMatchedType()) {
            throw new JsonApiException(
                $this->createApiError(
                    JsonApiException::HTTP_CODE_NOT_ACCEPTABLE,
                    self::UNSUPPORTED_ACCEPT_ERROR,
                    'Not acceptable'
                ),
                JsonApiException::HTTP_CODE_NOT_ACCEPTABLE
            );
        }
    }

    /**
     * Create JSON API error
     * @param int $status
     * @param string $code
     * @param string $title
     * @return Error
     */
    private static function createApiError($status, $code, $title)
    {
        return new Error(idx: rand(), status:  $status, code: $code, title: $title);
    }
}
