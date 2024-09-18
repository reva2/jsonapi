<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Controllers;

use Reva2\JsonApi\Attributes as API;

/**
 * AnotherController
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Controllers
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class AnotherController
{
    public function testAction()
    {

    }

    #[API\Request]
    public function anotherAction()
    {

    }
}