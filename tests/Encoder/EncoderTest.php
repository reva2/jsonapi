<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Encoder;

use Reva2\JsonApi\Encoder\Encoder;

/**
 * Tests for JSON API encoder
 *
 * @package Reva2\JsonApi\Tests\Encoder
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class EncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldUserCustomFactory()
    {
        $encoder = Encoder::instance();

        $this->assertInstanceOf(Encoder::class, $encoder);
    }
}
