<?php

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Annotations as API;

/**
 * @API\ApiResource(name="windows")
 */
class Window
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
     * @var int
     * @API\Attribute()
     */
    public $layers;

    /**
     * @var Office
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Office")
     */
    public $office;
}
