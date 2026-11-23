<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class ConnectionFactory
{
    private const OPTIONS = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    /** DB_DRIVER=sqlite (по умолчанию, без сервера) или DB_DRIVER=mysql. */
    public static function create(): PDO
    {
        return (getenv('DB_DRIVER') ?: 'sqlite') === 'mysql'
            ? self::mysql()
            : self::sqlite();
    }

    private static function mysql(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: '127.0.0.1',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_NAME') ?: 'php_course',
        );

        return new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', self::OPTIONS);
    }

    /** Файл базы создаётся автоматически вместе с таблицей и тестовыми данными. */
    private static function sqlite(): PDO
    {
        $pdo = new PDO('sqlite:' . dirname(__DIR__, 2) . '/database/app.sqlite', null, null, self::OPTIONS);

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS items (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                code       TEXT NOT NULL UNIQUE,
                name       TEXT NOT NULL,
                quantity   INTEGER NOT NULL DEFAULT 0 CHECK (quantity >= 0),
                unit       TEXT NOT NULL CHECK (unit IN ('шт', 'кг', 'л', 'м', 'упак')),
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )"
        );

        if ((int) $pdo->query('SELECT COUNT(*) FROM items')->fetchColumn() === 0) {
            $pdo->exec(
                "INSERT INTO items (code, name, quantity, unit) VALUES
                    ('BOLT-M8',  'Болт M8x40',        1200, 'шт'),
                    ('CEM-M500', 'Цемент М500',        350, 'кг'),
                    ('PAINT-W',  'Краска белая',        48, 'л'),
                    ('CABLE-3',  'Кабель ВВГ 3x2.5',   500, 'м'),
                    ('GLOVE-L',  'Перчатки рабочие',    30, 'упак')"
            );
        }

        return $pdo;
    }
}
