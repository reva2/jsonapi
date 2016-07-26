<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\EventListener;

use Doctrine\Common\Annotations\Reader;
use Reva2\JsonApi\Annotations\ApiRequest;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * JSON API listener handle JSON API annotations
 *
 * @package Reva2\JsonApi\EventListener
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ApiListener implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Default matcher configuration
     *
     * @var array
     */
    protected $defMatcher;

    /**
     * Constructor
     *
     * @param Reader $reader
     * @param FactoryInterface $factory
     * @param array $defMatcher
     */
    public function __construct(Reader $reader, FactoryInterface $factory, array $defMatcher)
    {
        $this->reader = $reader;
        $this->factory = $factory;
        $this->defMatcher = $defMatcher;
    }

    /**
     * Load JSON API configuration from controller annotations
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $config = null;

        $refClass = new \ReflectionClass($controller[0]);
        if (null !== ($annotation = $this->reader->getClassAnnotation($refClass, ApiRequest::class))) {
            /* @var $annotation ApiRequest */
            $config = $annotation->toArray();
        }

        $refMethod = $refClass->getMethod($controller[1]);
        if (null !== ($annotation = $this->reader->getMethodAnnotation($refMethod, ApiRequest::class))) {
            if (null !== $config) {
                $config = array_replace($config, $annotation->toArray());
            } else {
                $config = $annotation->toArray();
            }
        }

        if (null !== $config) {
            if (!array_key_exists('matcher', $config)) {
                $config['matcher'] = $this->defMatcher;
            }

            $event->getRequest()->attributes->set('_jsonapi', $this->factory->createEnvironment($config));
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}
