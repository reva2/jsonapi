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
use Reva2\JsonApi\Contracts\Decoders\Data\DocumentInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\ResourceInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\DecodersFactoryInterface;
use Reva2\JsonApi\Contracts\Decoders\DocumentDecoderInterface;
use Reva2\JsonApi\Contracts\Decoders\ResourceDecoderInterface;
use Reva2\JsonApi\Decoders\DataParser;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Test for data parser
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DataParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Value expected to be a string, but .+ given#
     */
    public function shouldParseStrings()
    {
        $data = new \stdClass();
        $data->key = 'value';
        $data->invalid = new DateTime();

        $parser = $this->getParser();

        $this->assertSame('value', $parser->parseString($data, 'key'));
        $this->assertNull($parser->parseString($data, 'unknown'));

        // should throw exception on invalid value
        $parser->parseString($data, 'invalid');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Value expected to be int, but .+ given#
     */
    public function shouldParseIntegers()
    {
        $data = new \stdClass();
        $data->key = 10;
        $data->another = '10';
        $data->invalid = 'invalid';

        $parser = $this->getParser();

        $this->assertSame(10, $parser->parseInt($data, 'key'));
        $this->assertSame(10, $parser->parseInt($data, 'another'));
        $this->assertNull($parser->parseInt($data, 'unknown'));

        // should throw exception on invalid value
        $parser->parseInt($data, 'invalid');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #alue expected to be float, but .+ given#
     */
    public function shouldParseFloats()
    {
        $data = new \stdClass();
        $data->key = 10.5;
        $data->another = '10.5';
        $data->invalid = 'invalid';

        $parser = $this->getParser();

        $this->assertSame(10.5, $parser->parseFloat($data, 'key'));
        $this->assertSame(10.5, $parser->parseFloat($data, 'another'));
        $this->assertNull($parser->parseFloat($data, 'unknown'));

        // should throw exception on invalid value
        $parser->parseFloat($data, 'invalid');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Value expected to be a boolean, but .+ given#
     */
    public function shouldParseBooleans()
    {
        $data = new \stdClass();
        $data->bool = true;
        $data->yes = 'yes';
        $data->no = 'no';
        $data->trueString = 'true';
        $data->falseString = 'false';
        $data->y = 'y';
        $data->n = 'n';
        $data->on = 'on';
        $data->off = 'off';
        $data->enabled = 'enabled';
        $data->disabled = 'disabled';
        $data->number = 1;
        $data->zero = 0;
        $data->invalid = new \DateTime();

        $parser = $this->getParser();

        $this->assertTrue($parser->parseBool($data, 'bool'));
        $this->assertTrue($parser->parseBool($data, 'yes'));
        $this->assertFalse($parser->parseBool($data, 'no'));
        $this->assertTrue($parser->parseBool($data, 'trueString'));
        $this->assertFalse($parser->parseBool($data, 'falseString'));
        $this->assertTrue($parser->parseBool($data, 'y'));
        $this->assertFalse($parser->parseBool($data, 'n'));
        $this->assertTrue($parser->parseBool($data, 'on'));
        $this->assertFalse($parser->parseBool($data, 'off'));
        $this->assertTrue($parser->parseBool($data, 'enabled'));
        $this->assertFalse($parser->parseBool($data, 'disabled'));
        $this->assertTrue($parser->parseBool($data, 'number'));
        $this->assertFalse($parser->parseBool($data, 'zero'));
        $this->assertNull($parser->parseBool($data, 'unknown'));

        // should throw exception on invalid value
        $parser->parseBool($data, 'invalid');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value expected to be a date/time string in 'Y-m-d' format
     */
    public function shouldParseDateTimes()
    {
        $data = new \stdClass();
        $data->date = '2016-07-01';
        $data->datetime = '2016-07-01 18:29:15';
        $data->time = '18:29:15';
        $data->invalid = 'invalid';

        $parser = $this->getParser();

        $date = $parser->parseDateTime($data, 'date');
        $this->assertInstanceOf(\DateTimeImmutable::class, $date);
        $this->assertEquals($data->date, $date->format('Y-m-d'));

        $datetime = $parser->parseDateTime($data, 'datetime', 'Y-m-d H:i:s');
        $this->assertInstanceOf(\DateTimeImmutable::class, $datetime);
        $this->assertEquals($data->datetime, $datetime->format('Y-m-d H:i:s'));

        $time = $parser->parseDateTime($data, 'time', 'H:i:s');
        $this->assertInstanceOf(\DateTimeImmutable::class, $time);
        $this->assertEquals($data->time, $time->format('H:i:s'));

        $this->assertNull($parser->parseDateTime($data, 'unknown'));

        // should throw exception on invalid value
        $parser->parseDateTime($data, 'invalid');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Value expected to be an array, but .+ given#
     */
    public function shouldParseArrays()
    {
        $data = new \stdClass();
        $data->strings = ['first', 'second', 'third'];
        $data->integers = [1, 2, 3];
        $data->floats = [0.5, 1.5, 2.5];
        $data->booleans = [false, false, true];
        $data->dates = ['2016-07-01'];
        $data->datetimes = ['2016-07-01 18:29:15'];
        $data->times = ['18:29:15'];
        $data->map = ['first' => 'one', 'second' => 'two', 'third' => 'three'];
        $data->invalid = 1;

        $parser = $this->getParser();

        $this->assertSame($data->strings, $parser->parseArray($data, 'strings', 'string'));
        $this->assertSame($data->integers, $parser->parseArray($data, 'integers', 'int'));
        $this->assertSame($data->integers, $parser->parseArray($data, 'integers', 'integer'));
        $this->assertSame($data->floats, $parser->parseArray($data, 'floats', 'float'));
        $this->assertSame($data->floats, $parser->parseArray($data, 'floats', 'double'));
        $this->assertSame($data->booleans, $parser->parseArray($data, 'booleans', 'bool'));
        $this->assertSame($data->booleans, $parser->parseArray($data, 'booleans', 'boolean'));

        $dates = $parser->parseArray($data, 'dates', 'date');
        $this->assertInternalType('array', $dates);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dates[0]);

        $datetimes = $parser->parseArray($data, 'datetimes', 'datetime');
        $this->assertInternalType('array', $datetimes);
        $this->assertInstanceOf(\DateTimeImmutable::class, $datetimes[0]);

        $times = $parser->parseArray($data, 'times', 'time');
        $this->assertInternalType('array', $times);
        $this->assertInstanceOf(\DateTimeImmutable::class, $times[0]);

        $this->assertSame($data->map, $parser->parseArray($data, 'map', 'string'));

        $custom = $parser->parseArray($data, 'strings', function ($data, $path, DataParserInterface $parser) {
            return substr($parser->getValue($data, $path), 0, 1);
        });
        $this->assertSame(['f', 's', 't'], $custom);

        $this->assertNull($parser->parseArray($data, 'uknown', 'string'));

        // should throw exception on invalid value
        $parser->parseArray($data, 'invalid', 'string');
    }

    /**
     * @test
     */
    public function shouldParseResources()
    {
        $data = new \stdClass();
        $data->resource = null;

        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();

        $decoder = $this->getMockBuilder(ResourceDecoderInterface::class)->getMock();
        $decoder
            ->expects($this->once())
            ->method('decode')
            ->withAnyParameters()
            ->willReturn($resource);

        $factory = $this->getMockBuilder(DecodersFactoryInterface::class)->getMock();
        $factory
            ->expects($this->once())
            ->method('getResourceDecoder')
            ->with('resource1')
            ->willReturn($decoder);

        $parser = new DataParser($factory);

        $this->assertSame($resource, $parser->parseResource($data, 'resource', 'resource1'));
    }

    /**
     * @test
     */
    public function shouldParseDocuments()
    {
        $data = new \stdClass();

        $doc = $this->getMockBuilder(DocumentInterface::class)->getMock();

        $decoder = $this->getMockBuilder(DocumentDecoderInterface::class)->getMock();
        $decoder
            ->expects($this->once())
            ->method('decode')
            ->withAnyParameters()
            ->willReturn($doc);

        $factory = $this->getMockBuilder(DecodersFactoryInterface::class)->getMock();
        $factory
            ->expects($this->once())
            ->method('getDocumentDecoder')
            ->with('document1')
            ->willReturn($decoder);

        $parser = new DataParser($factory);
        $this->assertSame($doc, $parser->parseDocument($data, 'document1'));
    }

    /**
     * @test
     */
    public function shouldThrowJsonApiExceptionOnError()
    {
        $data = new \stdClass();
        
        $exception = new \InvalidArgumentException("Test exception");
        
        $decoder = $this->getMockBuilder(DocumentDecoderInterface::class)->getMock();
        $decoder
            ->expects($this->once())
            ->method('decode')
            ->withAnyParameters()
            ->willThrowException($exception);
        
        $factory = $this->getMockBuilder(DecodersFactoryInterface::class)->getMock();
        $factory
            ->expects($this->once())
            ->method('getDocumentDecoder')
            ->withAnyParameters()
            ->willReturn($decoder);
        
        try {
            $parser = new DataParser($factory);
            $parser->parseDocument($data, 'document1');
            
            $this->fail("Parser should throw JsonApiException on error");
        } catch (JsonApiException $e) {
            $this->assertEquals(500, $e->getHttpCode());
            $this->assertSame($exception, $e->getPrevious());
            
            $errors = $e->getErrors();
            $error = $errors[0];

            $this->assertEquals(500, $error->getStatus());
            $this->assertEquals('Internal server error', $error->getTitle());
            $this->assertEquals($exception->getMessage(), $error->getDetail());
        }
    }


    /**
     * Returns instance of parser
     *
     * @return DataParser
     */
    private function getParser()
    {
        $factory = $this->getMockBuilder(DecodersFactoryInterface::class)->getMock();

        return new DataParser($factory);
    }
}
