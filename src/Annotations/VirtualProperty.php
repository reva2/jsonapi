<?php
/*
 * This file is part of the jsonapi.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Annotations;

/**
 * ApiObject virtual property annotation
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Annotations
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class VirtualProperty extends Property
{
    /**
     * Property name
     *
     * @var string
     */
    public $name;
}
