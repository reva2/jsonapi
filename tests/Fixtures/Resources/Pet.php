<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Annotations as API;

/**
 * Example JSON API resource that represent pet
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @API\Resource("pets")
 */
class Pet
{
    /**
     * @var string
     * @API\Attribute()
     */
    public $name;

    /**
     * @var Store
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Store")
     */
    public $store;
}