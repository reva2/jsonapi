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

use Reva2\JsonApi\Annotations as API;

/**
 * Cat
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 *
 * @API\ApiResource()
 */
class Cat extends Pet
{
    /**
     * @inheritdoc
     */
    public function whoIAm()
    {
        return 'cat';
    }
}