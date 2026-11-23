<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Response
{
    public function __construct(
        public string $body = '',
        public int $status = 200,
        public array $headers = ['Content-Type' => 'text/html; charset=UTF-8'],
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status);
    }

    public static function redirect(string $location): self
    {
        return new self('', 303, ['Location' => $location]);
    }

    public function send(): never
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
        exit;
    }
}
