<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Invoker\Exceptions\InsufficientArgumentsException;
use N1215\CakeCandle\Invoker\Exceptions\ReflectionFailedException;
use N1215\CakeCandle\Reflection\ReflectionCallable;
use Psr\Container\ContainerInterface;
use ReflectionException;

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
        $complementedArguments = $this->complement($callable, $args);
        return $callable(...$complementedArguments);
    }

    /**
     * @inheritDoc
     */
    public function complement(callable $callable, array $args = []): array
    {
        $originalArguments = array_values($args);
        try {
            $reflectionCallable = new ReflectionCallable($callable);
        } catch (ReflectionException $e) {
            throw new ReflectionFailedException('failed to get reflection of the callable', 0, $e);
        }

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

        return $complementedArguments;
    }
}
