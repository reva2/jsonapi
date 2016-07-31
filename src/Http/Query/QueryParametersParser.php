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

    /**
     * @inheritdoc
     */
    public function parse(ServerRequestInterface $request)
    {
        if (null === $this->parser) {
            throw new \RuntimeException('Data parser not specified');
        }

        if (null === $this->queryType) {
            throw new \RuntimeException('Query type not specified');
        }

        return $this->parser->parseQueryParams($request->getQueryParams(), $this->queryType);
    }

    /**
     * @inheritdoc
     */
    public function setDataParser(DataParserInterface $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setQueryType($type)
    {
        $this->queryType = $type;

        return $this;
    }
}
