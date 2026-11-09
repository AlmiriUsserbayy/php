<?php

declare(strict_types=1);

namespace University\TuitionPayments\Contract;

use University\TuitionPayments\Model\Invoice;

interface InvoiceRepositoryInterface
{
    public function add(Invoice $invoice): void;

    /** @return list<Invoice> */
    public function all(): array;

    public function findByNumber(string $number): ?Invoice;
}
