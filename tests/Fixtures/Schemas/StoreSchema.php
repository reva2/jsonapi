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
 * StoreSchema
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Schemas
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class StoreSchema extends BaseSchema
{
    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return 'stores';
    }

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Store */

        return $resource->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(mixed $resource, ContextInterface $context): array
    {
        /* @var $resource \Reva2\JsonApi\Tests\Fixtures\Resources\Store */
        return [
            'name' => $resource->getName(),
            'address' => $resource->getAddress()
        ];
    }

    public function getRelationships($resource, ContextInterface $context): array
    {
        return [];
    }
}