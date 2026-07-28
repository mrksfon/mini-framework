<?php

declare(strict_types=1);

use Framework\Http\Request;

it('can create a request object', function () {
    $request = new Request('GET');

    expect($request)->toBeInstanceOf(Request::class);
});

it('stores the http method', function () {
    $request = new Request('GET');

    expect($request->method())->toBe('GET');
});
