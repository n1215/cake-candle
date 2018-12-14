<?php

namespace N1215\CakeCandle;

use Psr\Container\ContainerInterface;

interface ContainerBagInterface extends ContainerInterface
{
    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     */
    public function call(callable $callable, array $args = []);
}
