<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;

#[API\Resource('windows')]
class Window
{
    /**
     * @var string
     */
    #[API\Id()]
    public string $id;

    /**
     * @var string
     */
    #[API\Attribute]
    public string $name;

    /**
     * @var int
     */
    #[API\Attribute]
    public int $layers;

    /**
     * @var Office
     */
    #[API\Relationship(Office::class)]
    public Office $office;
}
