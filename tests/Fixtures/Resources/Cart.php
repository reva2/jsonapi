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

/**
 * Example JSON API resource with two to-one relationships of different types
 * and type hinted setters. Used to cover the case when two relationships
 * reference the same (type, id) pair: a resource of the wrong type must not be
 * reused from the parsing context and passed to a setter expecting another
 * class.
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 *
 * @API\ApiResource("carts")
 */
class Cart
{
    /**
     * @var string
     * @API\Id()
     */
    public $id;

    /**
     * @var Store
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Store")
     */
    protected $store;

    /**
     * @var Person
     * @API\Relationship(type="Reva2\JsonApi\Tests\Fixtures\Resources\Person")
     */
    protected $owner;

    /**
     * @param Store $store
     * @return $this
     */
    public function setStore(Store $store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param Person $owner
     * @return $this
     */
    public function setOwner(Person $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Person
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
