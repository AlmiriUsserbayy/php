# Лабораторная работа 13 — вариант 4: «Склад»

Ресурс `Item`: `code`, `name`, `quantity`, `unit`. Обязательный фильтр — по единице измерения (плюс поиск по коду/названию).

## Запуск

Двойной клик по `start.bat` (или `php -S localhost:8000 -t public` в терминале) и откройте http://localhost:8000/items.
По умолчанию используется SQLite: файл `database/app.sqlite` создаётся сам, Composer и MySQL не нужны (нужен `pdo_sqlite`).

Режим MySQL: импортируйте `database/schema.sql`, в `.env` поставьте `DB_DRIVER=mysql` и укажите `DB_USER`/`DB_PASS`.
Тесты: `php tests/run.php`.

## Маршруты

| Метод | URI                  | Handler                         | Успех        | Ошибка   |
|-------|----------------------|---------------------------------|--------------|----------|
| GET   | /items               | ItemController::index           | 200          | 500      |
| GET   | /items/create        | ItemController::create          | 200          | —        |
| POST  | /items               | ItemController::store           | 303          | 403, 422 |
| GET   | /items/{id}          | ItemController::show            | 200          | 404      |
| GET   | /items/{id}/edit     | ItemController::edit            | 200          | 404      |
| POST  | /items/{id}/edit     | ItemController::update          | 303          | 403, 404, 422 |
| POST  | /items/{id}/delete   | ItemController::destroy         | 303          | 403, 404 |

Дополнительно: `GET /` → 303 на `/items`. Неизвестный путь → 404, неверный метод → 405 с заголовком `Allow`.
