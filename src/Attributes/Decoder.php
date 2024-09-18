<?php

namespace Reva2\JsonApi\Attributes;

class Decoder extends MediaType
{
    /**
     * @var string
     */
    public string $decoder;

    public function __construct(string $decoder, string $type, string $subtype = '*')
    {
        parent::__construct($type, $subtype);

        $this->decoder = $decoder;
    }
}