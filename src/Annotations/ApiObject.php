<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Annotations;

/**
 * Annotation for JSON API decoder object
 *
 * @package Reva2\JsonApi\Annotations
 * @author Sergey Revenko <dedsemen@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class ApiObject
{
    /**
     * Name of field that store discriminator value
     *
     * @var string
     */
    public $discField;

    /**
     * Mapping of discriminator values to classes
     *
     * @var array
     */
    public $discMap;
}
