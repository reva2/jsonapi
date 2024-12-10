<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Http;

use Reva2\JsonApi\Contracts\Encoder\EncodingParametersInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;

/**
 * JSON API request interface
 *
 * @package Reva2\JsonApi\Contracts\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface RequestInterface
{
    /**
     * Request request environment
     *
     * @return EnvironmentInterface
     */
    public function getEnvironment(): EnvironmentInterface;

    /**
     * Sets request query parameters
     *
     * @param EncodingParametersInterface|null $query
     * @return $this
     */
    public function setQuery(EncodingParametersInterface $query = null): self;

    /**
     * Returns query parameters
     *
     * @return EncodingParametersInterface|null
     */
    public function getQuery(): ?EncodingParametersInterface;

    /**
     * Sets request body
     *
     * @param mixed|null $body
     * @return $this
     */
    public function setBody(mixed $body = null): self;

    /**
     * Returns request body
     *
     * @return mixed|null
     */
    public function getBody(): mixed;
}
