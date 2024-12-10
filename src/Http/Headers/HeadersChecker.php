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
     * @param MediaTypeInterface $contentType
     * @param AcceptMediaTypeInterface[] $acceptedContentTypes
     * @return void
     */
    public function checkHeaders(MediaTypeInterface $contentType, iterable $acceptedContentTypes): void
    {
        $this->checkContentTypeHeader($contentType);
        $this->checkAcceptHeader($acceptedContentTypes);
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
            throw new JsonApiException(
                $this->createApiError(
                    JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                    self::UNSUPPORTED_CONTENT_TYPE_ERROR,
                    'Unsupported content type'
                ),
                JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE
            );
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
                    JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                    self::UNSUPPORTED_ACCEPT_ERROR,
                    'Unsupported media type'
                ),
                JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE
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
    private function createApiError($status, $code, $title)
    {
        return new Error(idx: rand(), status:  $status, code: $code, title: $title);
    }
}
