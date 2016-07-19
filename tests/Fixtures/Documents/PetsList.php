<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Documents;

use Reva2\JsonApi\Annotations as API;
use Reva2\JsonApi\Tests\Fixtures\Resources\Pet;

/**
 * JSON API document that contains pets list
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Documents
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @API\Document(allowEmpty=true)
 */
class PetsList
{
    /**
     * @var Pet[]
     * @API\Content(type="Array<Reva2\JsonApi\Tests\Fixtures\Resources\Pet>")
     */
    public $data;
}