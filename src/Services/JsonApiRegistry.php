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

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
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
    public function registerDecoder($name, $decoder)
    {
        if ((!$decoder instanceof DecoderInterface) && (!$decoder instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf(
                "Decoder must be a %s instance or closure",
                DecoderInterface::class
            ));
        }

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

        if ($this->decoders[$name] instanceof \Closure) {
            $factory = $this->decoders[$name];
            $decoder = $factory();
            if (!$decoder instanceof DecoderInterface) {
                throw new \LogicException(sprintf(
                    "Decoder '%s' should implement %s interface",
                    $name,
                    DecoderInterface::class
                ));
            }

            $this->decoders[$name] = $decoder;

        }

        return $this->decoders[$name];
    }

    /**
     * @inheritdoc
     */
    public function registerEncoder($name, $encoder)
    {
        if ((!$encoder instanceof EncoderInterface) && (!$encoder instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf(
                "Encoder must be a %s instance or closure",
                EncoderInterface::class
            ));
        }

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

        if ($this->encoders[$name] instanceof \Closure) {
            $factory = $this->encoders[$name];
            $encoder = $factory();
            if (!$encoder instanceof EncoderInterface) {
                throw new \LogicException(sprintf(
                    "Encoder '%s' should implement %s interface",
                    $name,
                    EncoderInterface::class
                ));
            }

            $this->encoders[$name] = $encoder;
        }

        return $this->encoders[$name];
    }
}
