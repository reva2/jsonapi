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

/**
 * Example JSON API resource that represent store
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
#[API\Resource('stores')]
class Store
{
    /**
     * @var string
     */
    #[API\Id]
    protected string $id;

    /**
     * @var string
     */
    #[API\Attribute]
    protected string $name;

    /**
     * @var string
     */
    #[API\Attribute]
    protected string $address;

    /**
     * @var bool
     */
    protected $converted = false;

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
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConverted()
    {
        return $this->converted;
    }

    /**
     * @param $converted
     * @return $this
     */
    public function setConverted($converted)
    {
        $this->converted = (bool) $converted;

        return $this;
    }
}