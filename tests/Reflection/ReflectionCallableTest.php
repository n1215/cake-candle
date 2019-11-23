<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Reflection;

use PHPUnit\Framework\TestCase;
use ReflectionException;

class ReflectionCallableTest extends TestCase
{
    /**
     * @param callable $callable
     * @dataProvider dataProvider_callables
     */
    public function test_getParameters(callable $callable)
    {
        $reflection = new ReflectionCallable($callable);

        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertInstanceOf(\ReflectionParameter::class, $parameters[0]);
        $this->assertEquals('name', $parameters[0]->getName());
    }

    /**
     * @param callable $callable
     * @dataProvider dataProvider_callables
     */
    public function test_invoke(callable $callable)
    {
        $reflection = new ReflectionCallable($callable);

        $result = $reflection->invoke(['world']);

        $this->assertEquals('Hello, world!', $result);
    }

    public function dataProvider_callables()
    {
        return [
            ['\N1215\CakeCandle\Reflection\helloFunc'],
            [[Hello::class, 'staticMethod']],
            [[new Hello(), 'instanceMethod']],
            ['\N1215\CakeCandle\Reflection\Hello::staticMethod'],
            [new Hello()],
            [
                function($name) {
                    return "Hello, {$name}!";
                }
            ],
        ];
    }

    public function test_constructor_throws_exception_when_reflection_failed()
    {
        $this->expectException(ReflectionException::class);
        new ReflectionCallable([HelloExtended::class, 'parent::staticMethod']);
    }
}

function helloFunc($name) {
    return "Hello, {$name}!";
}

class Hello {

    public function __invoke($name)
    {
        return "Hello, {$name}!";
    }

    public function instanceMethod($name)
    {
        return "Hello, {$name}!";
    }

    public static function staticMethod($name)
    {
        return "Hello, {$name}!";
    }
}

class HelloExtended extends Hello
{
    public static function staticMethod($name)
    {
        return "Hello, Hello, {$name}!!";
    }
}
