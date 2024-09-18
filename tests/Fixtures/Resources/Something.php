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
 * Something resource
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 */
#[API\Resource('something')]
class Something
{
    /**
     * Resource id
     *
     * @var string
     */
    #[API\Id]
    public string $id;

    /**
     * Resource name
     *
     * @var string
     */
    #[API\Attribute]
    public string $name;
}
