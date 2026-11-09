<?php

declare(strict_types=1);

namespace University\TuitionPayments\Model;

use University\TuitionPayments\Exception\InvalidInvoiceException;

final class Invoice
{
    private int $paid = 0;

    public function __construct(
        public readonly string $number,
        public readonly string $student,
        public readonly int $amount,
    ) {
        if (preg_match('/^INV[0-9]{3,6}$/', $number) !== 1) {
            throw new InvalidInvoiceException('Неверный номер счёта.');
        }
        if (trim($student) === '') {
            throw new InvalidInvoiceException('Имя студента обязательно.');
        }
        if ($amount <= 0) {
            throw new InvalidInvoiceException('Сумма начисления должна быть положительной.');
        }
    }

    public function paid(): int
    {
        return $this->paid;
    }

    public function balance(): int
    {
        return $this->amount - $this->paid;
    }

    public function isClosed(): bool
    {
        return $this->balance() === 0;
    }

    public function applyPayment(Payment $payment): void
    {
        if ($payment->amount > $this->balance()) {
            throw new InvalidInvoiceException('Платёж превышает остаток по счёту.');
        }
        $this->paid += $payment->amount;
    }
}
