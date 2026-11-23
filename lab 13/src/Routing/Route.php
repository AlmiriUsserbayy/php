<?php

declare(strict_types=1);

namespace App\Routing;

use Closure;

final readonly class Route
{
    public function __construct(
        public string $method,
        public string $pattern,
        public Closure $handler,
    ) {}

    /**
     * Сопоставляет только путь (метод не проверяется).
     *
     * @return array<string, string>|null параметры или null, если путь не совпал
     */
    public function match(string $path): ?array
    {
        $regex = preg_replace('/\{id\}/', '(?P<id>[1-9][0-9]*)', $this->pattern);

        if (preg_match('#^' . $regex . '$#', $path, $matches) !== 1) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
