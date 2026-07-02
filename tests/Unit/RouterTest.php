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
