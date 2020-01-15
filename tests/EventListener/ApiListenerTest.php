<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\EventListener\ApiListener;
use Reva2\JsonApi\Factories\Factory;
use Reva2\JsonApi\Tests\Fixtures\Controllers\AnotherController;
use Reva2\JsonApi\Tests\Fixtures\Controllers\PetsController;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetDocument;
use Reva2\JsonApi\Tests\Fixtures\Query\PetQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test for JSON API event listener
 *
 * @package Reva2\JsonApi\Tests\EventListener
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ApiListenerTest extends TestCase
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

        $environment = $request->attributes->get('_jsonapi');
        $this->assertInstanceOf(EnvironmentInterface::class, $environment);

        /* @var $environment EnvironmentInterface */

        $this->assertSame(PetQuery::class, $environment->getQueryType());
        $this->assertSame(PetDocument::class, $environment->getBodyType());

        $matcherConfig = [
            'encoders' => ['application/vnd.json+api' => 'jsonapi.encoders.jsonapi'],
            'decoders' => ['application/vnd.json+api' => 'jsonapi.decoders.jsonapi']
        ];
        $this->assertSame($matcherConfig, $environment->getMatcherConfiguration());

        $this->assertSame('/myapp', $environment->getUrlPrefix());
        $this->assertSame(['Default', 'Create'], $environment->getValidationGroups());
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
    protected function setUp(): void
    {
        $this->listener = new ApiListener(
            new AnnotationReader(),
            new Factory(),
            [
                'decoders' => ['application/vnd.json+api' => 'jsonapi'],
                'encoders' => ['application/vnd.json+api' => 'jsonapi']
            ]
        );
    }

    /**
     * @param callable $controller
     * @return \Symfony\Component\HttpKernel\Event\FilterControllerEvent | \Symfony\Component\HttpKernel\Event\ControllerEvent depends on symfony/http-kernel version
     */
    private function createEvent(callable $controller)
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();

        if (class_exists('Symfony\Component\HttpKernel\Event\FilterControllerEvent')) {
            return new \Symfony\Component\HttpKernel\Event\FilterControllerEvent($kernel, $controller, new Request(), HttpKernelInterface::MASTER_REQUEST);
        } else {
            return new \Symfony\Component\HttpKernel\Event\ControllerEvent($kernel, $controller, new Request(), HttpKernelInterface::MASTER_REQUEST);
        }
    }
}
