<?php

declare(strict_types=1);

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\MethodNotAllowedException;
use Framework\Routing\RouteNotFoundException;
use Framework\Routing\Router;

it('registers and dispatches a static route', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('GET', '/test', function () use (&$capturedResult) {
        $capturedResult = 'users';
    });

    $response = $router->dispatch('GET', '/test');

    expect($capturedResult)->toBe('users');
    expect($response)->toBeInstanceOf(Response::class);
});

it('throws an exception when no route matches', function () {
    $router = new Router;

    $router->dispatch('GET', '/non-existent-route');
})->throws(RouteNotFoundException::class);

it('distinguishes routes by HTTP method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('GET', '/test', function () use (&$capturedResult) {
        $capturedResult = 'users';
    });
    $router->add('POST', '/test', function () use (&$capturedResult) {
        $capturedResult = 'posts';
    });

    $getResponse = $router->dispatch('GET', '/test');

    expect($capturedResult)->toBe('users');
    expect($getResponse)->toBeInstanceOf(Response::class);

    $capturedResult = null;
    $postResponse = $router->dispatch('POST', '/test');

    expect($capturedResult)->toBe('posts');
    expect($postResponse)->toBeInstanceOf(Response::class);
});

it('matches a dynamic route parameter and passes it to the handler', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('GET', '/users/{id}', function (string $id) use (&$capturedResult) {
        $capturedResult = $id;
    });

    $response = $router->dispatch('GET', '/users/42');

    expect($capturedResult)->toBe('42');
    expect($response)->toBeInstanceOf(Response::class);
});

it('matches multiple dynamic route parameters and passed them to the handler', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('GET', '/users/{id}/posts/{postId}', function (string $id, string $postId) use (&$capturedResult) {
        $capturedResult = "{$id}-{$postId}";
    });

    $response = $router->dispatch('GET', '/users/42/posts/22');

    expect($capturedResult)->toBe('42-22');
    expect($response)->toBeInstanceOf(Response::class);
});

it('reject a dynamic route when a static segment does not match', function () {
    $router = new Router;

    $router->add('GET', '/users/{id}', fn (string $id): string => $id);

    $router->dispatch('GET', '/posts/43');
})->throws(RouteNotFoundException::class);

it('registers a GET route using the get method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('GET', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('registers a POST route using the post method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->post('/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('POST', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('registers a PUT route using the put method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->put('/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('PUT', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('registers a PATCH route using the patch method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->patch('/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('PATCH', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('registers a DELETE route using the delete method', function () {
    $router = new Router;
    $capturedResult = null;

    $router->delete('/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('DELETE', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('treats routes with and without a trailing slash as the same route', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users', function () use (&$capturedResult) {
        $capturedResult = 'users';
    });

    $router->get('/users/', function () use (&$capturedResult) {
        $capturedResult = 'users with slash';
    });

    $responseWithoutSlash = $router->dispatch('GET', '/users');

    expect($capturedResult)->toBe('users with slash');
    expect($responseWithoutSlash)->toBeInstanceOf(Response::class);

    $capturedResult = null;

    $responseWithSlash = $router->dispatch('GET', '/users/');

    expect($capturedResult)->toBe('users with slash');
    expect($responseWithSlash)->toBeInstanceOf(Response::class);
});

it('treats routes with and without a leading slash as the same route', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('users', function () use (&$capturedResult) {
        $capturedResult = 'users';
    });

    $router->get('/users', function () use (&$capturedResult) {
        $capturedResult = 'users with leading slash';
    });

    $responseWithoutLeadingSlash = $router->dispatch('GET', 'users');
    expect($capturedResult)->toBe('users with leading slash');
    expect($responseWithoutLeadingSlash)->toBeInstanceOf(Response::class);

    $capturedResult = null;

    $responseWithLeadingSlash = $router->dispatch('GET', '/users');

    expect($capturedResult)->toBe('users with leading slash');
    expect($responseWithLeadingSlash)->toBeInstanceOf(Response::class);
});

it('keeps the root path valid during normalization', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/', function () use (&$capturedResult) {
        $capturedResult = 'home';
    });

    $response = $router->dispatch('GET', '/');

    expect($capturedResult)->toBe('home');
    expect($response)->toBeInstanceOf(Response::class);
});

it('normalizes HTTP method casing during registration and dispatching', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('get', '/test', function () use (&$capturedResult) {
        $capturedResult = 'test';
    });

    $response = $router->dispatch('GeT', '/test');

    expect($capturedResult)->toBe('test');
    expect($response)->toBeInstanceOf(Response::class);
});

it('later duplicate route registration replaces the earlier handler', function () {
    $router = new Router;
    $capturedResult = null;

    $router->add('get', 'test/', function () use (&$capturedResult) {
        $capturedResult = 'first';
    });
    $router->add('get', 'test/', function () use (&$capturedResult) {
        $capturedResult = 'second';
    });

    $response = $router->dispatch('GET', '/test');

    expect($capturedResult)->toBe('second');
    expect($response)->toBeInstanceOf(Response::class);
});

it('rejects an empty http method', function () {
    $router = new Router;

    $router->add('', '/users', fn () => 'users');
})->throws(InvalidArgumentException::class);

it('rejects a whitespace-only http method', function () {
    $router = new Router;

    $router->add(' ', '/users', fn () => 'users');
})->throws(InvalidArgumentException::class);

it('normalizes an empty path to the root path', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('', fn () => 'home');
    $router->get('', function () use (&$capturedResult) {
        $capturedResult = 'home';
    });

    $response = $router->dispatch('GET', '');

    expect($capturedResult)->toBe('home');
    expect($response)->toBeInstanceOf(Response::class);
});

it('rejects an empty route parameter name', function () {
    $router = new Router;

    $router->get('/users/{}', fn () => 'users');
})->throws(InvalidArgumentException::class);

it('rejects an unclosed route parameter', function () {
    $router = new Router;

    $router->get('/users/{id', fn () => 'users');
})->throws(InvalidArgumentException::class);

it('rejects an unopened route parameter', function () {
    $router = new Router;

    $router->get('/users/id}', fn () => 'users');
})->throws(InvalidArgumentException::class);

it('throws method not allowed exception when path exists for a different method', function () {
    $router = new Router;

    $router->post('/users', fn () => 'created');

    $router->dispatch('GET', '/users');
})->throws(MethodNotAllowedException::class);

it('throws method not allowed exception when a dynamic path exists for a different method', function () {
    $router = new Router;

    $router->post('/users/{id}', fn (string $id): string => $id);

    $router->dispatch('GET', '/users/42');
})->throws(MethodNotAllowedException::class);

it('matches a dynamic route parameter only when it satisfies its constraint', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id:\d+}', function (string $id) use (&$capturedResult) {
        $capturedResult = $id;
    });

    $response = $router->dispatch('GET', '/users/42');

    expect($capturedResult)->toBe('42');
    expect($response)->toBeInstanceOf(Response::class);

    $router->dispatch('GET', '/users/abc');

})->throws(RouteNotFoundException::class);

it('does not throw method not allowed when a constrained dynamic path does not match another method', function () {
    $router = new Router;

    $router->post('/users/{id:\d+}', fn (string $id): string => $id);

    $router->dispatch('GET', '/users/abc');

})->throws(RouteNotFoundException::class);

it('prioritizes a static route over a dynamic route registered first', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id}', function () use (&$capturedResult) {
        $capturedResult = 'dynamic';
    });
    $router->get('/users/create', function () use (&$capturedResult) {
        $capturedResult = 'static';
    });

    $response = $router->dispatch('GET', '/users/create');

    expect($capturedResult)->toBe('static');
    expect($response)->toBeInstanceOf(Response::class);
});

it('prioritizes a static route over a dynamic route registered second', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/create', function () use (&$capturedResult) {
        $capturedResult = 'static';
    });
    $router->get('/users/{id}', function () use (&$capturedResult) {
        $capturedResult = 'dynamic';
    });

    $response = $router->dispatch('GET', '/users/create');

    expect($capturedResult)->toBe('static');
    expect($response)->toBeInstanceOf(Response::class);
});

it('matches a route when its optional parameter is omitted', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id?}', function (?string $id) use (&$capturedResult) {
        $capturedResult = $id;
    });
    $response = $router->dispatch('GET', '/users');

    expect($capturedResult)->toBeNull();
    expect($response)->toBeInstanceOf(Response::class);
});

it('passes an optional parameter to the handler when it is present', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id?}', function (?string $id) use (&$capturedResult) {
        $capturedResult = $id;
    });

    $response = $router->dispatch('GET', 'users/42');

    expect($capturedResult)->toBe('42');
    expect($response)->toBeInstanceOf(Response::class);
});

it('rejects an optional parameter that is not the final segment', function () {
    $router = new Router;

    $router->get('/users/{id?}/posts', fn (?string $id): ?string => $id);
})->throws(InvalidArgumentException::class);

it('throws method not allowed when an optional parameter is omitted for another method', function () {
    $router = new Router;

    $router->post('/users/{id?}', fn (?string $id): ?string => $id);

    $router->dispatch('GET', '/users');
})->throws(MethodNotAllowedException::class);

it('matches a catch-all wildcard parameter', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/files/{path:*}', function (string $path) use (&$capturedResult) {
        $capturedResult = $path;
    });

    $response = $router->dispatch('GET', '/files/docs/readme.md');
    expect($capturedResult)->toBe('docs/readme.md');
    expect($response)->toBeInstanceOf(Response::class);
});

it('throws method not allowed when a catch-all wildcard matches another method', function () {
    $router = new Router;

    $router->post('/files/{path:*}', fn (string $path): string => $path);

    $router->dispatch('GET', '/files/docs/readme.md');
})->throws(MethodNotAllowedException::class);

it('rejects a catch-all wildcard parameter that is not the final segment', function () {
    $router = new Router;

    $router->get('/files/{path:*}/edit', fn (string $path): string => $path);
})->throws(InvalidArgumentException::class);

it('passes dynamic route parameters to the handler by name', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id}/posts/{postId}', function (string $postId, string $id) use (&$capturedResult) {
        $capturedResult = "{$id}-{$postId}";
    });

    $response = $router->dispatch('GET', '/users/42/posts/22');

    expect($capturedResult)->toBe('42-22');
    expect($response)->toBeInstanceOf(Response::class);
});

it('passes constrained route parameters to the handler by name', function () {
    $router = new Router;
    $capturedResult = null;

    $router->get('/users/{id:\d+}/posts/{postId:\d+}', function (string $postId, string $id) use (&$capturedResult) {
        $capturedResult = "{$id}-{$postId}";
    });

    $response = $router->dispatch('GET', '/users/42/posts/22');

    expect($capturedResult)->toBe('42-22');
    expect($response)->toBeInstanceOf(Response::class);

});

it('passes catch-all wildcard route parameters to the handler by name', function () {
    $router = new Router;

    $capturedResult = null;

    $router->get('/tenants/{tenant}/files/{path:*}', function (string $path, string $tenant) use (&$capturedResult) {
        $capturedResult = "{$tenant}:{$path}";
    });

    $response = $router->dispatch('GET', 'tenants/acme/files/docs/readme.md');

    expect($capturedResult)->toBe('acme:docs/readme.md');
    expect($response)->toBeInstanceOf(Response::class);
});

it('allows a registered route to be named', function () {
    $router = new Router;

    $route = $router->get('/users', fn () => 'users');

    $route->name('users.index');

    expect($route->name())->toBe('users.index');
});

it('finds a route by name', function () {
    $router = new Router;

    $route = $router->get('/users', fn () => 'users');

    $route->name('users.index');

    expect($router->named('users.index'))->toBe($route);
});

it('generates a URL for a named route with parameters', function () {
    $router = new Router;

    $router->get('/users/{id}', fn () => 'users')->name('users.show');

    $url = $router->url('users.show', ['id' => '42']);

    expect($url)->toBe('/users/42');
});

it('throws an exception when generating a URL for an unknown route name', function () {
    $router = new Router;

    $router->url('users.show');
})->throws(RouteNotFoundException::class);

it('generates a URL for a named route with an omitted optional parameter', function () {
    $router = new Router;

    $router->get('/users/{id?}', fn () => 'users')->name('users.index');

    $url = $router->url('users.index');

    expect($url)->toBe('/users');
});

it('dispatches using a request object', function () {
    $router = new Router;

    $handled = false;

    $router->get('/users', function () use (&$handled) {
        $handled = true;
    });

    $request = new Request('GET', '/users');

    $response = $router->dispatchRequest($request);

    expect($handled)->toBeTrue();
    expect($response)->toBeInstanceOf(Response::class);
});

it('returns a response object from dispatch', function () {
    $router = new Router;

    $router->get('/test', fn () => 'hello');

    $response = $router->dispatch('GET', '/test');

    expect($response)->toBeInstanceOf(Response::class);
});

it('converts string handler results into a response object', function () {
    $router = new Router;

    $router->get('/test', fn () => 'hello world');

    $response = $router->dispatch('GET', '/test');

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->content())->toBe('hello world');
    expect($response->statusCode())->toBe(200);
});

it('preserves response handler resulsts', function () {
    $router = new Router;

    $expectedResponse = new Response('created', 201, ['Location' => '/users/1']);

    $router->post('/users', fn () => $expectedResponse);

    $response = $router->dispatch('POST', '/users');

    expect($response)->toBe($expectedResponse);
    expect($response->content())->toBe('created');
    expect($response->statusCode())->toBe(201);
    expect($response->headers())->toBe(['Location' => '/users/1']);
});
