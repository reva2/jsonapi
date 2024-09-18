<?php

namespace Reva2\JsonApi\Attributes;

class Matcher
{
    /**
     * Request decoders
     *
     * @var Decoder[]
     */
    public ?array $decoders;

    /**
     * Response encoders
     *
     * @var Encoder[]
     */
    public ?array $encoders;

    /**
     * @param Decoder[]|null $decoders
     * @param Encoder[]|null $encoders
     */
    public function __construct(?array $decoders = null, ?array $encoders = null)
    {
        $this->decoders = $decoders;
        $this->encoders = $encoders;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'encoders' => [],
            'decoders' => [],
        ];

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
    private function getMediaType(MediaType $mediaType): string
    {
        return $mediaType->type . '/' . $mediaType->subtype;
    }
}