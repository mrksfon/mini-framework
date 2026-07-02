<?php

declare(strict_types=1);

namespace Framework\Routing;

final class Router
{
    /**
     * @var array<string, array<string, callable(): mixed>>
     */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        if (! isset($this->routes[$method][$path])) {
            throw new RouteNotFoundException("Route {$method} {$path} not found.");
        }
        $handler = $this->routes[$method][$path];

        return $handler();
    }
}
