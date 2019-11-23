<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Console;

use Cake\Console\CommandFactoryInterface;
use Cake\Console\CommandInterface;
use Cake\Console\Shell;
use InvalidArgumentException;
use N1215\CakeCandle\ContainerBagLocator;

/**
 * This is a factory for creating Command and Shell instances.
 */
final class CommandFactory implements CommandFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($className)
    {
        $command = ContainerBagLocator::get()->get($className);
        if (!($command instanceof CommandInterface) && !($command instanceof Shell)) {
            /** @psalm-suppress DeprecatedClass */
            $valid = implode('` or `', [Shell::class, CommandInterface::class]);
            $message = sprintf('Class `%s` must be an instance of `%s`.', $className, $valid);
            throw new InvalidArgumentException($message);
        }

        return $command;
    }
}
