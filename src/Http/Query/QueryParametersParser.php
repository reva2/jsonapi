<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http\Query;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;

/**
 * Query parameters parser
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class QueryParametersParser implements QueryParametersParserInterface
{
    /**
     * @var DataParserInterface
     */
    protected $parser;

    /**
     * @var string|null
     */
    protected $queryType;


    public function parse(ServerRequestInterface $request)
    {
        // TODO: Implement parse() method.
    }

    public function setDataParser(DataParserInterface $parser)
    {
        // TODO: Implement setDataParser() method.
    }

    public function setQueryType($type)
    {
        // TODO: Implement setQueryType() method.
    }
}