<?php

declare(strict_types=1);

namespace Framework\Http;

final class Response
{
    /**
     * @param  array<string,string>  $headers
     */
    public function __construct(private string $content = '', private int $statusCode = 200, private array $headers = []) {}

    public function content(): string
    {
        return $this->content;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string,string>
     */
    public function headers(): array
    {
        return $this->headers;
    }
}
