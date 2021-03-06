<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Console;

use Cake\Console\Command;
use Cake\Console\Shell;
use N1215\CakeCandle\ContainerBagLocator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class CommandFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        ContainerBagLocator::flush();
        ContainerBagLocator::init($this->container);
    }

    /**
     * @param string $className
     * @dataProvider dataProvider_create
     */
    public function test_create($className): void
    {
        $commandFactory = new CommandFactory();

        $this->container->expects($this->once())
            ->method('get')
            ->with($className)
            ->willReturn(new $className);

        $result = $commandFactory->create($className);

        $this->assertInstanceOf($className, $result);
    }

    public function dataProvider_create(): array
    {
        return [
            [HelloCommand::class],
            [HelloShell::class],
        ];
    }

    /**
     * @dataProvider dataProvider_create
     */
    public function test_create_throws_exception_when_non_command_class_given(): void
    {
        $className = Hello::class;
        $commandFactory = new CommandFactory();

        $this->container->expects($this->once())
            ->method('get')
            ->with($className)
            ->willReturn(new $className);

        $this->expectException(\InvalidArgumentException::class);

        $commandFactory->create($className);
    }
}

class HelloCommand extends Command
{
}

class HelloShell extends Shell
{
}

class Hello
{
}
