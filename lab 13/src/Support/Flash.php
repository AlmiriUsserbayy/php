<?php

declare(strict_types=1);

namespace App\Support;

final class Flash
{
    public static function success(string $message): void
    {
        $_SESSION['_flash']['success'][] = $message;
    }

    /** @return array<string, list<string>> */
    public static function consume(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return $messages;
    }
}
