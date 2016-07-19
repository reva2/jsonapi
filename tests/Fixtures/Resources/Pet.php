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
use Reva2\JsonApi\Tests\Fixtures as Fixtures;

/**
 * Example JSON API resource that represent pet
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @API\Resource(
 *     name="pets",
 *     discField="family",
 *     discMap={
 *      "cat": Fixtures\Resources\Cat::class,
 *      "dog": Fixtures\Resources\Dog::class
 *     }
 * )
 */
class Pet
{
    /**
     * @var string
     * @API\Attribute()
     */
    public $name;

    /**
     * @var string
     * @API\Attribute()
     */
    public $family;

    /**
     * @var Store
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Store")
     */
    public $store;

    /**
     * @return string
     */
    public function whoIAm()
    {
        return 'pet';
    }
}