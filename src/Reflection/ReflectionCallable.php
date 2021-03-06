<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Reflection;

use Closure;
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
        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }

        // callable object
        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new ReflectionMethod($callable, '__invoke');
        }

        // standard function
        if (function_exists($callable)) {
            return new ReflectionFunction($callable);
        }

        // static method
        $parts = explode('::', $callable);
        return new ReflectionMethod($parts[0], $parts[1]);
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
        return ($this->callable)(...$args);
    }
}
