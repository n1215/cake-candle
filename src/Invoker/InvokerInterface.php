<?php

namespace N1215\CakeCandle\Invoker;

use Psr\Container\ContainerExceptionInterface;
use ReflectionException;

interface InvokerInterface
{
    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function invoke(callable $callable, array $args = []);
}
