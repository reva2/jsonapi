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

use Reva2\JsonApi\Attributes as API;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetDocument;
use Reva2\JsonApi\Tests\Fixtures\Query\PetQuery;
use Reva2\JsonApi\Tests\Fixtures\Query\PetsListQuery;

/**
 * PetsController
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Controllers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
#[API\Request(
    urlPrefix: '/myapp',
    matcher: new API\Matcher(
        decoders: [
            new API\Decoder('jsonapi.decoders.jsonapi', 'application', 'vnd.json+api')
        ],
        encoders: [
            new API\Encoder('jsonapi.encoders.jsonapi', 'application', 'vnd.json+api'),
        ]
    ),
)]
class PetsController
{
    #[API\Request(query: PetsListQuery::class)]
    public function getListAction()
    {

    }

    #[API\Request(query: PetQuery::class, body: PetDocument::class, validation: ['Default', 'Create'])]
    public function createAction()
    {

    }
}