<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Invoker\Exceptions\InvocationException;
use Psr\Container\ContainerExceptionInterface;

interface InvokerInterface
{
    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws InvocationException
     */
    public function invoke(callable $callable, array $args = []);

    /**
     * @param callable $callable
     * @param array $args
     * @return array
     * @throws InvocationException
     */
    public function complement(callable $callable, array $args = []): array;
}
