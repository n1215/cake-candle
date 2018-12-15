<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Http;

use Cake\Event\EventManager;

class MockApplication
{
    use ContainerAwareApplication;

    public function getEventManager()
    {
        return new EventManager();
    }

    public function getInnerDispatcher()
    {
        return $this->getDispatcher();
    }
}
