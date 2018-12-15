<?php

namespace N1215\CakeCandle;

use N1215\CakeCandle\Invoker\InvokerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerBagTest extends TestCase
{
    /**
     * @var ContainerInterface|MockObject
     */
    private $container;

    /**
     * @var InvokerInterface|MockObject
     */
    private $invoker;

    /**
     * @var ContainerBag
     */
    private $containerBag;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->invoker = $this->createMock(InvokerInterface::class);
        $this->containerBag = new ContainerBag(
            $this->container,
            $this->invoker
        );
    }

    public function test_get()
    {
        $id = Hello::class;
        $hello = new Hello();

        $this->container->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($hello);

        $result = $this->containerBag->get($id);

        $this->assertSame($hello, $result);
    }

    public function test_has()
    {
        $id = Hello::class;

        $this->container->expects($this->once())
            ->method('has')
            ->with($id)
            ->willReturn(true);

        $result = $this->containerBag->has($id);

        $this->assertTrue($result);
    }

    public function test_call()
    {
        $hello = new Hello();
        $callable = function ($name, Hello $hello) {
            return $hello->to($name);
        };
        $args = ['World'];

        $this->invoker->expects($this->once())
            ->method('invoke')
            ->with($callable, $args)
            ->willReturn($callable('World', $hello));

        $result = $this->containerBag->call($callable, $args);

        $this->assertEquals('Hello, World!', $result);
    }
}

class Hello
{
    public function to($name)
    {
        return "Hello, {$name}!";
    }
}
