<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http\Query;

use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Encoder\EncodingParametersInterface;
use Reva2\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Query parameters parser
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class QueryParametersParser implements QueryParametersParserInterface
{
    /**
     * @var DataParserInterface|null
     */
    protected ?DataParserInterface $parser = null;

    /**
     * @var string|null
     */
    protected ?string $queryType = null;

    /**
     * @param Request $request
     * @return EncodingParametersInterface
     */
    public function parse(Request $request): EncodingParametersInterface
    {
        if (null === $this->parser) {
            throw new RuntimeException('Data parser not specified');
        }

        if (null === $this->queryType) {
            throw new RuntimeException('Query type not specified');
        }

        return $this->parser->parseQueryParams($request->query->all(), $this->queryType);
    }

    /**
     * @inheritdoc
     */
    public function setDataParser(DataParserInterface $parser): self
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setQueryType($type): self
    {
        $this->queryType = $type;

        return $this;
    }
}
