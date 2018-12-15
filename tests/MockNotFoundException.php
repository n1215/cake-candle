<?php

namespace N1215\CakeCandle;

use Psr\Container\NotFoundExceptionInterface;

class MockNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}