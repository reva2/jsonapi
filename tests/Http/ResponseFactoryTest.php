<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Http;

use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test for response factory
 *
 * @package Reva2\JsonApi\Tests\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResponseFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateSymfonyResponseObject()
    {
        $factory = new ResponseFactory($this->getSchemas(), $this->getEnvironment());

        $response = $factory->getCodeResponse(200);
        /* @var $response Response */

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EnvironmentInterface
     */
    private function getEnvironment()
    {
        $environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();

        return $environment;
    }

    /**
     * @return SchemaContainerInterface|MockObject
     */
    private function getSchemas()
    {
        return $this->getMockBuilder(SchemaContainerInterface::class)->getMock();
    }
}
