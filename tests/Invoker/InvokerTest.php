<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Invoker;

use N1215\CakeCandle\Invoker\Exceptions\InsufficientArgumentsException;
use N1215\CakeCandle\Invoker\Exceptions\ReflectionFailedException;
use N1215\CakeCandle\MockContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class InvokerTest extends TestCase
{
    /**
     * @var MockContainer
     */
    private $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new MockContainer([
            Chiba::class => function () { return new Chiba(); },
            Shiga::class => function () { return new Shiga(); }
        ]);
    }

    public function test_invoke()
    {
        $invoker = new Invoker($this->container);

        $func = function($name, Chiba $chiba, Shiga $shiga) {
            return $name;
        };

        $result = $invoker->invoke($func, ['World']);

        $this->assertEquals('World', $result);
    }

    public function test_invoke_throws_exception_when_insufficient_parameters_given()
    {
        $invoker = new Invoker($this->container);
        $callable = function($firstName, Chiba $chiba, $lastName, Shiga $shiga) {
            return "{$firstName} {$lastName}";
        };

        $this->expectException(InsufficientArgumentsException::class);
        $this->expectExceptionMessage('Unable to fill the argument 3 `lastName`.');

        $invoker->invoke($callable, ['World']);
    }

    public function test_invoke_throws_exception_when_failed_to_get_object_from_container()
    {
        $invoker = new Invoker($this->container);
        $callable = function($name, Chiba $chiba, Shiga $shiga, Saga $saga) {
            return $name;
        };

        $this->expectException(NotFoundExceptionInterface::class);
        $this->expectExceptionMessage($this->container->createNotFoundMessage(Saga::class));

        $invoker->invoke($callable, ['World']);
    }

    public function test_invoke_throws_exception_when_callable_reflection_failed()
    {
        $invoker = new Invoker($this->container);
        $callable = [SagaExtended::class, 'parent::hello'];

        $this->expectException(ReflectionFailedException::class);

        $invoker->invoke($callable, ['World']);
    }
}

class Chiba
{
}

class Shiga
{
}

class Saga
{
    public static function hello()
    {
        return 'hello';
    }
}

class SagaExtended extends Saga
{
}