<?php

declare(strict_types=1);

use Framework\Http\Request;

it('can create a request object', function () {
    $request = new Request;

    expect($request)->toBeInstanceOf(Request::class);
});
