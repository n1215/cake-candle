<?php

namespace N1215\CakeCandle\Http;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Exception\MissingControllerException;
use N1215\CakeCandle\ContainerBag;
use N1215\CakeCandle\Http\Controller\MockHelloController;
use N1215\CakeCandle\Invoker\Invoker;
use N1215\CakeCandle\MockContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class ControllerFactoryTest extends TestCase
{
    /**
     * @var MockHelloController
     */
    private $helloController;

    /**
     * @var ControllerFactory
     */
    private $controllerFactory;

    public function setUp()
    {
        parent::setUp();

        Configure::write('App.namespace', 'N1215\CakeCandle\Http');
        $this->helloController = new MockHelloController();
        $container = new MockContainer([
            MockHelloController::class => function () {
                return $this->helloController;
            }
        ]);
        $this->controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));
    }

    public function test_create()
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello');
        $response = new Response();

        $result = $this->controllerFactory->create($request, $response);

        $this->assertSame($this->helloController, $result);
        $this->assertSame($request, $result->getRequest());
        $this->assertSame($response, $result->getResponse());
        $this->assertSame('MockHello', $result->getName());
    }

    public function test_create_throws_exception_when_cannot_resolve_controller_name()
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/goodby/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockGoodBy');
        $response = new Response();

        $this->expectException(MissingControllerException::class);

        $this->controllerFactory->create($request, $response);
    }

    public function test_create_throws_exception_when_resolved_controller_is_abstract()
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/abstract/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockAbstract');
        $response = new Response();

        $this->expectException(MissingControllerException::class);

        $this->controllerFactory->create($request, $response);
    }

    public function test_create_throws_exception_when_resolved_controller_is_interface()
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/interface/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockInterface');
        $response = new Response();

        $this->expectException(MissingControllerException::class);

        $this->controllerFactory->create($request, $response);
    }

    public function test_create_throws_exception_when_cannot_resolve_controller_object()
    {
        $container = new MockContainer([]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello');
        $response = new Response();

        $this->expectException(NotFoundExceptionInterface::class);

        $controllerFactory->create($request, $response);
    }
}

