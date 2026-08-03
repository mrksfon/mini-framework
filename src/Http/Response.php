<?php

declare(strict_types=1);

namespace Framework\Http;

final class Response
{
    public function __construct(private string $content = '') {}

    public function content(): string
    {
        return $this->content;
    }
}
