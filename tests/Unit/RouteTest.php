<?php

declare(strict_types=1);

use Framework\Routing\Route;

it('matches a static path', function () {
    $route = new Route('/users', fn () => 'users');

    $match = $route->matches('/users');

    expect($match)->toBe([]);
});

it('does not match a different static path', function () {
    $route = new Route('/users', fn () => 'users');

    $match = $route->matches('/posts');

    expect($match)->toBeNull();
});

it('matches a route with one dynamic parameter', function () {
    $route = new Route('/users/{id}', fn (string $id) => $id);

    $match = $route->matches('/users/42');

    expect($match)->toBe(['id' => '42']);
});

it('does not match when a static segment differs', function () {
    $route = new Route('/users/{id}', fn (string $id) => $id);

    $match = $route->matches('/posts/42');

    expect($match)->toBeNull();
});

it('matches multiple dynamic parameters', function () {
    $route = new Route('/users/{userId}/posts/{postId}', fn () => null);

    $match = $route->matches('/users/42/posts/7');

    expect($match)->toBe([
        'userId' => '42',
        'postId' => '7',
    ]);
});

it('does not match when the path has extra segments', function () {
    $route = new Route('/users/{id}', fn () => null);

    $match = $route->matches('/users/42/posts');

    expect($match)->toBeNull();
});

it('matches a constrained dynamic parameter', function () {
    $route = new Route('/users/{id:\d+}', fn () => null);

    $match = $route->matches('/users/42');

    expect($match)->toBe(['id' => '42']);
});

it('does not match a constrained dynamic parameter when the value fails the constrait', function () {
    $route = new Route('/users/{id:\d+}', fn () => null);

    $match = $route->matches('/users/abc');

    expect($match)->toBeNull();
});

it('matches an optional parameter when the value is present', function () {
    $route = new Route('/users/{id?}', fn () => null);

    $match = $route->matches('/users/42');

    expect($match)->toBe(['id' => '42']);
});

it('matches an optional parameter when the value is omitted', function () {
    $route = new Route('/users/{id?}', fn () => null);

    $match = $route->matches('/users');

    expect($match)->toBe(['id' => null]);
});

it('does not match when a required parameter is omitted', function () {
    $route = new Route('/users/{id}', fn () => null);

    $match = $route->matches('/users');

    expect($match)->toBeNull();
});

it('does not match when an optional parameter is not the final segment', function () {
    $route = new Route('/users/{id?}/posts', fn () => null);

    $match = $route->matches('/users/42/posts');

    expect($match)->toBeNull();
});

it('matches a catch-all wildcard parameter', function () {
    $route = new Route('/files/{path:*}', fn () => null);

    $match = $route->matches('/files/docs/readme.md');

    expect($match)->toBe(['path' => 'docs/readme.md']);
});

it('does not match when a wildcard parameter is not the final segment', function () {
    $route = new Route('/files/{path:*}/edit', fn () => null);

    $match = $route->matches('files/docs/readme.md/edit');

    expect($match)->toBeNull();
});

it('runs the route handler with matched parameters', function () {
    $route = new Route('/users/{id}', fn (string $id) => "user {$id}");

    $result = $route->run(['id' => '42']);

    expect($result)->toBe('user 42');
});

it('runs the route handler without parameters', function () {
    $route = new Route('/users', fn () => 'users');

    $result = $route->run([]);

    expect($result)->toBe('users');
});

it('stores a route name', function () {
    $route = new Route('/users', fn () => 'users');

    $route->name('users.index');

    expect($route->name())->toBe('users.index');
});

it('generates a URL by replacing route parameters', function () {
    $route = new Route('/users/{id}', fn () => 'users');

    $url = $route->url(['id' => '42']);

    expect($url)->toBe('/users/42');
});

it('generates a URL by replacing multiple route parameters', function () {
    $route = new Route('/users/{userId}/posts/{postId}', fn () => 'users');

    $url = $route->url([
        'userId' => '42',
        'postId' => '7',
    ]);

    expect($url)->toBe('/users/42/posts/7');
});

it('generates a URL for a constrained route parameter', function () {
    $route = new Route('/users/{id:\d+}', fn () => 'users');

    $url = $route->url(['id' => '42']);

    expect($url)->toBe('/users/42');
});

it('throws an exception when a required URL parameter is missing', function () {
    $route = new Route('/users/{id}', fn () => 'users');

    $route->url([]);
})->throws(InvalidArgumentException::class);

it('generates a URL for an optional route parameter when the value is present', function () {
    $route = new Route('/users/{id?}', fn () => 'users');

    $url = $route->url(['id' => '42']);

    expect($url)->toBe('/users/42');
});

it('generates a URL for an optional parameter when the value is omitted', function () {
    $route = new Route('/users/{id?}', fn () => 'users');

    $url = $route->url();

    expect($url)->toBe('/users');
});

it('generates a URL for a catch-all wildcard parameter', function () {
    $route = new Route('/files/{path:*}', fn () => 'files');

    $url = $route->url(['path' => 'docs/readme.md']);

    expect($url)->toBe('/files/docs/readme.md');
});
