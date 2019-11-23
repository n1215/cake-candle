<?php
declare(strict_types=1);

namespace N1215\CakeCandle\Controller\Controller;

use Cake\Controller\Controller;

class MockHelloController extends Controller
{
    public function hello($name, MockDependency $dependency)
    {
        return $this->response;
    }
}
