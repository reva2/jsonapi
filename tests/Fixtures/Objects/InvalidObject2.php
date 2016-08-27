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
 * Another invalid JSON API object
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Objects
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class InvalidObject2
{
    /**
     * @var mixed
     * @API\Property(parser="parseData")
     */
    public $data;
}