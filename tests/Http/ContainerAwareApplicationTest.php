<?php

namespace N1215\CakeCandle\Http;

use Cake\Http\ActionDispatcher;
use PHPUnit\Framework\TestCase;

class ContainerAwareApplicationTest extends TestCase
{
    public function test_getDispatcher()
    {
        $app = new MockApplication();
        $result = $app->getInnerDispatcher();
        $this->assertInstanceOf(ActionDispatcher::class, $result);
        $this->assertEquals($app->getEventManager(), $result->getEventManager());
    }
}
