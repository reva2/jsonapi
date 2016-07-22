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
 * @API\ApiResource("stores")
 */
class Store
{
    /**
     * @var string
     * @API\Id()
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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}