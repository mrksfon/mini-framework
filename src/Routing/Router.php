<?php

declare(strict_types=1);

namespace Framework\Routing;

use InvalidArgumentException;

final class Router
{
    /**
     * @var array<string, array<string, Route>>
     */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

        $this->routes[$method][$path] = new Route($path, $handler);
    }

    public function dispatch(string $method, string $path): mixed
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];

            return $route->run([]);
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            $parameters = $route->matches($path);

            if ($parameters === null) {
                continue;
            }

            return $route->run($parameters);
        }

        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) {
                continue;
            }

            foreach ($routes as $route) {
                if ($route->matches($path) !== null) {
                    throw new MethodNotAllowedException("Method {$method} not allowed for path {$path}.");
                }
            }
        }

        throw new RouteNotFoundException("Route {$method} {$path} not found.");
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function normalizePath(string $path): string
    {
        $path = '/'.trim($path, '/');
        $segments = explode('/', trim($path, '/'));
        foreach ($segments as $index => $segment) {
            if (str_starts_with($segment, '{') && str_ends_with($segment, '?}') && $index !== array_key_last($segments)) {
                throw new InvalidArgumentException('Optional route parameter must be the final segment');
            }
            if (str_starts_with($segment, '{') && str_ends_with($segment, ':*}') && $index !== array_key_last($segments)) {
                throw new InvalidArgumentException('Catch-all wildcard route parameter must be the final segment');
            }
            if ($segment === '{}') {
                throw new InvalidArgumentException('Route parameter cannot be empty');
            }
            if (str_starts_with($segment, '{') && ! str_ends_with($segment, '}')) {
                throw new InvalidArgumentException('Route parameter must be closed');
            }

            if (! str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
                throw new InvalidArgumentException('Route parameter must be opened');
            }
        }

        return $path;
    }

    private function normalizeMethod(string $method): string
    {
        if (trim($method) === '') {
            throw new InvalidArgumentException('HTTP method cannot be empty.');
        }

        return strtoupper($method);
    }
}
