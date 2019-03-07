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
use Reva2\JsonApi\Annotations\Loader;
use Reva2\JsonApi\Annotations\Relationship;

/**
 * Example JSON API resource that represent order
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 *
 * @ApiResource(name="orders")
 */
class Order
{
    /**
     * @var string
     * @Id()
     */
    public $id;

    /**
     * @var float
     * @Attribute()
     */
    public $amount;

    /**
     * @var Store
     * @Relationship(
     *     type="Reva2\JsonApi\Tests\Fixtures\Resources\Store",
     *     loaders={
     *      @Loader(loader="store.custom_loader:create", group="CreateOrder"),
     *      @Loader(loader="store.custom_loader:load", group="UpdateOrder")
     *     }
     * )
     */
    public $store;

    /**
     * @var Pet[]
     * @Relationship(
     *     type="Reva2\JsonApi\Tests\Fixtures\Resources\Pet[]",
     *     loaders={
     *      @Loader(loader="pet.custom_loader:create", group="CreateOrder"),
     *      @Loader(loader="pet.custom_loader:load", group="UpdateOrder")
     *     }
     * )
     */
    public $pets;
}
