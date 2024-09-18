<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;

#[API\Resource(type: "offices")]
class Office
{
    /**
     * @var ?string
     * @API\Id()
     */
    public ?string $id;

    /**
     * @var ?string
     * @API\Attribute()
     */
    public ?string $name;

    /**
     * @var ?Table
     */
    #[API\Relationship(type: Table::class)]
    public ?Table $table;

    /**
     * @var array|Window[]
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Window[]")
     */
    #[API\Relationship(type: Window::class . '[]')]
    public ?array $windows;
}
