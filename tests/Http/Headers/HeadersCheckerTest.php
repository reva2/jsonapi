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

use Neomerx\JsonApi\Contracts\Http\Headers\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Reva2\JsonApi\Http\Headers\HeadersChecker;

/**
 * Test for headers checker
 *
 * @package Reva2\JsonApi\Tests\Http\Headers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class HeadersCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCheckRequestHeaders()
    {
        $contentType = $this->createMock(MediaTypeInterface::class);
        $accept = [$this->createMock(AcceptMediaTypeInterface::class)];

        $matcher = $this->createMock(CodecMatcherInterface::class);
        $matcher->expects($this->once())->method('matchDecoder')->with($contentType);
        $matcher->expects($this->once())->method('getDecoderHeaderMatchedType')
            ->willReturn($this->createMock(MediaTypeInterface::class));
        $matcher->expects($this->once())->method('matchEncoder')->with($accept);
        $matcher->expects($this->once())->method('getEncoderHeaderMatchedType')
            ->willReturn($this->createMock(AcceptMediaTypeInterface::class));

        (new HeadersChecker($matcher))->checkHeaders($contentType, $accept);
    }

    /**
     * @test
     */
    public function shouldSkipContentTypeCheckIfItIsNull()
    {
        $accept = [$this->createMock(AcceptMediaTypeInterface::class)];

        $matcher = $this->createMock(CodecMatcherInterface::class);
        $matcher->expects($this->never())->method('matchDecoder');
        $matcher->expects($this->never())->method('getDecoderHeaderMatchedType');
        $matcher->expects($this->once())->method('matchEncoder')->with($accept);
        $matcher->expects($this->once())->method('getEncoderHeaderMatchedType')
            ->willReturn($this->createMock(AcceptMediaTypeInterface::class));

        (new HeadersChecker($matcher))->checkHeaders(null, $accept);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnUnsupportedContentType()
    {
        $matcher = $this->createMock(CodecMatcherInterface::class);
        $matcher->expects($this->once())->method('getDecoderHeaderMatchedType')->willReturn(null);

        try {
            (new HeadersChecker($matcher))->checkHeaders($this->createMock(MediaTypeInterface::class), []);

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
        $matcher = $this->createMock(CodecMatcherInterface::class);
        $matcher->expects($this->once())->method('getEncoderHeaderMatchedType')->willReturn(null);

        try {
            (new HeadersChecker($matcher))->checkHeaders(null, []);

            $this->fail('Checker should throw exception on unsupported response media type');
        } catch (JsonApiException $e) {
            $this->assertEquals(406, $e->getHttpCode());
            $this->assertEquals(HeadersChecker::UNSUPPORTED_ACCEPT_ERROR, $e->getErrors()[0]->getCode());
        }
    }

    /**
     * @test
     */
    public function shouldCreateUnsupportedContentTypeException()
    {
        $e = HeadersChecker::createUnsupportedContentTypeException();

        $this->assertEquals(415, $e->getHttpCode());
        $this->assertEquals(HeadersChecker::UNSUPPORTED_CONTENT_TYPE_ERROR, $e->getErrors()[0]->getCode());
        $this->assertEquals('Unsupported content type', $e->getErrors()[0]->getTitle());
    }
}
