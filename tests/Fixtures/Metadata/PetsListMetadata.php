<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Metadata;

use Reva2\JsonApi\Annotations\ApiObject;
use Reva2\JsonApi\Annotations\Property;

/**
 * Pets list metadata
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Metadata
 *
 * @ApiObject()
 */
class PetsListMetadata
{
    /**
     * @var string
     * @Property(type="string")
     */
    public $someString;

    /**
     * @var int
     * @Property(type="integer")
     */
    public $someInt;
}
