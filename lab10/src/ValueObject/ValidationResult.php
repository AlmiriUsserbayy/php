<?php
declare(strict_types=1);
namespace App\ValueObject;

final readonly class ValidationResult {
    public function __construct(
        public string $id,
        public string $documentName,
        public \DateTimeImmutable $checkedAt,
    ) {}
}