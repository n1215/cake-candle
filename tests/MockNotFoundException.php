<?php
declare(strict_types=1);

namespace N1215\CakeCandle;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class MockNotFoundException extends Exception implements NotFoundExceptionInterface
{
}