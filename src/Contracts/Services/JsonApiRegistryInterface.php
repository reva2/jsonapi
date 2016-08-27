<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Services;

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
     * @param string $name
     * @param \Closure $decoder
     * @return $this
     */
    public function registerDecoder($name, \Closure $decoder);

    /**
     * Returns decoder with specified name
     *
     * @param string $name
     * @return \Closure
     */
    public function getDecoder($name);

    /**
     * Register encoder with specified name
     *
     * @param string $name
     * @param \Closure $encoder
     * @return $this
     */
    public function registerEncoder($name, \Closure $encoder);

    /**
     * Returns encoder with specified name
     *
     * @param string $name
     * @return \Closure
     */
    public function getEncoder($name);
}
