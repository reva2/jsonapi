<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders;

use Neomerx\JsonApi\Exceptions\JsonApiException;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\DataParser;
use Reva2\JsonApi\Decoders\RequestDecoder;

/**
 * Tests for request decoder
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class RequestDecoderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDecodeRequest()
    {
        $request = new \stdClass();
        $request->data = new \stdClass();
        $request->data->id = '1';
        $request->data->type = 'resource1';

        $content = json_encode($request);

        $parser = $this
            ->getMockBuilder(DataParser::class)
            ->setMethods(['parseDocument'])
            ->disableOriginalConstructor()
            ->getMock();

        $parser
            ->expects($this->once())
            ->method('parseDocument')
            ->with($request, 'resource1')
            ->willReturn($request);

        $decoder = new RequestDecoder($parser);

        $this->assertInstanceOf(\stdClass::class, $decoder->decode($content));

        $decoder->setContentType('resource1');
        $result = $decoder->decode($content);

        $this->assertEquals($request, $result);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnBadContent()
    {
        try {
            $content = "invalid json";
            $parser = $this->getMockBuilder(DataParser::class)->disableOriginalConstructor()->getMock();

            $decoder = new RequestDecoder($parser);
            $decoder->decode($content);

            $this->fail("Request decoder should throw exception on bad content");
        } catch (JsonApiException $e) {
            $this->assertEquals(400, $e->getHttpCode());

            $errors = $e->getErrors();
            $error = $errors[0];

            $this->assertEquals(400, $error->getStatus());
            $this->assertEquals('Unable to parse JSON data', $error->getTitle());
        }
    }
}
