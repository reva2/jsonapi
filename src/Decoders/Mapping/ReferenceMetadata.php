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

use Reva2\JsonApi\Contracts\Decoders\Mapping\ReferenceMetadataInterface;

/**
 * Reference metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ReferenceMetadata extends PropertyMetadata implements ReferenceMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    public $refClass;

    /**
     * @var bool
     * @internal
     */
    public $array = false;

    /**
     * @inheritdoc
     */
    public function getReferenceClass()
    {
        return $this->refClass;
    }

    /**
     * Sets reference class
     * 
     * @param string $refClass
     * @return $this
     */
    public function setReferenceClass($refClass)
    {
        $this->refClass = $refClass;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isArray()
    {
        return $this->array;
    }

    /**
     * Sets value of flag which show that refrence contains serveral items.
     * 
     * @param bool $isArray
     * @return $this
     */
    public function setArray($isArray)
    {
        $this->array = $isArray;
        
        return $this;
    }
}