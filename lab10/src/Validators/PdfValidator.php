<?php
declare(strict_types=1);
namespace App\Validators;

use App\Exceptions\InvalidDocumentException;
use App\ValueObject\Document;

final class PdfValidator extends AbstractValidator {
    public function supports(Document $document): bool {
        return $document->type === 'pdf';
    }

    protected function checkStructure(string $content): void {
        // Имитация проверки PDF-заголовка
        if (!str_starts_with($content, '%PDF')) {
            throw new InvalidDocumentException('Неверная структура PDF-файла (отсутствует сигнатура).');
        }
    }

    protected function performValidation(Document $document): void {
        if ($document->name === 'corrupted.pdf') {
            throw new \RuntimeException('Внутренняя ошибка парсера PDF.');
        }
    }
}