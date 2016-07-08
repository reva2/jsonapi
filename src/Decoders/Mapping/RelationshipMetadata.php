<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping;

use Reva2\JsonApi\Contracts\Decoders\Mapping\RelationshipMetadataInterface;

/**
 * JSON API resource relationship metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class RelationshipMetadata extends ReferenceMetadata implements RelationshipMetadataInterface
{
    /**
     * @var bool
     * @internal
     */
    public $ormEntity = false;

    /**
     * @var string|null
     * @internal
     */
    public $ormEntityClass;

    /**
     * @inheritdoc
     */
    public function isOrmEntity()
    {
        return $this->ormEntity;
    }

    /**
     * Sets value of flag which show that reference contains ORM entity
     *
     * @param boolean $ormEntity
     * @return $this
     */
    public function setOrmEntity($ormEntity)
    {
        $this->ormEntity = $ormEntity;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrmEntityClass()
    {
        return $this->ormEntityClass;
    }

    /**
     * Sets class of ORM entity
     *
     * @param string|null $entityClass
     * @return $this
     */
    public function setOrmEntityClass($entityClass = null)
    {
        $this->ormEntityClass = $entityClass;

        return $this;
    }
}