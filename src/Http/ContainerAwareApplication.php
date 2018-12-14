<?php

namespace N1215\CakeCandle\Http;

use Cake\Http\ActionDispatcher;
use Cake\Http\BaseApplication;
use Cake\Routing\DispatcherFactory;

trait ContainerAwareApplication
{
    /**
     * Get the ActionDispatcher.
     * @return ActionDispatcher
     * @see BaseApplication::getDispatcher()
     */
    protected function getDispatcher()
    {
        $controllerFactory = new ControllerFactory();
        return new ActionDispatcher($controllerFactory, $this->getEventManager(), DispatcherFactory::filters());
    }
}
