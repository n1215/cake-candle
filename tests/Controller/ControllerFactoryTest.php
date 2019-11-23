<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Controller;

use Cake\Controller\Exception\MissingActionException;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Http\Exception\MissingControllerException;
use N1215\CakeCandle\ContainerBag;
use N1215\CakeCandle\Controller\Controller\MockDependency;
use N1215\CakeCandle\Controller\Controller\MockHelloController;
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

    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.namespace', 'N1215\CakeCandle\Controller');
        $this->helloController = new MockHelloController();
        $container = new MockContainer([
            MockHelloController::class => function () {
                return $this->helloController;
            }
        ]);
        $this->controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));
    }

    public function test_create(): void
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello');

        $result = $this->controllerFactory->create($request);

        $this->assertSame($this->helloController, $result);
        $this->assertSame($request, $result->getRequest());
        $this->assertSame('MockHello', $result->getName());
    }

    public function test_create_throws_exception_when_cannot_resolve_controller_name(): void
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/goodby/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockGoodBy');

        $this->expectException(MissingControllerException::class);

        $this->controllerFactory->create($request);
    }

    public function test_create_throws_exception_when_resolved_controller_is_abstract(): void
    {
        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/abstract/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockAbstract');

        $this->expectException(MissingControllerException::class);

        $this->controllerFactory->create($request);
    }

    public function test_create_throws_exception_when_cannot_resolve_controller_object(): void
    {
        $container = new MockContainer([]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello');

        $this->expectException(NotFoundExceptionInterface::class);

        $controllerFactory->create($request);
    }


    public function test_invoke(): void
    {
        $container = new MockContainer([
            MockDependency::class => function () { return new MockDependency(); },
        ]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello')
            ->withParam('action', 'hello')
            ->withParam('pass', ['taro']);
        $response = new Response();
        $controller = new MockHelloController($request, $response);

        $result = $controllerFactory->invoke($controller);
        $this->assertSame($response, $result);
    }

    public function test_invoke_and_return_startup_response(): void
    {
        $container = new MockContainer([
            MockDependency::class => function () { return new MockDependency(); },
        ]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello')
            ->withParam('action', 'hello')
            ->withParam('pass', ['taro']);
        $response = new Response();
        $startupResponse = new Response();
        $controller = new MockHelloController($request, $response);
        $controller->startupResponse = $startupResponse;

        $result = $controllerFactory->invoke($controller);

        $this->assertSame($startupResponse, $result);
    }

    public function test_invoke_and_return_shutdown_response(): void
    {
        $container = new MockContainer([
            MockDependency::class => function () { return new MockDependency(); },
        ]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello')
            ->withParam('action', 'hello')
            ->withParam('pass', ['taro']);
        $response = new Response();
        $shutdownResponse = new Response();
        $controller = new MockHelloController($request, $response);
        $controller->shutdownResponse = $shutdownResponse;

        $result = $controllerFactory->invoke($controller);

        $this->assertSame($shutdownResponse, $result);
    }

    public function test_invoke_throws_exception_when_action_not_found(): void
    {
        $container = new MockContainer([
            MockDependency::class => function () { return new MockDependency(); },
        ]);
        $controllerFactory = new ControllerFactory(new ContainerBag($container, new Invoker($container)));

        $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/hello/taro',]);
        $request = (new ServerRequest([]))
            ->withUri($uri)
            ->withParam('controller', 'MockHello')
            ->withParam('action', 'goodbye')
            ->withParam('pass', ['taro']);
        $response = new Response();
        $controller = new MockHelloController($request, $response);
        $this->expectException(MissingActionException::class);
        $controllerFactory->invoke($controller);
    }
}

