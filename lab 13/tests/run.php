<?php

declare(strict_types=1);

/**
 * Интеграционные тесты: Router -> Controller -> Repository -> Renderer.
 * Используется SQLite in-memory (расширение pdo_sqlite), MySQL не нужен.
 * Запуск: php tests/run.php
 */

use App\Controller\ItemController;
use App\Http\Request;
use App\Http\Response;
use App\Repository\ItemRepository;
use App\Routing\Router;
use App\View\Renderer;

$root = dirname(__DIR__);

if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        $file = $root . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (str_starts_with($class, 'App\\') && is_file($file)) {
            require $file;
        }
    });
    require $root . '/src/View/helpers.php';
}

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "Нужно расширение pdo_sqlite для запуска тестов.\n");
    exit(2);
}

/** @return array{Router, PDO} */
function makeApp(): array
{
    $_SESSION = [];

    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec(
        'CREATE TABLE items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 0,
            unit TEXT NOT NULL
        )'
    );
    $pdo->exec(
        "INSERT INTO items (code, name, quantity, unit) VALUES
         ('BOLT-M8', 'Болт M8x40', 1200, 'шт'),
         ('CEM-500', 'Цемент М500', 350, 'кг'),
         ('PAINT-W', 'Краска белая', 48, 'л')"
    );

    $controller = new ItemController(
        new ItemRepository($pdo),
        new Renderer(dirname(__DIR__) . '/templates'),
    );
    $router = new Router();
    (require dirname(__DIR__) . '/config/routes.php')($router, $controller);

    return [$router, $pdo];
}

function get(Router $r, string $path, array $query = []): Response
{
    return $r->dispatch(new Request('GET', $path, $query));
}

function post(Router $r, string $path, array $data = [], bool $csrf = true): Response
{
    if ($csrf) {
        $data['_csrf'] = csrf_token();
    }

    return $r->dispatch(new Request('POST', $path, [], $data));
}

function count_items(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
}

$validItem = ['code' => 'nail-50', 'name' => 'Гвозди 50 мм', 'quantity' => '500', 'unit' => 'упак'];

$tests = [];
$tests[] = function () {
    [$r] = makeApp();
    $res = get($r, '/items');
    return ['1. GET /items', '200 и список', $res->status . ' и ' . (str_contains($res->body, 'Болт M8x40') ? 'список' : 'нет списка')];
};
$tests[] = function () {
    [$r] = makeApp();
    $res = get($r, '/items/1');
    return ['2. GET /items/1', '200 и карточка', $res->status . ' и ' . (str_contains($res->body, 'BOLT-M8') ? 'карточка' : 'нет карточки')];
};
$tests[] = function () {
    [$r] = makeApp();
    return ['3. GET /items/999', '404', (string) get($r, '/items/999')->status];
};
$tests[] = function () {
    [$r] = makeApp();
    return ['4. GET /unknown', '404', (string) get($r, '/unknown')->status];
};
$tests[] = function () {
    [$r] = makeApp();
    $res = post($r, '/items/create');
    return ['5. POST /items/create (GET-only)', '405 и Allow: GET', $res->status . ' и Allow: ' . ($res->headers['Allow'] ?? '-')];
};
$tests[] = function () use ($validItem) {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items', $validItem);
    $loc = $res->headers['Location'] ?? '';
    $page = get($r, $loc);
    $ok = $res->status === 303 && $page->status === 200 && str_contains($page->body, 'NAIL-50') && str_contains($page->body, 'Позиция добавлена');
    return ['6. Корректный create', '303, затем GET 200 с flash', $ok ? '303, затем GET 200 с flash' : $res->status . '/' . $page->status];
};
$tests[] = function () {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items', ['code' => '', 'name' => 'X', 'quantity' => 'abc', 'unit' => 'ящик']);
    $ok = $res->status === 422 && count_items($pdo) === 3 && str_contains($res->body, 'value="X"');
    return ['7. Некорректный create', '422, форма заполнена, строка не добавлена', $ok ? '422, форма заполнена, строка не добавлена' : (string) $res->status];
};
$tests[] = function () use ($validItem) {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items/1/edit', ['code' => 'BOLT-M8', 'name' => 'Болт M8x50', 'quantity' => '777', 'unit' => 'шт']);
    $page = get($r, '/items/1');
    $ok = $res->status === 303 && str_contains($page->body, 'Болт M8x50') && str_contains($page->body, '777');
    return ['8. Корректный update', '303 и новые данные', $ok ? '303 и новые данные' : (string) $res->status];
};
$tests[] = function () {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items/1/delete', [], false);
    $ok = $res->status === 403 && count_items($pdo) === 3;
    return ['9. Delete без CSRF', '403, строка сохранена', $ok ? '403, строка сохранена' : (string) $res->status];
};
$tests[] = function () {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items/1/delete');
    $gone = get($r, '/items/1')->status === 404 && count_items($pdo) === 2;
    return ['10. Корректный delete', '303, строка отсутствует', $res->status === 303 && $gone ? '303, строка отсутствует' : (string) $res->status];
};
$tests[] = function () {
    [$r] = makeApp();
    post($r, '/items', ['code' => 'XSS-1', 'name' => '<script>alert(1)</script>', 'quantity' => '1', 'unit' => 'шт']);
    $body = get($r, '/items')->body;
    $ok = str_contains($body, '&lt;script&gt;') && !str_contains($body, '<script>alert');
    return ['11. <script> в названии', 'Выведен как текст', $ok ? 'Выведен как текст' : 'Не экранирован'];
};
$tests[] = function () use ($validItem) {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items', $validItem);
    get($r, $res->headers['Location']);
    get($r, $res->headers['Location']); // «F5» после PRG: повторяется только GET
    return ['12. Обновление после POST', 'Строк 4 (INSERT не повторён)', 'Строк ' . count_items($pdo) . (count_items($pdo) === 4 ? ' (INSERT не повторён)' : '')];
};
$tests[] = function () {
    [$r] = makeApp();
    $res = get($r, '/items', ['unit' => 'кг']);
    $ok = str_contains($res->body, 'Цемент') && !str_contains($res->body, 'Болт') && !str_contains($res->body, 'Краска');
    return ['13. Фильтр по единице «кг»', 'Только цемент', $ok ? 'Только цемент' : 'Фильтр не работает'];
};
$tests[] = function () {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items', ['code' => 'bolt-m8', 'name' => 'Дубль', 'quantity' => '1', 'unit' => 'шт']);
    return ['14. Дубликат кода', '422, строк 3', $res->status . ', строк ' . count_items($pdo)];
};
$tests[] = function () {
    [$r] = makeApp();
    $a = get($r, '/items/0')->status;
    $b = get($r, '/items/abc')->status;
    return ['15. id = 0 и id = abc', '404 и 404', $a . ' и ' . $b];
};
$tests[] = function () use ($validItem) {
    [$r, $pdo] = makeApp();
    $res = post($r, '/items', $validItem, false);
    return ['16. Create без CSRF', '403, строк 3', $res->status . ', строк ' . count_items($pdo)];
};
$tests[] = function () {
    [$r] = makeApp();
    $res = get($r, '/items/1/delete');
    return ['17. GET /items/1/delete', '405 (удаление GET запрещено)', (string) $res->status . ' (' . ($res->status === 405 ? 'удаление GET запрещено' : 'ошибка') . ')'];
};

$failed = 0;
foreach ($tests as $test) {
    [$name, $expected, $actual] = $test();
    $ok = $expected === $actual;
    $failed += $ok ? 0 : 1;
    printf("[%s] %-34s ожидалось: %-45s получено: %s\n", $ok ? 'OK  ' : 'FAIL', $name, $expected, $actual);
}

echo $failed === 0 ? "\nВсе тесты пройдены (" . count($tests) . ").\n" : "\nПровалено тестов: $failed\n";
exit($failed === 0 ? 0 : 1);
