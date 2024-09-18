<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;

#[API\Resource('tables')]
class Table
{
    /**
     * @var string
     */
    #[API\Id]
    public string $id;

    /**
     * @var string
     */
    #[API\Attribute]
    public string $name;

    /**
     * @var float
     */
    #[API\Attribute]
    public float $height;

    /**
     * @var Office
     */
    #[API\Relationship(type: Office::class)]
    public Office $office;
}
