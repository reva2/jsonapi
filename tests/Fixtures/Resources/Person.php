<?php
/*
 * This file is part of the jsonapi.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Annotations\ApiResource;
use Reva2\JsonApi\Annotations\Attribute;
use Reva2\JsonApi\Annotations\Id;

/**
 * Person
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 *
 * @ApiResource(name="persons")
 */
class Person
{
    /**
     * @var string
     * @Id()
     */
    public $id;

    /**
     * @var string
     * @Attribute()
     */
    public $firstName;

    /**
     * @var string
     * @Attribute()
     */
    public $lastName;
}
