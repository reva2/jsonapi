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
     * Constructor
     *
     * @param string $property
     * @param string $className
     */
    public function __construct($property, $className)
    {
        parent::__construct($className);

        $this->propertyName = $className;
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
}