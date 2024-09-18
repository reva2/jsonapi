<?php

namespace Reva2\JsonApi\Attributes;

class MediaType
{
    /**
     * Media type
     *
     * @var string
     */
    public string $type;

    /**
     * Media sub-type
     *
     * @var string
     */
    public string $subtype;

    /**
     * @param string $type
     * @param string $subtype
     */
    public function __construct(string $type, string $subtype = '*')
    {
        $this->type = $type;
        $this->subtype = $subtype;
    }
}