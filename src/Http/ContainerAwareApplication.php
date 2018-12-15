<?php

namespace N1215\CakeCandle\Http;

use Cake\Event\EventManager;
use Cake\Http\ActionDispatcher;
use Cake\Http\BaseApplication;
use Cake\Routing\DispatcherFactory;
use N1215\CakeCandle\ContainerBagLocator;

trait ContainerAwareApplication
{
    /**
     * @return EventManager
     * @see BaseApplication::getEventManager()
     */
    abstract public function getEventManager();

    /**
     * Get the ActionDispatcher.
     * @return ActionDispatcher
     * @see BaseApplication::getDispatcher()
     */
    protected function getDispatcher()
    {
        $controllerFactory = new ControllerFactory(ContainerBagLocator::get());
        return new ActionDispatcher($controllerFactory, $this->getEventManager(), DispatcherFactory::filters());
    }
}
