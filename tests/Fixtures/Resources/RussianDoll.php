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

use Reva2\JsonApi\Annotations as API;
use Reva2\JsonApi\Tests\Fixtures as Fixtures;

/**
 * Example JSON API resource that represent Russian Doll
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Mikhail Shaulsky <mikhail.shaulsky@orbitsoft.com>
 *
 * @API\ApiResource(name="dolls")
 */
class RussianDoll
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
    public $color;

    /**
     * @var RussianDoll
     * @API\Relationship(
     *     type="Reva2\JsonApi\Tests\Fixtures\Resources\RussianDoll",
     * )
     */
    public $containedBy;
}