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

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * JSON API request interface
 *
 * @package Reva2\JsonApi\Contracts\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface RequestInterface
{
    /**
     * Returns query parameters
     *
     * @return EncodingParametersInterface
     */
    public function getQuery();

    /**
     * Returns request body
     *
     * @return mixed|null
     */
    public function getBody();
}
