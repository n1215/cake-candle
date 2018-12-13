<?php

namespace N1215\CakeCandle\Http;

use Cake\Http\ActionDispatcher;
use Cake\Http\BaseApplication;
use Cake\Routing\DispatcherFactory;

/**
 * Trait ContainerAwareApplication
 */
trait ContainerAwareApplication
{
    /**
     * Get the ActionDispatcher.
     * @return ActionDispatcher
     * @see BaseApplication
     */
    protected function getDispatcher()
    {
        $controllerFactory = new ControllerFactory();
        return new ActionDispatcher($controllerFactory, $this->getEventManager(), DispatcherFactory::filters());
    }
}
