<?php

namespace N1215\CakeCandle\Http\Controller;

use Cake\Controller\Controller;
use N1215\CakeCandle\Http\AssistedAction;

class MockHelloController extends Controller
{
    use AssistedAction;

    public function hello($name, MockDependency $dependency)
    {
        return $this->response;
    }
}
