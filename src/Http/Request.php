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
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;

/**
 * JSON API request
 *
 * @package Reva2\JsonApi\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Request implements RequestInterface
{
    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @var EncodingParametersInterface
     */
    protected $query;

    /**
     * @var mixed|null
     */
    protected $body;

    /**
     * Constructor
     *
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param EncodingParametersInterface|null $query
     * @return $this
     */
    public function setQuery(EncodingParametersInterface $query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed|null $body
     * @return $this
     */
    public function setBody($body = null)
    {
        $this->body = $body;

        return $this;
    }
}
