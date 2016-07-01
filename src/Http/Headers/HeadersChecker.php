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
                new Error(
                    rand(),
                    null,
                    JsonApiException::HTTP_CODE_BAD_REQUEST,
                    'invalid-content-type',
                    "Invalid content type"
                ),
                JsonApiException::HTTP_CODE_BAD_REQUEST
            );
        }

        $this->matcher->matchDecoder($header);
        if (null === $this->matcher->getDecoderHeaderMatchedType()) {
            throw new JsonApiException(
                new Error(
                    rand(),
                    null,
                    JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                    'unsupported-content-type',
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
                new Error(
                    rand(),
                    null,
                    JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE,
                    'unsupported-media-type',
                    'Unsupported media type'
                ),
                JsonApiException::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE
            );
        }
    }
}