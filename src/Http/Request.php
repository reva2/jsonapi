<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Http;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\DocumentInterface;
use Reva2\JsonApi\Contracts\Http\RequestInterface;

/**
 * JSON API request
 *
 * @package Reva2\JsonApi\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Request implements RequestInterface
{
    /**
     * @var EncodingParametersInterface
     */
    protected $query;

    /**
     * @var DocumentInterface|null
     */
    protected $body;

    /**
     * Constructor
     *
     * @param EncodingParametersInterface $query
     * @param DocumentInterface|null $body
     */
    public function __construct(EncodingParametersInterface $query, DocumentInterface $body = null)
    {
        $this->query = $query;
        $this->body = $body;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }
}