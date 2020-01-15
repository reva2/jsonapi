<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Http\Query;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Http\Query\QueryParametersParser;
use RuntimeException;

/**
 * Test for query parameters parser
 *
 * @package Reva2\JsonApi\Tests\Http\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class QueryParametersParserTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseQuery()
    {
        $parameters = [
            'param1' => 'value1',
            'param2' => 'value2'
        ];

        $queryType = 'test';

        $value = new \stdClass();
        $value->param1 = 'value1';
        $value->param2 = 'value2';

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request
            ->expects($this->once())
            ->method('getQueryParams')
            ->willReturn($parameters);

        $dataParser = $this->getMockBuilder(DataParserInterface::class)->getMock();
        $dataParser
            ->expects($this->once())
            ->method('parseQueryParams')
            ->with($parameters, $queryType)
            ->willReturn($value);

        $queryParser = new QueryParametersParser();
        $queryParser
            ->setDataParser($dataParser)
            ->setQueryType($queryType)
            ->parse($request);
    }

    /**
     * @test
     */
    public function shouldThrowIfDataParserNotSpecified()
    {
        $query = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data parser not specified');

        $queryParser = new QueryParametersParser();
        $queryParser->parse($query);
    }

    /**
     * @test
     */
    public function shouldThrowIfQueryTypeNotSpecified()
    {
        $query = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $dataParser = $this->getMockBuilder(DataParserInterface::class)->getMock();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query type not specified');

        $queryParser = new QueryParametersParser();
        $queryParser
            ->setDataParser($dataParser)
            ->parse($query);
    }
}
