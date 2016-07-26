<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface as BaseDecoderInterface;

/**
 * Response decoder interface
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DecoderInterface extends BaseDecoderInterface
{
    /**
     * Sets content type
     *
     * @param string $type
     */
    public function setContentType($type);
}