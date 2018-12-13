# CakeCandle
A PSR-11 compatible dependency injection plugin for CakePHP3.

# Install

```
composer require n1215/cake-candle
```

# Usage

### Install your PSR-11 container
You can use php-di/php-di for example.
```
composer require php-di/php-di
```

### Change your Application class

```php
<?php

namespace App;

// ...
use Cake\Http\BaseApplication;
// ...
use DI\ContainerBuilder;
use N1215\CakeCandle\Container\ContainerBag;
use N1215\CakeCandle\Http\ContainerAwareApplication;
// ...

class Application extends BaseApplication
{
    // 1. use ContainerAwareApplication trait.
    use ContainerAwareApplication;

    // 2. configure a PSR-11 compatible container as you like.
    private function configureContainer()
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAnnotations(true);
        return $builder->build();
    }

    // 3. initialize ContainerBag with the configured container in Application::bootstrap().
    public function bootstrap()
    {
        try {
            $container = $this->configureContainer();
            ContainerBag::init($container);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to configure the di container.', 0, $e);
        }

        // Call parent to load bootstrap from files.
        parent::bootstrap();

        // ...
    }

    // ...
}
```

### Create dependency

```php
<?php

namespace App;

class GreetingService
{
    public function hello(string $name)
    {
        return "Hello, {$name}";
    }
}
```

## Inject into Controller

### Assisted injection (Method injection)
Extends AppController and use N1215\CakeCandle\Http\AssistedAction trait.
This trait can fill type declared parameters when action methods are invoked.

```php
<?php

namespace App\Controller;

use App\GreetingService;
use N1215\CakeCandle\Http\AssistedAction;

class HelloController extends AppController
{
    use AssistedAction;

    public function index(string $name, GreetingService $greetingService)
    {
        $suffix = $this->request->getQuery('suffix', 'san');
        $this->response
            ->getBody()
            ->write($greetingService->hello($name . ' ' . $suffix));

        return $this->response;
    }
}

```


## Inject into Console Command

### Constructor injection

Extends \Cake\Console\Command as usual.
Type declared parameters are filled using your container.

```php
<?php

namespace App\Command;

use App\GreetingService;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class HelloCommand extends Command
{
    /**
     * @var GreetingService
     */
    private $greetingService;

    public function __construct(GreetingService $greetingService)
    {
        parent::__construct();
        $this->greetingService = $greetingService;
    }

    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->addArgument('name', [
            'help' => 'name'
        ]);
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $name = $args->getArgument('name');
        $io->out($this->greetingService->hello($name));
    }
}
```

## @Inject annotation
You can use @Inject annotation with PHP-DI.

Install doctrine/annotations.

```
composer require doctrine/annotations
```

Enable annotaions on your container configuration.
```php
// in Application::configureContainer();
$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(true);
```

Add @Inject and @param Type declaration comments for your controller or command properties.

```php
<?php

namespace App\Controller;

use App\GreetingService;
use Cake\Http\Response;

class HelloController extends AppController
{
    /**
     * @Inject
     * @param GreetingService
     */
    private $greetingService;

    public function index(string $name)
    {
        $suffix = $this->request->getQuery('suffix', 'san');
        $this->response
            ->getBody()
            ->write($this->greetingService->hello($name . ' ' . $suffix));
        return $this->response;
    }
}
```

```php
<?php

namespace App\Command;

use App\GreetingService;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class HelloCommand extends Command
{
    /**
     * @Inject
     * @var GreetingService
     */
    private $greetingService;

    //
}
```

# License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
