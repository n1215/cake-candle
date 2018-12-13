<?php

namespace N1215\CakeCandle\Container;

interface ActionInvokerInterface
{
    /**
     * @param object $controller
     * @param string $action
     * @param array $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function invoke($controller, $action, array $arguments);
}
