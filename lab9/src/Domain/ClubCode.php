<?php
declare(strict_types=1);

namespace App\Domain;

final readonly class ClubCode
{
    public function __construct(public string $value)
    {
        if (preg_match('/^[A-Z]{2,5}-\d{2}$/', $value) !== 1) {
            throw new \InvalidArgumentException(
                'Формат кода клуба: две-пять заглавных латинских букв, дефис и две цифры (например, ROB-01).'
            );
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
