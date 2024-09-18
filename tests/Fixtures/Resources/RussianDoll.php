<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;
use Reva2\JsonApi\Tests\Fixtures as Fixtures;

/**
 * Example JSON API resource that represent Russian Doll
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Mikhail Shaulsky <mikhail.shaulsky@orbitsoft.com>
 */
#[API\Resource(type: 'dolls')]
class RussianDoll
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
    public string $color;

    /**
     * @var ?RussianDoll
     */
    #[API\Relationship(type: RussianDoll::class)]
    public ?RussianDoll $containedBy = null;
}