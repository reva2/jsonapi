<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Annotations;

/**
 * JSON API code matcher annotation
 *
 * @package Reva2\JsonApi\Annotations
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Matcher
{
    /**
     * Request decoders
     *
     * @var array<Reva2\JsonApi\Annotations\Decoder>
     */
    public $decoders;

    /**
     * Response encoders
     *
     * @var array<Reva2\JsonApi\Annotations\Encoder>
     */
    public $encoders;

    /**
     * @return array
     */
    public function toArray()
    {
        $data = ['encoders' => [], 'decoders' => []];

        if (null !== $this->decoders) {
            foreach ($this->decoders as $decoder) {
                $data['decoders'][$this->getMediaType($decoder)] = $decoder->decoder;
            }
        }

        if (null !== $this->encoders) {
            foreach ($this->encoders as $encoder) {
                $data['encoders'][$this->getMediaType($encoder)] = $encoder->encoder;
            }
        }

        return $data;
    }

    /**
     * @param MediaType $mediaType
     * @return string
     */
    private function getMediaType(MediaType $mediaType)
    {
        return $mediaType->type . '/' . $mediaType->subtype;
    }
}
