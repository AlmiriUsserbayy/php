<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    public function __construct(
        public string $method,
        public string $path,
        public array $query = [],
        public array $post = [],
    ) {}

    public static function fromGlobals(): self
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            $_GET,
            $_POST,
        );
    }
}
