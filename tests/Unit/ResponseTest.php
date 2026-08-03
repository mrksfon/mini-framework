<?php

declare(strict_types=1);

use Framework\Http\Response;

it('can be created', function () {
    $response = new Response;

    expect($response)->toBeInstanceOf(Response::class);
});
