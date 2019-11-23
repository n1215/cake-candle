<?php
declare(strict_types=1);

namespace N1215\CakeCandle;

use LogicException;
use PHPUnit\Framework\TestCase;

class ContainerBagLocatorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ContainerBagLocator::flush();
    }

    public function test_init_and_get(): void
    {
        $container = new MockContainer([]);

        ContainerBagLocator::init($container);
        $bag = ContainerBagLocator::get();

        $this->assertInstanceOf(ContainerBagInterface::class, $bag);
    }

    public function test_get_always_returns_same_object(): void
    {
        $container = new MockContainer([]);
        ContainerBagLocator::init($container);

        $bag = ContainerBagLocator::get();
        $secondBag = ContainerBagLocator::get();

        $this->assertSame($bag, $secondBag);
    }

    public function test_get_throws_exception_when_called_before_init(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('ContainerBagLocator has not been initialized.');

        ContainerBagLocator::get();
    }

    public function test_init_throws_exception_when_called_twice(): void
    {
        $container = new MockContainer([]);
        ContainerBagLocator::init($container);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('ContainerBagLocator has already been initialized.');

        ContainerBagLocator::init($container);
    }
}
