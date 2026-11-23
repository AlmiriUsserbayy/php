<?php

declare(strict_types=1);

use App\Controller\ItemController;
use App\Database\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Repository\ItemRepository;
use App\Routing\Router;
use App\Support\Env;
use App\View\Renderer;

$root = dirname(__DIR__);

if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    // Запуск без Composer: простая PSR-4 автозагрузка App\ => src/
    spl_autoload_register(static function (string $class) use ($root): void {
        $file = $root . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (str_starts_with($class, 'App\\') && is_file($file)) {
            require $file;
        }
    });
    require $root . '/src/View/helpers.php';
}

Env::load(dirname(__DIR__) . '/.env');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

try {
    $renderer   = new Renderer(dirname(__DIR__) . '/templates');
    $repository = new ItemRepository(ConnectionFactory::create());
    $controller = new ItemController($repository, $renderer);

    $router = new Router();
    (require dirname(__DIR__) . '/config/routes.php')($router, $controller);

    $response = $router->dispatch(Request::fromGlobals());
} catch (Throwable $error) {
    error_log($error->__toString());
    $response = Response::html('Внутренняя ошибка', 500);
}

$response->send();
