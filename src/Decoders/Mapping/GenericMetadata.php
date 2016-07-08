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

use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;

/**
 * Generic metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class GenericMetadata implements GenericMetadataInterface
{
    /**
     * @var string
     * @internal
     */
    public $className;

    /**
     * Constructor
     *
     * @param $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @inheritdoc
     */
    public function getClassName()
    {
        return $this->className;
    }
}