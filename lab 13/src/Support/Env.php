<?php

declare(strict_types=1);

namespace App\Support;

final class Env
{
    /** Простая загрузка KEY=VALUE из .env (переменные среды приоритетнее). */
    public static function load(string $file): void
    {
        if (!is_file($file)) {
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            if (getenv($key) === false) {
                putenv($key . '=' . trim($value, " \t\"'"));
            }
        }
    }
}
