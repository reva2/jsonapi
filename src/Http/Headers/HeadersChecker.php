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

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\AcceptHeaderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeadersCheckerInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Headers checker
 *
 * @package Reva2\JsonApi\Http\Headers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class HeadersChecker implements HeadersCheckerInterface
{
    const INVALID_CONTENT_TYPE_ERROR = 'ebb239c5-221d-42d2-a2f7-ca4bbc3d476f';
    const UNSUPPORTED_CONTENT_TYPE_ERROR = 'aa389b54-1b33-4b0f-a5f4-1d680ca5f6d3';
    const UNSUPPORTED_ACCEPT_ERROR = '6df3b139-d9ef-4576-8d0a-c0ce2e803827';
    /**
     * @var CodecMatcherInterface
     */
    protected $matcher;

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
     * @inheritdoc
     */
    public function checkHeaders(HeaderParametersInterface $parameters)
    {

        $this->checkContentTypeHeader($parameters->getContentTypeHeader());
        $this->checkAcceptHeader($parameters->getAcceptHeader());
    }

    /**
     * Check content type header
     *
     * @param HeaderInterface $header
     */
    private function checkContentTypeHeader(HeaderInterface $header)
    {
        if (count($header->getMediaTypes()) > 1) {
            throw new JsonApiException(
                $this->createApiError(
                    JsonApiException::HTTP_CODE_BAD_REQUEST,
                    self::INVALID_CONTENT_TYPE_ERROR,
                    "Invalid content type"
                ),
                JsonApiException::HTTP_CODE_BAD_REQUEST
            );
        }

        $this->matcher->matchDecoder($header);
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
     * @param AcceptHeaderInterface $header
     * @return Error
     */
    private function checkAcceptHeader(AcceptHeaderInterface $header)
    {
        $this->matcher->matchEncoder($header);

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
        return new Error(rand(), null, $status, $code, $title);
    }
}
