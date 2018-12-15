<?php

namespace N1215\CakeCandle\Http;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use N1215\CakeCandle\ContainerBagInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;

/**
 * Factory method for building controllers from request/response pairs.
 */
class ControllerFactory extends \Cake\Http\ControllerFactory
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
     */
    public function create(ServerRequest $request, Response $response)
    {
        $className = $this->getControllerClass($request);

        if (!$className) {
            $this->missingController($request);
        }
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            $this->missingController($request);
        }

        /** @var Controller $controller */
        try {
            $controller = $this->containerBag->get($className);
        } catch (ContainerExceptionInterface $e) {
            throw $e;
        }

        $controller->setRequest($request);
        $controller->setResponse($response);

        return $controller;
    }
}
