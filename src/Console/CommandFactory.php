<?php

namespace N1215\CakeCandle\Console;

use Cake\Console\Command;
use Cake\Console\Shell;
use InvalidArgumentException;
use N1215\CakeCandle\Container\ContainerBag;

/**
 * This is a factory for creating Command and Shell instances.
 *
 * This factory can be replaced or extended if you need to customize building
 * your command and shell objects.
 */
class CommandFactory extends \Cake\Console\CommandFactory
{
    /**
     * {@inheritDoc}
     * @see \Cake\Console\CommandFactory
     */
    public function create($className)
    {
        $command = ContainerBag::getInstance()->get($className);
        if (!($command instanceof Command) && !($command instanceof Shell)) {
            $valid = implode('` or `', [Shell::class, Command::class]);
            $message = sprintf('Class `%s` must be an instance of `%s`.', $className, $valid);
            throw new InvalidArgumentException($message);
        }

        return $command;
    }
}
