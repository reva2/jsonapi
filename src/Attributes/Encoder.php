<?php

namespace Reva2\JsonApi\Attributes;

class Encoder extends MediaType
{
    /**
     * @var string
     */
    public string $encoder;

    public function __construct(string $encoder, string $type, string $subtype = '*')
    {
        parent::__construct($type, $subtype);

        $this->encoder = $encoder;
    }
}