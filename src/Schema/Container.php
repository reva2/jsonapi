<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Schema;

use Doctrine\Common\Util\ClassUtils;
use Neomerx\JsonApi\Schema\SchemaContainer;

/**
 * Schema container for JSON API encoder
 *
 * @package Reva2\JsonApi\Schema
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Container extends SchemaContainer
{
    /**
     * @inheritdoc
     */
    protected function getResourceType($resource): string
    {
        return ClassUtils::getRealClass(get_class($resource));
    }
}
