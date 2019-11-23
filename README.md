# CakeCandle
A PSR-11 compatible dependency injection plugin for CakePHP4.

[![Latest Stable Version](https://poser.pugx.org/n1215/cake-candle/v/stable)](https://packagist.org/packages/n1215/cake-candle)
[![License](https://poser.pugx.org/n1215/cake-candle/license)](https://packagist.org/packages/n1215/cake-candle)
[![Build Status](https://scrutinizer-ci.com/g/n1215/cake-candle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/n1215/cake-candle/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/n1215/cake-candle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/n1215/cake-candle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/n1215/cake-candle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/n1215/cake-candle/?branch=master)

# Requirements
- CakePHP >= 4.0

# Install

```
# Create your CakePHP4 project.
composer create-project --prefer-dist cakephp/app your_app

cd your_app

# Install CakeCandle.
composer require n1215/cake-candle
```

# Usage

## 1. Install your PSR-11 container
You can use php-di/php-di for example.
```
composer require php-di/php-di
```

## 2. Change your Application class

```php
<?php
declare(strict_types=1);

namespace App;

// ...
use Cake\Http\BaseApplication;
// ...
use DI\ContainerBuilder;
use N1215\CakeCandle\ContainerBagLocator;
use N1215\CakeCandle\Controller\ControllerFactory;
// ...

class Application extends BaseApplication
{
    // 1. configure a PSR-11 compatible container as you like.
    private function configureContainer()
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAnnotations(false);
        return $builder->build();
    }

    // 2. initialize ContainerBagLocator and ControllerFactory with the configured container in Application::bootstrap().
    public function bootstrap(): void
    {
        try {
            $container = $this->configureContainer();
            ContainerBagLocator::init($container);
            $this->controllerFactory = new ControllerFactory(ContainerBagLocator::get());
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

## 3. Change bin/cake.php

```
// ...

+ use N1215\CakeCandle\Console\CommandFactory;

// add CommandFactory to CommandRunner constructor parameters.
- $runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
+ $runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake', new CommandFactory());

```

## 4. Create dependency

```php
<?php
declare(strict_types=1);

namespace App;

class GreetingService
{
    public function hello(string $name): string
    {
        return "Hello, {$name}";
    }
}
```

## 5. Inject into Controller

### Assisted injection (Method injection)
Extends AppController and use N1215\CakeCandle\Http\AssistedAction trait.
This trait can fill type declared parameters when action methods are invoked.

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use App\GreetingService;
use Cake\Http\Response;

class HelloController extends AppController
{
    public function index(string $name, GreetingService $greetingService): Response
    {
        $suffix = $this->request->getQuery('suffix', 'san');
        $this->response
            ->getBody()
            ->write($greetingService->hello($name . ' ' . $suffix));

        return $this->response;
    }
}

```


## 6. Inject into Console Command

### Constructor injection

Extends \Cake\Console\Command as usual.
Type declared parameters are filled using your container.

```php
<?php
declare(strict_types=1);

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

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('name', [
            'help' => 'name'
        ]);
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $name = $args->getArgument('name');
        $io->out($this->greetingService->hello($name));
    }
}
```

## Additional usage

### @Inject annotation
You can use @Inject annotation with PHP-DI.

Install doctrine/annotations.

```
composer require doctrine/annotations
```

Enable annotations on your container configuration.
```php
// in Application::configureContainer();
$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(true);
```

Add @Inject and @param Type declaration comments for your controller or command properties.

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use App\GreetingService;
use Cake\Http\Response;

class HelloController extends AppController
{
    /**
     * @Inject
     * @var GreetingService
     */
    private $greetingService;

    public function index(string $name): Response
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
declare(strict_types=1);

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

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('name', [
            'help' => 'name'
        ]);
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $name = $args->getArgument('name');
        $io->out($this->greetingService->hello($name));
    }
}
```

# License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
