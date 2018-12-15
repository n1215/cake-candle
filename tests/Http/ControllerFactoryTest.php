<?php

namespace N1215\CakeCandle\Http {

    use App\Controller\HelloController;
    use Cake\Core\Configure;
    use Cake\Http\Response;
    use Cake\Http\ServerRequest;
    use Cake\Http\ServerRequestFactory;
    use Cake\Routing\Exception\MissingControllerException;
    use N1215\CakeCandle\ContainerBag;
    use N1215\CakeCandle\Invoker\Invoker;
    use N1215\CakeCandle\MockContainer;
    use PHPUnit\Framework\TestCase;
    use Psr\Container\ContainerExceptionInterface;
    use Psr\Container\NotFoundExceptionInterface;

    class ControllerFactoryTest extends TestCase
    {
        /**
         * @var HelloController
         */
        private $helloController;

        /**
         * @var ControllerFactory
         */
        private $controllerFactory;

        public function setUp()
        {
            parent::setUp();

            Configure::write('App.namespace', 'App');
            $this->helloController = new HelloController();
            $container = new MockContainer([
                HelloController::class => function () {
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
                ->withParam('controller', 'Hello');
            $response = new Response();

            $result = $this->controllerFactory->create($request, $response);

            $this->assertSame($this->helloController, $result);
            $this->assertSame($request, $result->getRequest());
            $this->assertSame($response, $result->getResponse());
            $this->assertSame('Hello', $result->getName());
        }

        public function test_create_throws_exception_when_cannot_resolve_controller_name()
        {
            $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/goodby/taro',]);
            $request = (new ServerRequest([]))
                ->withUri($uri)
                ->withParam('controller', 'GoodBy');
            $response = new Response();

            $this->expectException(MissingControllerException::class);

            $this->controllerFactory->create($request, $response);
        }

        public function test_create_throws_exception_when_resolved_controller_is_abstract()
        {
            $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/abstract/taro',]);
            $request = (new ServerRequest([]))
                ->withUri($uri)
                ->withParam('controller', 'Abstract');
            $response = new Response();

            $this->expectException(MissingControllerException::class);

            $this->controllerFactory->create($request, $response);
        }

        public function test_create_throws_exception_when_resolved_controller_is_interface()
        {
            $uri = ServerRequestFactory::createUri(['PATH_INFO' => '/interface/taro',]);
            $request = (new ServerRequest([]))
                ->withUri($uri)
                ->withParam('controller', 'Interface');
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
                ->withParam('controller', 'Hello');
            $response = new Response();

            $this->expectException(NotFoundExceptionInterface::class);

            $controllerFactory->create($request, $response);
        }
    }
}

namespace App\Controller {

    use Cake\Controller\Controller;

    class HelloController extends Controller
    {
    }

    abstract class AbstractController {}

    interface InterfaceController {}
}
