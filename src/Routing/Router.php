<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Http\Request;
use Framework\Http\Response;
use InvalidArgumentException;

final readonly class Router
{
    private RouteCollection $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    public function add(string $method, string $path, callable $handler): Route
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

        $route = new Route($path, $handler);

        $this->routes->add($method, $path, $route);

        return $route;
    }

    public function dispatch(string $method, string $path): Response
    {
        $method = $this->normalizeMethod($method);

        $path = $this->normalizePath($path);

        $match = $this->routes->match($method, $path);

        if ($match !== null) {
            $result = $match['route']->run($match['parameters']);

            if ($result instanceof Response) {
                return $result;
            }

            if (is_string($result)) {
                return new Response($result);
            }

            return new Response;
        }

        if ($this->routes->matchesOtherMethod($method, $path)) {
            throw new MethodNotAllowedException("Method {$method} not allowed for path {$path}.");
        }

        throw new RouteNotFoundException("Route {$method} {$path} not found.");
    }

    public function get(string $path, callable $handler): Route
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): Route
    {
        return $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): Route
    {
        return $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): Route
    {
        return $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): Route
    {
        return $this->add('DELETE', $path, $handler);
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

    public function named(string $name): ?Route
    {
        return $this->routes->named($name);
    }

    /**
     * @param  array<string,string>  $parameters
     */
    public function url(string $name, array $parameters = []): string
    {
        $route = $this->routes->named($name);

        if ($route === null) {
            throw new RouteNotFoundException("Named route {$name} not found.");
        }

        return $route->url($parameters);
    }

    public function dispatchRequest(Request $request): Response
    {
        return $this->dispatch($request->method(), $request->path());
    }
}
