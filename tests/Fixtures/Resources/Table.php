<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Annotations as API;

/**
 * @API\ApiResource(name="tables")
 */
class Table
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
     * @var float
     * @API\Attribute()
     */
    public $height;

    /**
     * @var Office
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Office")
     */
    public $office;
}
