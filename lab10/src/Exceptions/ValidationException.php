<?php
declare(strict_types=1);
namespace App\Exceptions;

final class ValidationException extends \RuntimeException {
    public function __construct(
        string $message,
        public readonly string $documentType,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}