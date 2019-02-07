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
 * JSON API relationship loader annotation
 *
 * @author Sergey Revenko <dedsemen@gmail.com>
 * @package Reva2\JsonApi\Annotations
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Loader
{
    /**
     * @var string
     */
    public $loader;

    /**
     * Serialization group
     *
     * @var string
     */
    public $group = 'Default';
}