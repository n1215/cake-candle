<?php

namespace N1215\CakeCandle;

use PHPUnit\Framework\TestCase;

class ContainerBagLocatorTest extends TestCase
{
    public function test_get_throws_exception_when_called_before_init()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('ContainerBagLocator has not been initialized.');

        ContainerBagLocator::get();
    }

    public function test_init_and_get()
    {
        $container = new MockContainer([]);

        ContainerBagLocator::init($container);
        $bag = ContainerBagLocator::get();

        $this->assertInstanceOf(ContainerBagInterface::class, $bag);
    }

    public function test_get_always_returns_same_object()
    {
        $container = new MockContainer([]);
        ContainerBagLocator::init($container);

        $bag = ContainerBagLocator::get();
        $secondBag = ContainerBagLocator::get();

        $this->assertSame($bag, $secondBag);
    }
}
