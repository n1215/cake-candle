<?php

namespace N1215\CakeCandle;

use N1215\CakeCandle\Invoker\Invoker;
use Psr\Container\ContainerInterface;

final class ContainerBagLocator
{
    /**
     * @var ContainerBagInterface
     */
    private static $bag;

    /**
     * @param ContainerInterface $container
     */
    public static function init(ContainerInterface $container)
    {
        self::$bag = new ContainerBag($container, new Invoker($container));
    }

    /**
     * @return ContainerBagInterface
     */
    public static function get()
    {
        if (self::$bag === null) {
            throw new \LogicException('ContainerBagLocator has not been initialized.');
        }

        return self::$bag;
    }
}
