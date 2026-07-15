<?php

declare(strict_types=1);

namespace Framework\Routing;

final class RouteCollection
{
    /**
     * @var array<string, array<string, Route>>
     */
    private array $routes = [];

    public function add(string $method, string $path, Route $route): void
    {
        $this->routes[$method][$path] = $route;
    }

    /**
     * @return array{route: Route, parameters: array<string, string|null>}|null
     */
    public function match(string $method, string $path): ?array
    {
        if (isset($this->routes[$method][$path])) {
            return [
                'route' => $this->routes[$method][$path],
                'parameters' => [],
            ];
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            $parameters = $route->matches($path);

            if ($parameters === null) {
                continue;
            }

            return [
                'route' => $route,
                'parameters' => $parameters,
            ];
        }

        return null;
    }

    public function matchesOtherMethod(string $method, string $path): bool
    {
        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) {
                continue;
            }

            foreach ($routes as $route) {
                if ($route->matches($path) !== null) {
                    return true;
                }
            }
        }

        return false;
    }
}
