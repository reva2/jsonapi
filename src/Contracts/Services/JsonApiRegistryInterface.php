<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Services;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;

/**
 * JSON API decoders/encoders registry interface
 *
 * @package Reva2\JsonApi\Contracts\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface JsonApiRegistryInterface
{
    /**
     * Register decoder with specified name
     *
     * @param string $name;
     * @param DecoderInterface|\Closure $decoder
     * @return $this
     */
    public function registerDecoder($name, $decoder);

    /**
     * Returns decoder with specified name
     *
     * @param string $name
     * @return DecoderInterface
     */
    public function getDecoder($name);

    /**
     * Register encoder with specified name
     *
     * @param string $name
     * @param EncoderInterface|\Closure $encoder
     * @return $this;
     */
    public function registerEncoder($name, $encoder);

    /**
     * Returns encoder with specified name
     *
     * @param string $name
     * @return EncoderInterface
     */
    public function getEncoder($name);
}