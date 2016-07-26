<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Services;

use Reva2\JsonApi\Contracts\Services\JsonApiRegistryInterface;

/**
 * JSON API decoders/encoders registry
 *
 * @package Reva2\JsonApi\Services
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class JsonApiRegistry implements JsonApiRegistryInterface
{
    /**
     * Decoders map
     *
     * @var array
     */
    protected $decoders = [];

    /**
     * Encoders map
     *
     * @var array
     */
    protected $encoders = [];

    /**
     * @inheritdoc
     */
    public function registerDecoder($name, \Closure $decoder)
    {
        $this->decoders[$name] = $decoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDecoder($name)
    {
        if (!array_key_exists($name, $this->decoders)) {
            throw new \RuntimeException(sprintf("Decoder with name '%s' is not registered", $name));
        }

        return $this->decoders[$name];
    }

    /**
     * @inheritdoc
     */
    public function registerEncoder($name, \Closure $encoder)
    {
        $this->encoders[$name] = $encoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoder($name)
    {
        if (!array_key_exists($name, $this->encoders)) {
            throw new \RuntimeException(sprintf("Encoder with name '%s' is not registered", $name));
        }

        return $this->encoders[$name];
    }
}
