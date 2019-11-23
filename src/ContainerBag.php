<?php
declare(strict_types=1);

namespace N1215\CakeCandle;

use N1215\CakeCandle\Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;

final class ContainerBag implements ContainerBagInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var InvokerInterface
     */
    private $invoker;

    public function __construct(ContainerInterface $container, InvokerInterface $invoker)
    {
        $this->container = $container;
        $this->invoker = $invoker;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @param callable $callable
     * @param array $args
     * @return mixed
     * @throws Invoker\Exceptions\InvocationException
     */
    public function call(callable $callable, array $args = [])
    {
        return $this->invoker->invoke($callable, $args);
    }

    /**
     * @param callable $callable
     * @param array $args
     * @return array
     * @throws Invoker\Exceptions\InvocationException
     */
    public function complement(callable $callable, array $args = []): array
    {
        return $this->invoker->complement($callable, $args);
    }
}
