<?php

namespace Reva2\JsonApi\Tests\Fixtures\Metadata;

use Reva2\JsonApi\Attributes as API;

/**
 * @ApiObject()
 */
#[API\ApiObject()]
class OfficesListMetadata
{
    /**
     * @var string
     */
    #[API\Property(type: "integer", path: "inner_int.one")]
    public $someInnerIntOne;

    /**
     * @var int
     * @Property(type="integer", path="inner_int.two")
     */
    #[API\Property(path: "inner_int.two")]
    public $someInnerIntTwo;

    /**
     * @var int
     */
    #[API\Property]
    public int $someInt;
}
