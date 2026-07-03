<?php

declare(strict_types=1);

use Framework\Routing\RouteNotFoundException;
use Framework\Routing\Router;

it('registers and dispatches a static route', function () {
    $router = new Router;

    $router->add('GET', '/test', fn () => 'users');

    $result = $router->dispatch('GET', '/test');

    expect($result)->toBe('users');
});

it('throws an exception when no route matches', function () {
    $router = new Router;

    $router->dispatch('GET', '/non-existent-route');
})->throws(RouteNotFoundException::class);

it('distinguishes routes by HTTP method', function () {
    $router = new Router;

    $router->add('GET', '/test', fn () => 'users');
    $router->add('POST', '/test', fn () => 'posts');

    $getResult = $router->dispatch('GET', '/test');
    $postResult = $router->dispatch('POST', '/test');

    expect($getResult)->toBe('users');
    expect($postResult)->toBe('posts');
});

it('matches a dynamic route parameter and passes it to the handler', function () {
    $router = new Router;

    $router->add('GET', '/users/{id}', fn (string $id): string => $id);

    expect($router->dispatch('GET', '/users/42'))->toBe('42');
});

it('matches multiple dynamic route parameters and passed them to the handler', function () {
    $router = new Router;

    $router->add('GET', '/users/{id}/posts/{postId}', fn (string $id, string $postId): string => "{$id}-{$postId}");

    expect($router->dispatch('GET', '/users/42/posts/22'))->toBe('42-22');
});

it('reject a dynamic route when a static segment does not match', function () {
    $router = new Router;

    $router->add('GET', '/users/{id}', fn (string $id): string => $id);

    $router->dispatch('GET', '/posts/43');
})->throws(RouteNotFoundException::class);

it('registers a GET route using the get method', function () {
    $router = new Router;

    $router->get('/test', fn () => 'test');

    $result = $router->dispatch('GET', '/test');

    expect($result)->toBe('test');
});

it('registers a POST route using the post method', function () {
    $router = new Router;

    $router->post('/test', fn () => 'test');

    $result = $router->dispatch('POST', '/test');

    expect($result)->toBe('test');
});

it('registers a PUT route using the put method', function () {
    $router = new Router;

    $router->put('/test', fn () => 'test');

    $result = $router->dispatch('PUT', '/test');

    expect($result)->toBe('test');
});

it('registers a PATCH route using the patch method', function () {
    $router = new Router;

    $router->patch('/test', fn () => 'test');

    $result = $router->dispatch('PATCH', '/test');

    expect($result)->toBe('test');
});

it('registers a DELETE route using the delete method', function () {
    $router = new Router;

    $router->delete('/test', fn () => 'test');

    $result = $router->dispatch('DELETE', '/test');

    expect($result)->toBe('test');
});
