<?php

declare(strict_types=1);

/** Экранирование значения для вывода в HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    return $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_valid(mixed $token): bool
{
    return is_string($token)
        && $token !== ''
        && hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token);
}
