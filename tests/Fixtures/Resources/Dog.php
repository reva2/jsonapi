<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;

/**
 * Example JSON API resource that represent dog
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
#[API\Resource]
class Dog extends Pet
{
    /**
     * @inheritdoc
     */
    public function whoIAm(): string
    {
        return 'dog';
    }
}