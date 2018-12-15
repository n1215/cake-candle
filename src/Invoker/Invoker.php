<?php

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Invoker\Exceptions\InsufficientArgumentsException;
use N1215\CakeCandle\Reflection\ReflectionCallable;
use Psr\Container\ContainerInterface;

final class Invoker implements InvokerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
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
            if ($class !== null) {
                $complementedArguments[] = $this->container->get($class->getName());
                continue;
            }

            if (count($originalArguments) === 0) {
                $position = $parameter->getPosition() + 1;
                $name = $parameter->getName();
                throw new InsufficientArgumentsException(
                    "Unable to fill the argument {$position} `{$name}`."
                );
            }

            $complementedArguments[] = array_shift($originalArguments);
        }

        return $reflectionCallable->invoke($complementedArguments);
    }
}
