<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Annotations as API;

/**
 * @API\ApiResource(name="offices")
 */
class Office
{
    /**
     * @var string
     * @API\Id()
     */
    public $id;

    /**
     * @var string
     * @API\Attribute()
     */
    public $name;

    /**
     * @var Table
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Table")
     */
    public $table;

    /**
     * @var array|Window[]
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Window[]")
     */
    public $windows;
}
