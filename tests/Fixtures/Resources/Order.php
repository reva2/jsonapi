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
 * Example JSON API resource that represent order
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 */
#[API\Resource("orders")]
class Order
{
    /**
     * @var string
     */
    #[API\Id]
    public string $id;

    /**
     * @var float
     */
    #[API\Attribute]
    public float $amount;

    /**
     * @var Store
     */
    #[API\Relationship(
        type: Store::class,
        loaders: [
            new API\Loader('store.custom_loader:create', 'CreateOrder'),
            new API\Loader('store.custom_loader:load', 'UpdateOrder')
        ]
    )]
    public Store $store;

    /**
     * @var Pet[]
     */
    #[API\Relationship(
        type: Pet::class . '[]',
        loaders: [
            new API\Loader('pet.custom_loader:create', 'CreateOrder'),
            new API\Loader('pet.custom_loader:load', 'UpdateOrder'),
        ]
    )]
    public array $pets;
}
