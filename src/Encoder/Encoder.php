<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Encoder;

use Neomerx\JsonApi\Encoder\Encoder as BaseEncoder;
use Reva2\JsonApi\Factories\Factory;

/**
 * Response encoder
 *
 * @package Reva2\JsonApi\Encoder
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Encoder extends BaseEncoder
{
    /**
     * @inheritdoc
     */
    protected static function getFactory()
    {
        return new Factory();
    }
}
