<?php

namespace N1215\CakeCandle\Http;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use N1215\CakeCandle\Container\ContainerBag;
use ReflectionClass;

/**
 * Factory method for building controllers from request/response pairs.
 */
class ControllerFactory extends \Cake\Http\ControllerFactory
{
    /**
     * @inheritdoc
     * @see \Cake\Http\ControllerFactory
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
        $controller = ContainerBag::getInstance()->get($className);
        if (!empty($request->getParam('controller'))) {
            $controller->setName($className);
        }
        $controller->setRequest($request);
        $controller->setResponse($response);

        return $controller;
    }
}
