<?php

declare(strict_types=1);

namespace University\TuitionPayments\Service;

use University\TuitionPayments\Contract\InvoiceRepositoryInterface;
use University\TuitionPayments\Exception\InvalidInvoiceException;
use University\TuitionPayments\Model\Invoice;
use University\TuitionPayments\Model\Payment;

final readonly class PaymentService
{
    public function __construct(
        private InvoiceRepositoryInterface $repository,
    ) {
    }

    /** Начисление: регистрирует новый счёт. */
    public function charge(Invoice $invoice): void
    {
        if ($this->repository->findByNumber($invoice->number) !== null) {
            throw new \LogicException('Счёт с таким номером уже существует.');
        }
        $this->repository->add($invoice);
    }

    /** Оплата: уменьшает остаток по счёту. */
    public function pay(Payment $payment): Invoice
    {
        $invoice = $this->repository->findByNumber($payment->invoiceNumber)
            ?? throw new InvalidInvoiceException('Счёт не найден.');
        $invoice->applyPayment($payment);

        return $invoice;
    }

    /** @return list<Invoice> */
    public function openInvoices(): array
    {
        return array_values(array_filter(
            $this->repository->all(),
            static fn (Invoice $invoice): bool => !$invoice->isClosed(),
        ));
    }
}
