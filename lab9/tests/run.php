<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Domain\{Club, ClubCode, Curator, Student, User};

$passed = 0;
$failed = 0;

function check(string $name, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "OK:   $name\n";
    } else {
        $failed++;
        echo "FAIL: $name\n";
    }
}

function throws(string $class, callable $action): bool
{
    try {
        $action();
    } catch (Throwable $e) {
        return get_class($e) === $class;
    }
    return false;
}

$curator = new Curator(1, 'Анна Ким', 'a.kim@example.kz', 'Кафедра ИС');
$s1 = new Student(10, 'Алия Нур', 'aliya@example.kz', 'ИС');
$s2 = new Student(11, 'Данияр Серик', 'daniyar@example.kz', 'ИС');
$s3 = new Student(12, 'Мадина Ахмет', 'madina@example.kz', 'ВТ');

check('1. Корректные Student и Curator создаются', $s1->id === 10 && $curator->department === 'Кафедра ИС');

check('2. Пустое имя -> InvalidArgumentException',
    throws(InvalidArgumentException::class, fn() => new Student(2, '  ', 'x@example.kz', 'ИС')));

check('3. Неверный email -> InvalidArgumentException',
    throws(InvalidArgumentException::class, fn() => new Student(2, 'Иван Иванов', 'bad-email', 'ИС')));

check('4. Лимит клуба 0 -> InvalidArgumentException',
    throws(InvalidArgumentException::class, fn() => new Club(new ClubCode('ROB-01'), 'Клуб', $curator, 0)));

check('5. Неверный код клуба -> InvalidArgumentException',
    throws(InvalidArgumentException::class, fn() => new ClubCode('robotics')));

$club = new Club(new ClubCode('ROB-01'), 'Клуб робототехники', $curator, 2);
$m = $club->join($s1);
check('6. Вступление: членство создано, мест стало меньше',
    $m->studentId === 10 && $m->clubCode->value === 'ROB-01' && $club->freePlaces() === 1);

// лимит 2 и одно место свободно, поэтому срабатывает именно проверка дубля
check('7. Повторное вступление -> DomainException',
    throws(DomainException::class, fn() => $club->join($s1)));

$club->join($s2);
check('8. Сверх лимита -> DomainException',
    throws(DomainException::class, fn() => $club->join($s3)));

check('9. После отказа число участников не изменилось',
    count($club->memberships()) === 2 && $club->freePlaces() === 0);

$labels = array_map(fn(User $u): string => $u->roleLabel(), [$curator, $s1]);
check('10. Полиморфизм: разные подписи ролей', $labels[0] !== $labels[1]);

check('11. Изменение readonly ID -> Error',
    throws(Error::class, function () use ($s1): void { $s1->id = 99; }));

$club->leave($s2);
check('12. Выход освобождает место', $club->freePlaces() === 1 && !$club->hasMember($s2));
$club->join($s2);
check('13. После выхода можно вступить снова', $club->hasMember($s2));

$club2 = new Club(new ClubCode('CHS-02'), 'Шахматный клуб', $curator, 3);
check('14. Выход не участника -> DomainException',
    throws(DomainException::class, fn() => $club2->leave($s3)));

check('15. Лимит ниже числа участников -> DomainException',
    throws(DomainException::class, fn() => $club->changeCapacity(1)));

$copy = $club->memberships();
$copy[] = 'мусор';
check('16. Внешняя запись в копию не меняет клуб', count($club->memberships()) === 2);

echo "\nПройдено: $passed, провалено: $failed\n";
exit($failed === 0 ? 0 : 1);
