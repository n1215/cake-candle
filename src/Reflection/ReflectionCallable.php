<?php

namespace N1215\CakeCandle\Reflection;

use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Class ReflectionCallable
 * @package N1215\CakeCandle\Reflection
 */
final class ReflectionCallable
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var ReflectionFunctionAbstract
     */
    private $reflectionFunctionAbstract;

    /**
     * コンストラクタ
     * @param callable $callable
     * @throws ReflectionException
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->reflectionFunctionAbstract = $this->createReflectionFunctionAbstract($callable);
    }

    /**
     * @param callable $callable
     * @return ReflectionFunctionAbstract
     * @throws ReflectionException
     */
    private function createReflectionFunctionAbstract(callable $callable)
    {
        // array
        if (is_array($callable)) {
            list($class, $method) = $callable;
            return new ReflectionMethod($class, $method);
        }

        // closure
        if ($callable instanceof \Closure) {
            return new ReflectionFunction($callable);
        }

        // callable object
        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new ReflectionMethod($callable, '__invoke');
        }

        if (is_string($callable)) {

            // standard function
            if (\function_exists($callable)) {
                return new ReflectionFunction($callable);
            }

            // static method
            $parts = explode('::', $callable);
            if (count($parts) === 2) {
                return new ReflectionMethod($parts[0], $parts[1]);
            }
        }

        throw new ReflectionException('failed to reflect the callable.');
    }

    /**
     * @return ReflectionParameter[]
     */
    public function getParameters()
    {
        return $this->reflectionFunctionAbstract->getParameters();
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function invoke(array $args = [])
    {
        $callable = $this->callable;
        return $callable(...$args);
    }
}
