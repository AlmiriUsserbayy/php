<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Domain\{Club, ClubCode, Curator, Student, User};

function printUserCard(User $user): void
{
    printf(
        "#%d %s — %s <%s>\n",
        $user->id,
        $user->fullName(),
        $user->roleLabel(),
        $user->email,
    );
}

$curator = new Curator(1, 'Анна Ким', 'a.kim@example.kz', 'Кафедра ИС');
$aliya   = new Student(10, 'Алия Нур', 'aliya@example.kz', 'ИС');
$daniyar = new Student(11, 'Данияр Серик', 'daniyar@example.kz', 'ИС');
$madina  = new Student(12, 'Мадина Ахмет', 'madina@example.kz', 'ВТ');

$club = new Club(new ClubCode('ROB-01'), 'Клуб робототехники', $curator, 2);

echo "=== Пользователи ===\n";
foreach ([$curator, $aliya, $daniyar, $madina] as $user) {
    printUserCard($user);        // полиморфный вызов: тип не проверяется
}

echo "\n=== Вступление в клуб «{$club->name}» (лимит {$club->capacity()}) ===\n";
foreach ([$aliya, $daniyar, $aliya, $madina] as $student) {
    try {
        $club->join($student);
        echo "OK: {$student->fullName()} вступил(а). Свободных мест: {$club->freePlaces()}\n";
    } catch (DomainException $e) {
        echo "Отказ для {$student->fullName()}: {$e->getMessage()}\n";
    }
}

echo "\n=== Участники ===\n";
$names = [10 => $aliya, 11 => $daniyar, 12 => $madina];
foreach ($club->memberships() as $m) {
    printf("%s — с %s\n", $names[$m->studentId]->fullName(), $m->joinedAt->format('Y-m-d H:i'));
}
