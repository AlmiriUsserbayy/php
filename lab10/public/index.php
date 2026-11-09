<?php
declare(strict_types=1);

// Подключаем автозагрузчик из корня проекта
require_once dirname(__DIR__) . '/bootstrap.php';

use App\Validators\{PdfValidator, ImageValidator};
use App\Services\ValidationService;
use App\ValueObject\Document;
use App\Exceptions\ValidationException;

$service = new ValidationService([
    new PdfValidator(),
    new ImageValidator(),
]);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты проверки документов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; color: #333; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .success { color: green; font-weight: bold; }
        .error { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Результаты работы службы проверки документов</h1>

    <?php
    // Пример 1: Успешный PDF
    try {
        $result = $service->validateDocument(new Document('contract.pdf', '%PDF-1.4 official text', 'pdf'));
        echo "<div class='card'>";
        echo "<p class='success'>✔ Документ успешно проверен!</p>";
        echo "<p>Имя: {$result->documentName}</p>";
        echo "<p>ID проверки: {$result->id}</p>";
        echo "<p>Время: " . $result->checkedAt->format('Y-m-d H:i:s') . "</p>";
        echo "</div>";
    } catch (\Throwable $e) {
        echo "<div class='card error'>Ошибка: {$e->getMessage()}</div>";
    }

    // Пример 2: Ошибочный документ (вызов исключения)
    try {
        $service->validateDocument(new Document('bad.pdf', 'INVALID-FORMAT', 'pdf'));
    } catch (ValidationException | \InvalidArgumentException $e) {
        echo "<div class='card error'>";
        echo "<p class='error'>✖ Перехвачено ожидаемое исключение:</p>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    ?>
</body>
</html>