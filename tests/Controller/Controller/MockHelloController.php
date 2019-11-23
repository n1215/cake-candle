<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Controller\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;

class MockHelloController extends Controller
{
    /**
     * @var Response
     */
    public $startupResponse;

    /**
     * @var Response
     */
    public $shutdownResponse;


    public function startupProcess(): ?ResponseInterface
    {
        parent::startupProcess();
        return $this->startupResponse;
    }

    public function shutdownProcess(): ?ResponseInterface
    {
        parent::shutdownProcess();
        return $this->shutdownResponse;
    }

    public function hello(string $name, MockDependency $dependency)
    {
        return $this->response;
    }
}
