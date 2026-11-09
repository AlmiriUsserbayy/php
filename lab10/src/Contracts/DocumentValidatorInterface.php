<?php
declare(strict_types=1);
namespace App\Contracts;

use App\ValueObject\Document;
use App\ValueObject\ValidationResult;

interface DocumentValidatorInterface {
    public function supports(Document $document): bool;
    public function validate(Document $document): ValidationResult;
}