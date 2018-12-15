<?php

namespace N1215\CakeCandle\Http;

use Cake\Controller\Controller;
use Cake\Controller\Exception\MissingActionException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use N1215\CakeCandle\ContainerBagLocator;
use N1215\CakeCandle\MockContainer;
use PHPUnit\Framework\TestCase;

class AssistedActionTest extends TestCase
{
    public function test_invokeAction()
    {
        $container = new MockContainer([
            Hoge::class => function () { return new Hoge(); },
        ]);
        ContainerBagLocator::flush();
        ContainerBagLocator::init($container);

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'Hello')
            ->withParam('action', 'hello')
            ->withParam('pass', ['taro']);

        $response = new Response();
        $controller = new HelloController($request, $response);

        $result = $controller->invokeAction();
        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_invokeAction_throws_exception_when_request_not_set()
    {
        $container = new MockContainer([
            Hoge::class => function () { return new Hoge(); },
        ]);
        ContainerBagLocator::flush();
        ContainerBagLocator::init($container);

        $controller = new HelloController();
        $controller->request = null;

        $this->expectException(\LogicException::class);

        $controller->invokeAction();
    }

    public function test_invokeAction_throws_exception_when_action_not_found()
    {
        $container = new MockContainer([
            Hoge::class => function () { return new Hoge(); },
        ]);
        ContainerBagLocator::flush();
        ContainerBagLocator::init($container);

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'Hello')
            ->withParam('action', 'goodbye')
            ->withParam('pass', ['taro']);

        $response = new Response();
        $controller = new HelloController($request, $response);

        $this->expectException(MissingActionException::class);

        $controller->invokeAction();
    }
}

class Hoge
{
}

class HelloController extends Controller
{
    use AssistedAction;

    public function hello($name, Hoge $hoge)
    {
        return new Response();
    }
}
