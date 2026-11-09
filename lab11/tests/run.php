<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use University\TuitionPayments\Exception\InvalidInvoiceException;
use University\TuitionPayments\Model\Invoice;
use University\TuitionPayments\Model\Payment;
use University\TuitionPayments\Repository\InMemoryInvoiceRepository;
use University\TuitionPayments\Service\PaymentService;

$passed = 0;

function check(string $name, bool $condition): void
{
    global $passed;
    if (!$condition) {
        throw new RuntimeException("ПРОВАЛ: $name");
    }
    $passed++;
    echo "[OK] $name\n";
}

function expectException(string $name, callable $action, string $expected): void
{
    try {
        $action();
    } catch (Throwable $error) {
        check($name, $error instanceof $expected);
        return;
    }
    throw new RuntimeException("ПРОВАЛ: $name — исключение не выброшено");
}

$repository = new InMemoryInvoiceRepository();
$service = new PaymentService($repository);

// 1. Начисление
$service->charge(new Invoice('INV001', 'Айдар', 100000));
check('Начисление: счёт найден по номеру', $repository->findByNumber('INV001')?->student === 'Айдар');

// 2. Частичная оплата
$invoice = $service->pay(new Payment('INV001', 40000));
check('Частичная оплата: остаток 60000', $invoice->balance() === 60000);

// 3. Полная оплата (граница: остаток ровно 0)
$service->pay(new Payment('INV001', 60000));
check('Полная оплата: счёт закрыт', $repository->findByNumber('INV001')?->isClosed() === true);
check('Закрытый счёт не входит в открытые', $service->openInvoices() === []);

// 4. Ошибки предметных данных
expectException('Неверный номер счёта', fn () => new Invoice('bad', 'Айдар', 1000), InvalidInvoiceException::class);
expectException('Нулевая сумма платежа', fn () => new Payment('INV001', 0), InvalidInvoiceException::class);
expectException('Переплата', fn () => $service->pay(new Payment('INV001', 1)), InvalidInvoiceException::class);
expectException('Платёж по несуществующему счёту', fn () => $service->pay(new Payment('INV999', 10)), InvalidInvoiceException::class);

// 5. Дубликат
expectException('Повторный номер счёта', fn () => $service->charge(new Invoice('INV001', 'Другой', 5)), LogicException::class);

// 6. PSR-4: классы загружаются автозагрузчиком без require
check('PSR-4: Invoice загружен Composer', class_exists(Invoice::class));
check(
    'PSR-4: путь класса соответствует правилу',
    str_ends_with((new ReflectionClass(PaymentService::class))->getFileName(), '/src/Service/PaymentService.php'),
);

echo "Все проверки пройдены ($passed).\n";
