<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\BaseSchema;

/**
 * JSON API schema for pets resource
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Schemas
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class PetSchema extends BaseSchema
{
    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return 'pets';
    }

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Pet */

        return $resource->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(mixed $resource, ContextInterface $context): array
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
    public function getRelationships($resource, ContextInterface $context): array
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Pet */

        return [
            'store' => [
                self::RELATIONSHIP_DATA => $resource->store,
                self::RELATIONSHIP_LINKS_RELATED => true,
                self::RELATIONSHIP_LINKS_SELF => false
            ]
        ];
    }
}