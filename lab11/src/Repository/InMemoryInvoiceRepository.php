<?php

declare(strict_types=1);

namespace University\TuitionPayments\Repository;

use University\TuitionPayments\Contract\InvoiceRepositoryInterface;
use University\TuitionPayments\Model\Invoice;

final class InMemoryInvoiceRepository implements InvoiceRepositoryInterface
{
    /** @var array<string, Invoice> */
    private array $items = [];

    public function add(Invoice $invoice): void
    {
        $this->items[$invoice->number] = $invoice;
    }

    public function all(): array
    {
        return array_values($this->items);
    }

    public function findByNumber(string $number): ?Invoice
    {
        return $this->items[$number] ?? null;
    }
}
