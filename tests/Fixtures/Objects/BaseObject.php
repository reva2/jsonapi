<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Objects;

use Reva2\JsonApi\Annotations as API;

/**
 * Base example object
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Objects
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class BaseObject
{
    /**
     * @var string
     * @API\Property()
     */
    protected $parentProp;

    /**
     * Sets value of parent property
     *
     * @param $value
     */
    public function setParentProp($value)
    {
        $this->parentProp = $value;
    }
}