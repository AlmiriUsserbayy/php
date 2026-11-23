<?php

declare(strict_types=1);

use App\Controller\ItemController;
use App\Http\Request;
use App\Http\Response;
use App\Routing\Router;

/**
 * Статический /items/create регистрируется раньше /items/{id}.
 * Параметр {id} принимает только положительное целое число.
 */
return static function (Router $router, ItemController $controller): void {
    $router->get('/items',               fn (Request $r) => $controller->index($r->query));
    $router->get('/items/create',        fn (Request $r) => $controller->create());
    $router->post('/items',              fn (Request $r) => $controller->store($r->post));
    $router->get('/items/{id}',          fn (Request $r, string $id) => $controller->show($id));
    $router->get('/items/{id}/edit',     fn (Request $r, string $id) => $controller->edit($id));
    $router->post('/items/{id}/edit',    fn (Request $r, string $id) => $controller->update($id, $r->post));
    $router->post('/items/{id}/delete',  fn (Request $r, string $id) => $controller->destroy($id, $r->post));

    // Вспомогательный маршрут: корень сайта ведёт к списку.
    $router->get('/', fn (Request $r) => Response::redirect('/items'));
};
