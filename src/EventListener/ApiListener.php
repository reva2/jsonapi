<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\EventListener;

use ReflectionClass;
use Reva2\JsonApi\Attributes\Request;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * JSON API listener handle JSON API annotations
 *
 * @package Reva2\JsonApi\EventListener
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ApiListener implements EventSubscriberInterface
{
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
     * @param FactoryInterface $factory
     * @param array $defMatcher
     */
    public function __construct(FactoryInterface $factory, array $defMatcher)
    {
        $this->factory = $factory;
        $this->defMatcher = $defMatcher;
    }

    /**
     * Load JSON API configuration from controller annotations
     *
     * @param ControllerEvent $event
     * @throws \ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $config = null;

        $refClass = new ReflectionClass($controller[0]);
        if (null !== ($attr = $this->getAttribute($refClass->getAttributes(Request::class)))) {
            $config = $attr->toArray();
        }

        $refMethod = $refClass->getMethod($controller[1]);
        if (null !== ($attr = $this->getAttribute($refMethod->getAttributes(Request::class)))) {
            if (null !== $config) {
                $config = array_replace($config, $attr->toArray());
            } else {
                $config = $attr->toArray();
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

    private function getAttribute(array $attrs): ?Request
    {
        if (count($attrs) > 0) {
            return $attrs[0]->newInstance();
        }

        return null;
    }
}
