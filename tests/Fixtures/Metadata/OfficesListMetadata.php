<?php

namespace Reva2\JsonApi\Tests\Fixtures\Metadata;

use Reva2\JsonApi\Annotations\ApiObject;
use Reva2\JsonApi\Annotations\Property;

/**
 * @ApiObject()
 */
class OfficesListMetadata
{
    /**
     * @var string
     * @Property(type="integer", path="inner_int.one")
     */
    public $someInnerIntOne;

    /**
     * @var int
     * @Property(type="integer", path="inner_int.two")
     */
    public $someInnerIntTwo;

    /**
     * @var int
     * @Property(type="integer")
     */
    public $someInt;
}
