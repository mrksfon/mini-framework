<?php

declare(strict_types=1);

namespace Framework\Routing;

use InvalidArgumentException;

final class Router
{
    /**
     * @var array<string, array<string, callable>>
     */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

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

        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) {
                continue;
            }

            foreach (array_keys($routes) as $routePath) {
                $routeSegments = explode('/', trim($routePath, '/'));
                $pathSegments = explode('/', trim($path, '/'));

                if (count($routeSegments) !== count($pathSegments)) {
                    continue;
                }

                $matches = true;

                foreach ($routeSegments as $index => $routeSegment) {
                    $isParameter = str_starts_with($routeSegment, '{') && str_ends_with($routeSegment, '}');

                    if ($isParameter) {
                        continue;
                    }

                    if ($routeSegment !== $pathSegments[$index]) {
                        $matches = false;
                        break;
                    }
                }

                if ($matches) {
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
        foreach (explode('/', trim($path, '/')) as $segment) {
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
