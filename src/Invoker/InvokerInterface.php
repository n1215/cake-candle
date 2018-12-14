<?php

namespace N1215\CakeCandle\Invoker;

interface InvokerInterface
{
    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     */
    public function invoke(callable $callable, array $args = []);
}
