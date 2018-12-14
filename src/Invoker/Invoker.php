<?php

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Reflection\ReflectionCallable;
use Psr\Container\ContainerInterface;

final class Invoker implements InvokerInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function invoke(callable $callable, array $args = [])
    {
        $originalArguments = array_values($args);
        $reflectionCallable = new ReflectionCallable($callable);
        $parameters = $reflectionCallable->getParameters();

        $complementedArguments = [];
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            if ($class === null) {
                $complementedArguments[] = array_shift($originalArguments);
                continue;
            }

            $complementedArguments[] = $this->container->get($class->getName());
        }

        return $reflectionCallable->invoke($complementedArguments);
    }
}
