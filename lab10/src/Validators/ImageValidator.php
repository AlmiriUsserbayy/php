<?php
declare(strict_types=1);
namespace App\Validators;

use App\Exceptions\InvalidDocumentException;
use App\ValueObject\Document;

final class ImageValidator extends AbstractValidator {
    public function supports(Document $document): bool {
        return $document->type === 'image';
    }

    protected function checkStructure(string $content): void {
        // Имитация проверки заголовка изображения (например, PNG/JPEG)
        if (!str_starts_with($content, 'GIF') && !str_starts_with($content, 'PNG')) {
            throw new InvalidDocumentException('Неподдерживаемый или поврежденный формат изображения.');
        }
    }

    protected function performValidation(Document $document): void {
        if (strlen($document->content) < 10) {
            throw new \RuntimeException('Разрешение изображения слишком мало.');
        }
    }
}