<?php

namespace N1215\CakeCandle\Container;

use Psr\Container\ContainerInterface;

final class ContainerBag implements ActionInvokerInterface, ContainerInterface
{
    /**
     * @var ContainerBag
     */
    private static $instance;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * コンストラクタ
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * @param object $controller
     * @param string $action
     * @param array $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function invoke($controller, $action, array $arguments)
    {
        $originalArguments = array_values($arguments);
        $reflectionMethod = new \ReflectionMethod($controller, $action);
        $parameters = $reflectionMethod->getParameters();

        $complementedArguments = [];
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            if ($class !== null) {
                $complementedArguments[] = $this->get($class->getName());
                continue;
            }
            $complementedArguments[] = array_shift($originalArguments);
        }

        return $reflectionMethod->invoke($controller, ...$complementedArguments);
    }

    /**
     * @param ContainerInterface $container
     */
    public static function init(ContainerInterface $container)
    {
        self::$instance = new self($container);
    }

    /**
     * @return ContainerBag
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}
