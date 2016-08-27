<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\Fixtures\Controllers;

use Reva2\JsonApi\Annotations as API;

/**
 * PetsController
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Controllers
 * @author Sergey Revenko <dedsemen@gmail.com>
 *
 * @API\ApiRequest(
 *     urlPrefix="/myapp",
 *     matcher=@API\Matcher(
 *      decoders={
 *          @API\Decoder(type="application", subtype="vnd.json+api", decoder="jsonapi.decoders.jsonapi")
 *      },
 *      encoders={
 *          @API\Encoder(type="application", subtype="vnd.json+api", encoder="jsonapi.encoders.jsonapi")
 *      }
 *     )
 * )
 */
class PetsController
{
    /**
     * @API\ApiRequest(query="Reva2\JsonApi\Tests\Fixtures\Query\PetsListQuery")
     */
    public function getListAction()
    {

    }

    /**
     * @API\ApiRequest(
     *     query="Reva2\JsonApi\Tests\Fixtures\Query\PetQuery",
     *     body="Reva2\JsonApi\Tests\Fixtures\Documents\PetDocument",
     *     validation={"Default", "Create"}
     * )
     */
    public function createAction()
    {

    }
}