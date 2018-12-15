<?php

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Invoker\Exceptions\InvocationException;
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
     * @throws InvocationException
     */
    public function invoke(callable $callable, array $args = []);
}
