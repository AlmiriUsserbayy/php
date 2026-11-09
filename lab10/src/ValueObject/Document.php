<?php
declare(strict_types=1);
namespace App\ValueObject;

final readonly class Document {
    public function __construct(
        public string $name,
        public string $content,
        public string $type, // 'pdf' или 'image'
    ) {
        if (trim($name) === '' || trim($content) === '') {
            throw new \InvalidArgumentException('Имя и содержимое документа обязательны.');
        }
    }
}