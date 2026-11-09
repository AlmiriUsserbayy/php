<?php

declare(strict_types=1);

namespace University\TuitionPayments\Model;

use University\TuitionPayments\Exception\InvalidInvoiceException;

final readonly class Payment
{
    public function __construct(
        public string $invoiceNumber,
        public int $amount,
    ) {
        if ($amount <= 0) {
            throw new InvalidInvoiceException('Сумма платежа должна быть положительной.');
        }
    }
}
