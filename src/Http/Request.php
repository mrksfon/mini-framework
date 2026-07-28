<?php

declare(strict_types=1);

namespace Framework\Http;

final class Request
{
    public function __construct(private string $method, private string $path) {}

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }
}
