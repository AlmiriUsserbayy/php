<?php

declare(strict_types=1);

namespace App\Routing;

use App\Http\Request;
use App\Http\Response;
use Closure;

final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    public function add(string $method, string $pattern, Closure $handler): void
    {
        $this->routes[] = new Route(strtoupper($method), $pattern, $handler);
    }

    public function get(string $pattern, Closure $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, Closure $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function dispatch(Request $request): Response
    {
        $allowed = [];

        foreach ($this->routes as $route) {
            $parameters = $route->match($request->path);
            if ($parameters === null) {
                continue;
            }
            if ($route->method === $request->method) {
                return ($route->handler)($request, ...$parameters);
            }
            $allowed[$route->method] = true;
        }

        if ($allowed !== []) {
            return new Response(
                'Метод не разрешён',
                405,
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Allow'        => implode(', ', array_keys($allowed)),
                ],
            );
        }

        return Response::html('Страница не найдена', 404);
    }
}
