<?php

declare(strict_types=1);

namespace Framework\Routing;

final class Router
{
    /**
     * @var array<string, array<string, callable>>
     */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];

            return $handler();
        }

        foreach ($this->routes[$method] ?? [] as $routePath => $handler) {
            $routeSegments = explode('/', trim($routePath, '/'));
            $pathSegments = explode('/', trim($path, '/'));

            if (count($routeSegments) !== count($pathSegments)) {
                continue;
            }

            $parameters = [];
            $matches = true;
            foreach ($routeSegments as $index => $routeSegment) {
                $isParameter = str_starts_with($routeSegment, '{') && str_ends_with($routeSegment, '}');
                if ($isParameter) {
                    $parameters[] = $pathSegments[$index];

                    continue;
                }

                if ($routeSegment !== $pathSegments[$index]) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                return $handler(...$parameters);
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
}
