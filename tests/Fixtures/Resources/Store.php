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
 * Example JSON API resource that represent store
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @API\Resource("stores")
 */
class Store
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     * @API\Attribute()
     */
    protected $name;

    /**
     * @var string
     * @API\Attribute()
     */
    protected $address;
}