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

use Reva2\JsonApi\Attributes as API;

/**
 * Person
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 */
#[API\Resource(type: 'persons')]
class Person
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
    public string $firstName;

    /**
     * @var string
     */
    #[API\Attribute]
    public string $lastName;
}
