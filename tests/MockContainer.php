<?php
declare(strict_types=1);

namespace N1215\CakeCandle;

use Psr\Container\ContainerInterface;

class MockContainer implements ContainerInterface
{
    private $factories = [];

    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new MockNotFoundException($this->createNotFoundMessage($id));
        }
        $factory = $this->factories[$id];
        return $factory();
    }

    public function createNotFoundMessage($id)
    {
        return "entry {$id} not found";
    }

    public function has($id)
    {
        return array_key_exists($id, $this->factories);
    }
}
