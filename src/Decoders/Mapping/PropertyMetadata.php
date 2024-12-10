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
    public string $propertyName;

    /**
     * @var string|null
     * @internal
     */
    public ?string $setter = null;

    /**
     * @var string|null
     * @internal
     */
    public ?string $dataPath = null;

    /**
     * @var string
     * @internal
     */
    public string $dataType;

    /**
     * @var mixed
     * @internal
     */
    public mixed $dataTypeParams;

    /**
     * @var string|null
     * @internal
     */
    public ?string $converter;

    /**
     * @var array
     * @internal
     */
    public array $groups = ['Default'];

    /**
     * @var array
     * @internal
     */
    public array $loaders = [];

    /**
     * Constructor
     *
     * @param string $property
     * @param string $className
     */
    public function __construct(string $property, string $className)
    {
        parent::__construct($className);

        $this->propertyName = $property;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @inheritdoc
     */
    public function getSetter(): ?string
    {
        return $this->setter;
    }

    /**
     * Sets property setter
     *
     * @param string|null $setter
     * @return $this
     */
    public function setSetter(string $setter = null): self
    {
        $this->setter = $setter;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataPath(): string
    {
        return (null !== $this->dataPath) ? $this->dataPath : $this->propertyName;
    }

    /**
     * @param string|null $dataPath
     * @return $this
     */
    public function setDataPath(string $dataPath = null): self
    {
        $this->dataPath = $dataPath;

        return $this;
    }

    /**
     * Returns property data type
     *
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * Sets property data type
     *
     * @param string $dataType
     * @return $this
     */
    public function setDataType(string $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Returns additional data type params
     *
     * @return mixed
     */
    public function getDataTypeParams(): mixed
    {
        return $this->dataTypeParams;
    }

    /**
     * Sets additional data type params
     *
     * @param mixed $dataTypeParams
     * @return $this
     */
    public function setDataTypeParams(mixed $dataTypeParams = null): self
    {
        $this->dataTypeParams = $dataTypeParams;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConverter(): ?string
    {
        return $this->converter;
    }

    /**
     * @param string|null $converter
     * @return $this
     */
    public function setConverter(string $converter = null): self
    {
        $this->converter = $converter;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     * @return $this
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @param array $loaders
     * @return $this
     */
    public function setLoaders(array $loaders): self
    {
        $this->loaders = $loaders;

        return $this;
    }

    /**
     * @return array
     */
    public function getLoaders(): array
    {
        return $this->loaders;
    }
}
