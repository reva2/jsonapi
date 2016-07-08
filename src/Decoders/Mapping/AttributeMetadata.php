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

use Reva2\JsonApi\Contracts\Decoders\Mapping\AttributeMetadataInterface;

/**
 * JSON API resource attribute metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class AttributeMetadata extends PropertyMetadata implements AttributeMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    protected $dataType;

    /**
     * @var string|null
     * @internal
     */
    protected $datetimeFormat;

    /**
     * @var string
     */
    protected $itemType;

    /**
     * @inheritdoc
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * @param null|string $datetimeFormat
     * @return $this
     */
    public function setDatetimeFormat($datetimeFormat = null)
    {
        $this->datetimeFormat = $datetimeFormat;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItemType()
    {
        return $this->itemType;
    }
}