<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Annotations;

/**
 * ApiObject property annotation
 *
 * @package Reva2\JsonApi\Annotations
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Property
{
    /**
     * Data type
     *
     * @var string
     */
    public $type;

    /**
     * Data path
     *
     * @var string
     */
    public $path;

    /**
     * Method use for value parsing
     *
     * @var string
     */
    public $parser;

    /**
     * @var string
     */
    public $setter;
}
