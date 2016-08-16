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
 * JSON API schema for pets resource
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Schemas
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class PetSchema extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    public function getResourceType()
    {
        return 'pets';
    }

    /**
     * @inheritdoc
     */
    public function getId($resource)
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Pet */

        return $resource->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($resource)
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Pet */

        return [
            'name' => $resource->name,
            'family' => $resource->family
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Pet */

        return [
            'store' => [
                self::DATA => $resource->store,
                self::SHOW_RELATED => true
            ]
        ];
    }
}