<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Metadata;

use Reva2\JsonApi\Attributes as API;

/**
 * Pets list metadata
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Tests\Fixtures\Metadata
 */
#[API\ApiObject]
class PetsListMetadata
{
    /**
     * @var string
     */
    #[API\Property]
    public string $someString;

    /**
     * @var int
     */
    #[API\Property]
    public int $someInt;
}
