<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Http\Headers;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\AcceptHeaderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Http\Headers\HeadersChecker;

/**
 * Test for headers checker
 *
 * @package Reva2\JsonApi\Tests\Http\Headers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class HeadersCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCheckRequestHeaders()
    {
        $contentMediaTypes = [$this->getMockBuilder(MediaTypeInterface::class)->getMock()];

        $contentTypeHeader = $this->getMockBuilder(HeaderInterface::class)->getMock();
        $contentTypeHeader->expects($this->once())->method('getMediaTypes')->willReturn($contentMediaTypes);

        $acceptHeader = $this->getMockBuilder(AcceptHeaderInterface::class)->getMock();

        $headers = $this->getMockBuilder(HeaderParametersInterface::class)->getMock();
        $headers->expects($this->once())->method('getContentTypeHeader')->willReturn($contentTypeHeader);
        $headers->expects($this->once())->method('getAcceptHeader')->willReturn($acceptHeader);

        $decoderMediaType = $this->getMockBuilder(MediaTypeInterface::class)->getMock();
        $encoderMediaType = $this->getMockBuilder(AcceptMediaTypeInterface::class)->getMock();

        $matcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();
        $matcher->expects($this->once())->method('matchDecoder')->with($contentTypeHeader);
        $matcher->expects($this->once())->method('getDecoderHeaderMatchedType')->willReturn($decoderMediaType);
        $matcher->expects($this->once())->method('matchEncoder')->with($acceptHeader);
        $matcher->expects($this->once())->method('getEncoderHeaderMatchedType')->willReturn($encoderMediaType);

        $checker = new HeadersChecker($matcher);
        $checker->checkHeaders($headers);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnInvalidContentType()
    {
        $contentMediaTypes = [
            $this->getMockBuilder(MediaTypeInterface::class)->getMock(),
            $this->getMockBuilder(MediaTypeInterface::class)->getMock()
        ];

        $contentTypeHeader = $this->getMockBuilder(HeaderInterface::class)->getMock();
        $contentTypeHeader->expects($this->once())->method('getMediaTypes')->willReturn($contentMediaTypes);

        $headers = $this->getMockBuilder(HeaderParametersInterface::class)->getMock();
        $headers->expects($this->once())->method('getContentTypeHeader')->willReturn($contentTypeHeader);

        $matcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();

        try {
            $checker = new HeadersChecker($matcher);
            $checker->checkHeaders($headers);

            $this->fail("Checker should throw exception on invalid content type");
        } catch (JsonApiException $e) {
            $this->assertEquals(400, $e->getHttpCode());

            $errors = $e->getErrors();
            $this->assertEquals(HeadersChecker::INVALID_CONTENT_TYPE_ERROR, $errors[0]->getCode());
        }
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnUnsupportedContentType()
    {
        $contentMediaTypes = [$this->getMockBuilder(MediaTypeInterface::class)->getMock()];

        $contentTypeHeader = $this->getMockBuilder(HeaderInterface::class)->getMock();
        $contentTypeHeader->expects($this->once())->method('getMediaTypes')->willReturn($contentMediaTypes);

        $headers = $this->getMockBuilder(HeaderParametersInterface::class)->getMock();
        $headers->expects($this->once())->method('getContentTypeHeader')->willReturn($contentTypeHeader);

        $matcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();
        $matcher->expects($this->once())->method('getDecoderHeaderMatchedType')->willReturn(null);

        try {
            $checker = new HeadersChecker($matcher);
            $checker->checkHeaders($headers);

            $this->fail('Checker should throw exception on unsupported content type');
        } catch (JsonApiException $e) {
            $this->assertEquals(415, $e->getHttpCode());

            $this->assertEquals(HeadersChecker::UNSUPPORTED_CONTENT_TYPE_ERROR, $e->getErrors()[0]->getCode());
        }
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnUnsupportedAcceptType()
    {
        $contentMediaTypes = [$this->getMockBuilder(MediaTypeInterface::class)->getMock()];

        $contentTypeHeader = $this->getMockBuilder(HeaderInterface::class)->getMock();
        $contentTypeHeader->expects($this->once())->method('getMediaTypes')->willReturn($contentMediaTypes);

        $acceptHeader = $this->getMockBuilder(AcceptHeaderInterface::class)->getMock();

        $headers = $this->getMockBuilder(HeaderParametersInterface::class)->getMock();
        $headers->expects($this->once())->method('getContentTypeHeader')->willReturn($contentTypeHeader);
        $headers->expects($this->once())->method('getAcceptHeader')->willReturn($acceptHeader);

        $decoderMediaType = $this->getMockBuilder(MediaTypeInterface::class)->getMock();

        $matcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();
        $matcher->expects($this->once())->method('getDecoderHeaderMatchedType')->willReturn($decoderMediaType);
        $matcher->expects($this->once())->method('getEncoderHeaderMatchedType')->willReturn(null);

        try {
            $checker = new HeadersChecker($matcher);
            $checker->checkHeaders($headers);

            $this->fail('Checker should throw exception on unsupported response media type');
        } catch (JsonApiException $e) {
            $this->assertEquals(415, $e->getHttpCode());
            $this->assertEquals(HeadersChecker::UNSUPPORTED_ACCEPT_ERROR, $e->getErrors()[0]->getCode());
        }
    }
}
