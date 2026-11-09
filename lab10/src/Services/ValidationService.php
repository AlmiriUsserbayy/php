<?php
declare(strict_types=1);
namespace App\Services;

use App\Contracts\DocumentValidatorInterface;
use App\Exceptions\ValidationException;
use App\ValueObject\Document;
use App\ValueObject\ValidationResult;

final class ValidationService {
    /** @param list<DocumentValidatorInterface> $validators */
    public function __construct(private array $validators) {}

    public function validateDocument(Document $document): ValidationResult {
        foreach ($this->validators as $validator) {
            if ($validator->supports($document)) {
                return $validator->validate($document);
            }
        }
        throw new ValidationException("Не найден подходящий валидатор для типа: {$document->type}", $document->type);
    }
}