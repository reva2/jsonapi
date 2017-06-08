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

use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;

/**
 * Property metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class PropertyMetadata extends GenericMetadata implements PropertyMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    public $propertyName;

    /**
     * @var string
     * @internal
     */
    public $setter;

    /**
     * @var string
     * @internal
     */
    public $dataPath;

    /**
     * @var string
     * @internal
     */
    public $dataType;

    /**
     * @var mixed
     * @internal
     */
    public $dataTypeParams;

    /**
     * @var string|null
     * @internal
     */
    public $converter;

    /**
     * @var array
     * @internal
     */
    public $groups = ['Default'];

    /**
     * Constructor
     *
     * @param string $property
     * @param string $className
     */
    public function __construct($property, $className)
    {
        parent::__construct($className);

        $this->propertyName = $property;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @inheritdoc
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * Sets property setter
     *
     * @param string|null $setter
     * @return $this
     */
    public function setSetter($setter = null) {
        $this->setter = $setter;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        return (null !== $this->dataPath) ? $this->dataPath : $this->propertyName;
    }

    /**
     * @param string|null $dataPath
     * @return $this
     */
    public function setDataPath($dataPath = null)
    {
        $this->dataPath = $dataPath;

        return $this;
    }

    /**
     * Returns property data type
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Sets property data type
     *
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Returns additional data type params
     *
     * @return mixed
     */
    public function getDataTypeParams()
    {
        return $this->dataTypeParams;
    }

    /**
     * Sets additional data type params
     *
     * @param mixed $dataTypeParams
     * @return $this
     */
    public function setDataTypeParams($dataTypeParams = null)
    {
        $this->dataTypeParams = $dataTypeParams;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * @param string|null $converter
     * @return $this
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     * @return $this
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }
}
