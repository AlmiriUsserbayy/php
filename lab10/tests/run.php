<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

use App\Validators\{PdfValidator, ImageValidator};
use App\Services\ValidationService;
use App\ValueObject\Document;
use App\Exceptions\{InvalidDocumentException, ValidationException};

function expectException(callable $action, string $label): void {
    try {
        $action();
        echo "FAIL: $label\n";
    } catch (\Throwable $e) {
        echo "OK: $label -> " . get_class($e) . "\n";
    }
}

$service = new ValidationService([
    new PdfValidator(),
    new ImageValidator(),
]);

// 1. Успешный PDF
$pdfResult = $service->validateDocument(new Document('report.pdf', '%PDF-1.4 content', 'pdf'));
assert($pdfResult->documentName === 'report.pdf');
echo "OK: Успешная проверка PDF\n";

// 2. Успешная Image
$imgResult = $service->validateDocument(new Document('photo.png', 'PNG-image-bytes-long-enough', 'image'));
assert($imgResult->documentName === 'photo.png');
echo "OK: Успешная проверка Image\n";

// 3-10. Проверка граничных и ошибочных сценариев
expectException(
    fn() => $service->validateDocument(new Document('bad.pdf', 'NOT-PDF', 'pdf')),
    'Неверная структура PDF (InvalidDocumentException)'
);

expectException(
    fn() => $service->validateDocument(new Document('corrupted.pdf', '%PDF-1.4', 'pdf')),
    'Внутренняя ошибка парсера PDF (ValidationException с previous)'
);

expectException(
    fn() => $service->validateDocument(new Document('photo.jpg', 'INVALID', 'image')),
    'Неверный формат изображения'
);

expectException(
    fn() => $service->validateDocument(new Document('doc.txt', 'some text', 'word')),
    'Неизвестный тип документа (неподдерживаемый канал/валидатор)'
);

// Блок finally демонстрация
try {
    $service->validateDocument(new Document('bad.pdf', 'WRONG', 'pdf'));
} catch (\Throwable $e) {
    echo "Перехвачено исключение: {$e->getMessage()}\n";
} finally {
    echo "Сценарий проверки завершён (блок finally сработал).\n";
}

echo "Все базовые тесты завершены.\n";