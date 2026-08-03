<?php

declare(strict_types=1);

namespace Framework\Http;

final class Response
{
    public function __construct(private string $content = '', private int $statusCode = 200) {}

    public function content(): string
    {
        return $this->content;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
