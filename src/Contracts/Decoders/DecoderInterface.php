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

/**
 * Response decoder interface
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DecoderInterface
{
    /**
     * Sets content type
     *
     * @param string $type
     */
    public function setContentType(string $type);

    /**
     * Decode input JSON API data
     *
     * @param string $data
     * @return mixed
     */
    public function decode(string $data): mixed;
}
