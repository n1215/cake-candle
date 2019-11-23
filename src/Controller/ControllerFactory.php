<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Controller;

use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use N1215\CakeCandle\ContainerBagInterface;
use N1215\CakeCandle\Invoker\Exceptions\InvocationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Factory method for building controllers for request.
 */
class ControllerFactory extends \Cake\Controller\ControllerFactory
{
    /**
     * @var ContainerBagInterface
     */
    private $containerBag;

    /**
     * @param ContainerBagInterface $containerBag
     */
    public function __construct(ContainerBagInterface $containerBag)
    {
        $this->containerBag = $containerBag;
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function create(ServerRequestInterface $request): Controller
    {
        assert($request instanceof ServerRequest);
        $className = $this->getControllerClass($request);
        if (!$className) {
            $this->missingController($request);
        }

        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract()) {
            $this->missingController($request);
        }

        /** @var Controller $controller */
        $controller = $this->containerBag->get($className);

        $controller->setRequest($request);

        return $controller;
    }

    /**
     * @inheritDoc
     * @throws InvocationException
     */
    public function invoke($controller): ResponseInterface
    {
        $result = $controller->startupProcess();
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $action = $controller->getAction();
        $args = array_values($controller->getRequest()->getParam('pass'));

        $args = $this->containerBag->complement($action, $args);

        $controller->invokeAction($action, $args);

        $result = $controller->shutdownProcess();
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return $controller->getResponse();
    }
}
