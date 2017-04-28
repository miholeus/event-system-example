<?php

namespace TaskBundle\Event\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use TaskBundle\Annotation\ActionBreadcrumb;
use TaskBundle\Menu\Breadcrumb;
use TaskBundle\Service\BreadcrumbsService;

/**
 * Class BreadcrumbAnnotationListener
 *
 * Listens to "ActionBreadcrumb" annotations on controller actions
 * and registers corresponding breadcrumb in the BreadcrumbService
 * Breadcrumb is also passed to the controller in Request
 */
class BreadcrumbAnnotationListener
{

    private $reader;
    private $breadcrumbsService;

    public function __construct(Reader $reader, BreadcrumbsService $breadcrumbsService)
    {
        $this->reader = $reader;
        $this->breadcrumbsService = $breadcrumbsService;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }
        /** @var Controller $controllerObject */
        list($controllerObject, $methodName) = $controller;
        /** @var ActionBreadcrumb $actionBreadcrumbAnnotation */
        $actionBreadcrumbAnnotation = $this->reader->getMethodAnnotation(
            new \ReflectionMethod(ClassUtils::getClass($controllerObject), $methodName),
            ActionBreadcrumb::class
        );
        if ($actionBreadcrumbAnnotation) {
            /** @var Breadcrumb $breadcrumb */
            $breadcrumb = Breadcrumb::create($actionBreadcrumbAnnotation->label['value'], $event->getRequest()->getUri());
            $this->breadcrumbsService->registerAppendedBreadcrumb($breadcrumb);
            $event->getRequest()->attributes->set(BreadcrumbsService::REQUEST_BREADCRUMB_KEY, $breadcrumb);
        }
    }
}
