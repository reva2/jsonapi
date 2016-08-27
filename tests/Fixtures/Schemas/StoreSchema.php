<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Schemas;

use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * StoreSchema
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Schemas
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class StoreSchema extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    public function getResourceType()
    {
        return 'stores';
    }

    /**
     * @inheritdoc
     */
    public function getId($resource)
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Store */

        return $resource->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($resource)
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Store */
        return [
            'name' => $resource->getName(),
            'address' => $resource->getAddress()
        ];
    }
}