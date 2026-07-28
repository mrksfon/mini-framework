<?php

declare(strict_types=1);

use Framework\Http\Request;

it('can create a request object', function () {
    $request = new Request('GET', '/users');

    expect($request)->toBeInstanceOf(Request::class);
});

it('stores the http method', function () {
    $request = new Request('GET', '/users');

    expect($request->method())->toBe('GET');
});

it('stores the request path', function () {
    $request = new Request('GET', '/users');

    expect($request->path())->toBe('/users');
});

it('normalizes the http method casing', function () {
    $request = new Request('get', '/users');

    expect($request->method())->toBe('GET');
});

it('normalizes request path slashes', function () {
    $request = new Request('GET', 'users/');

    expect($request->path())->toBe('/users');
});

it('keeps the root request path valid after normalization', function () {
    $request = new Request('GET', '/');

    expect($request->path())->toBe('/');
});
