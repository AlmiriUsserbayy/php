<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use University\TuitionPayments\Model\Invoice;
use University\TuitionPayments\Model\Payment;
use University\TuitionPayments\Repository\InMemoryInvoiceRepository;
use University\TuitionPayments\Service\PaymentService;

$service = new PaymentService(new InMemoryInvoiceRepository());

$service->charge(new Invoice('INV001', 'Айдар Сериков', 450000));
$service->charge(new Invoice('INV002', 'Мадина Ахметова', 450000));
$service->charge(new Invoice('INV003', 'Тимур Нурланов', 300000));

$service->pay(new Payment('INV001', 450000));
$service->pay(new Payment('INV002', 200000));

echo "Открытые счета:\n";
foreach ($service->openInvoices() as $invoice) {
    printf(
        "%s | %s | начислено %d | оплачено %d | остаток %d\n",
        $invoice->number,
        $invoice->student,
        $invoice->amount,
        $invoice->paid(),
        $invoice->balance(),
    );
}
