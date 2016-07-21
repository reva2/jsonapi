<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Decoders;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Interface for JSON API requests query params decoders
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface QueryParamsDecoderInterface
{
    /**
     * Decode JSON API request query parameters
     *
     * @param object|array $data
     * @return EncodingParametersInterface
     */
    public function decode($data);
}
