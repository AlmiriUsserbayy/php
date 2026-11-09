<?php
declare(strict_types=1);
namespace App\Validators;

use App\Contracts\DocumentValidatorInterface;
use App\Exceptions\ValidationException;
use App\Support\LogsActivity;
use App\ValueObject\Document;
use App\ValueObject\ValidationResult;

abstract class AbstractValidator implements DocumentValidatorInterface {
    use LogsActivity;

    final public function validate(Document $document): ValidationResult {
        if (!$this->supports($document)) {
            throw new ValidationException(
                "Валидатор не поддерживает тип документа: {$document->type}",
                $document->type
            );
        }

        $this->checkStructure($document->content);

        try {
            $this->performValidation($document);
            $this->log("Документ '{$document->name}' успешно проверен.");

            return new ValidationResult(
                bin2hex(random_bytes(8)),
                $document->name,
                new \DateTimeImmutable()
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ValidationException(
                "Ошибка проверки документа: " . $e->getMessage(),
                $document->type,
                $e
            );
        }
    }

    abstract protected function checkStructure(string $content): void;
    abstract protected function performValidation(Document $document): void;
}