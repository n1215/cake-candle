<?php
declare(strict_types=1);

namespace N1215\CakeCandle;

use Psr\Container\ContainerInterface;

interface ContainerBagInterface extends ContainerInterface
{
    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws Invoker\Exceptions\InvocationException
     */
    public function call(callable $callable, array $args = []);

    /**
     * @param callable $callable
     * @param array $args
     * @return array
     * @throws Invoker\Exceptions\InvocationException
     */
    public function complement(callable $callable, array $args = []): array;
}
