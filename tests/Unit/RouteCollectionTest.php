<?php

declare(strict_types=1);

use Framework\Routing\Route;
use Framework\Routing\RouteCollection;

it('matches a static route by method and path', function () {
    $collection = new RouteCollection;

    $route = new Route('/users', fn () => 'users');

    $collection->add('GET', '/users', $route);

    $match = $collection->match('GET', '/users');

    expect($match)->not->toBeNull()
        ->and($match['route'])->toBe($route)
        ->and($match['parameters'])->toBe([]);
});

it('returns null when no route matches the method and path', function () {
    $collection = new RouteCollection;

    $route = new Route('/users', fn () => null);

    $collection->add('GET', '/users', $route);

    $match = $collection->match('POST', '/users');

    expect($match)->toBeNull();
});

it('matches a dynamic route and returns parameters', function () {
    $collection = new RouteCollection;

    $route = new Route('/users/{id}', fn (string $id) => $id);

    $collection->add('GET', '/users/{id}', $route);

    $match = $collection->match('GET', '/users/42');

    expect($match)->not->toBeNull()
        ->and($match['route'])->toBe($route)
        ->and($match['parameters'])->toBe(['id' => '42']);
});

it('prefers an exact route over a dynamic route', function () {
    $collection = new RouteCollection;

    $dynamicRoute = new Route('/users/{id}', fn (string $id) => $id);
    $staticRoute = new Route('/users/create', fn () => 'static');

    $collection->add('GET', '/users/{id}', $dynamicRoute);
    $collection->add('GET', '/users/create', $staticRoute);

    $match = $collection->match('GET', '/users/create');

    expect($match)->not->toBeNull()
        ->and($match['route'])->toBe($staticRoute)
        ->and($match['parameters'])->toBe([]);
});

it('detects when a path matches a different method', function () {
    $collection = new RouteCollection;

    $route = new Route('/users/{id}', fn (string $id) => $id);

    $collection->add('POST', '/users/{id}', $route);

    $matches = $collection->matchesOtherMethod('GET', '/users/42');

    expect($matches)->toBeTrue();
});

it('does not detect a different method when no route path matches', function () {
    $collection = new RouteCollection;

    $route = new Route('/users/{id}', fn (string $id) => $id);

    $collection->add('POST', '/users/{id}', $route);

    $matches = $collection->matchesOtherMethod('GET', '/posts/42');

    expect($matches)->toBeFalse();
});
