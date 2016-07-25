<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Reva2\JsonApi\EventListener\ApiListener;
use Reva2\JsonApi\Tests\Fixtures\Controllers\AnotherController;
use Reva2\JsonApi\Tests\Fixtures\Controllers\PetsController;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetDocument;
use Reva2\JsonApi\Tests\Fixtures\Query\PetQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test for JSON API event listener
 *
 * @package Reva2\JsonApi\Tests\EventListener
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ApiListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiListener
     */
    protected $listener;

    /**
     * @test
     */
    public function shouldSubscribeOnKernelControllerEvent()
    {
        $this->assertArrayHasKey(KernelEvents::CONTROLLER, ApiListener::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function shouldLoadJsonApiConfiguration()
    {
        $controller = new PetsController();
        $event = $this->createEvent([$controller, 'createAction']);

        $this->listener->onKernelController($event);

        $request = $event->getRequest();

        $this->assertTrue($request->attributes->has('_jsonapi'));

        $config = $request->attributes->get('_jsonapi');
        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('query', $config);
        $this->assertSame(PetQuery::class, $config['query']);

        $this->assertArrayHasKey('body', $config);
        $this->assertSame(PetDocument::class, $config['body']);

        $this->assertArrayHasKey('matcher', $config);
        $this->assertArrayHasKey('decoders', $config['matcher']);
        $this->assertSame(['application/vnd.json+api' => 'jsonapi.decoders.jsonapi'], $config['matcher']['decoders']);
        $this->assertArrayHasKey('encoders', $config['matcher']);
        $this->assertSame(['application/vnd.json+api' => 'jsonapi.encoders.jsonapi'], $config['matcher']['encoders']);

        $this->assertArrayHasKey('urlPrefix', $config);
        $this->assertSame('/myapp', $config['urlPrefix']);
    }

    /**
     * @test
     */
    public function shouldLoadConfigurationOnlyIfAppropriateAnnotationExist()
    {
        $controller = new AnotherController();

        $event = $this->createEvent([$controller, 'testAction']);
        $this->listener->onKernelController($event);
        $this->assertFalse($event->getRequest()->attributes->has('_jsonapi'));

        $anotherEvent = $this->createEvent([$controller, 'anotherAction']);
        $this->listener->onKernelController($anotherEvent);
        $this->assertTrue($anotherEvent->getRequest()->attributes->has('_jsonapi'));
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->listener = new ApiListener(new AnnotationReader());
    }

    /**
     * @param callable $controller
     * @return FilterControllerEvent
     */
    private function createEvent(callable $controller)
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();

        return new FilterControllerEvent($kernel, $controller, new Request(), HttpKernelInterface::MASTER_REQUEST);
    }
}
