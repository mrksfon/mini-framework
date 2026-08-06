<?php

declare(strict_types=1);

use Framework\Http\Response;

it('can be created', function () {
    $response = new Response;

    expect($response)->toBeInstanceOf(Response::class);
});

it('stores response content', function () {
    $response = new Response('hello world');

    expect($response->content())->toBe('hello world');
});

it('stores response status code', function () {
    $response = new Response('hello world', 201);

    expect($response->statusCode())->toBe(201);
});

it('defaults response status code to 200', function () {
    $response = new Response;

    expect($response->statusCode())->toBe(200);
});

it('stores response headers', function () {
    $response = new Response('hello world', 200, ['Content-Type' => 'text/plain']);

    expect($response->headers())->toBe(['Content-Type' => 'text/plain']);
});

it('defaults response headers to an empty array', function () {
    $response = new Response;

    expect($response->headers())->toBe([]);
});

it('can fluently set a response header', function () {
    $response = new Response;

    $result = $response->withHeader('Content-Type', 'text/plain');

    expect($result)->toBe($response)->and($response->headers())->toBe(['Content-Type' => 'text/plain']);

});
