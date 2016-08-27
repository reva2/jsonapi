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
 * Example JSON API resource that represent pet
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 *
 * @API\ApiResource(
 *     name="pets",
 *     discField="family",
 *     discMap={
 *      "cats": Fixtures\Resources\Cat::class,
 *      "dogs": Fixtures\Resources\Dog::class
 *     }
 * )
 */
class Pet
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
    public $name;

    /**
     * @var string
     * @API\Attribute()
     */
    public $family;

    /**
     * @var Store
     * @API\Relationship(
     *     type="Reva2\JsonApi\Tests\Fixtures\Resources\Store",
     *     converter="Reva2\JsonApi\Tests\Fixtures\Converters\StoreConverter::convert"
     * )
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