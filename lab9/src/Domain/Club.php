<?php
declare(strict_types=1);

namespace App\Domain;

final class Club
{
    /** @var list<Membership> */
    private array $memberships = [];

    public function __construct(
        public readonly ClubCode $code,
        public readonly string $name,
        public readonly Curator $curator,
        private int $capacity,
    ) {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Название клуба обязательно.');
        }
        if ($capacity < 1) {
            throw new \InvalidArgumentException('Лимит участников должен быть положительным.');
        }
    }

    public function join(Student $student): Membership
    {
        // Все проверки выполняются до изменения состояния.
        if ($this->hasMember($student)) {
            throw new \DomainException('Студент уже состоит в клубе.');
        }
        if ($this->isFull()) {
            throw new \DomainException('В клубе нет свободных мест.');
        }

        $membership = new Membership($student->id, $this->code, new \DateTimeImmutable());
        $this->memberships[] = $membership;

        return $membership;
    }

    public function leave(Student $student): void
    {
        foreach ($this->memberships as $index => $membership) {
            if ($membership->studentId === $student->id) {
                unset($this->memberships[$index]);
                $this->memberships = array_values($this->memberships);
                return;
            }
        }
        throw new \DomainException('Студент не состоит в клубе.');
    }

    public function changeCapacity(int $newCapacity): void
    {
        if ($newCapacity < 1) {
            throw new \InvalidArgumentException('Лимит участников должен быть положительным.');
        }
        if ($newCapacity < count($this->memberships)) {
            throw new \DomainException('Нельзя уменьшить лимит ниже числа текущих участников.');
        }
        $this->capacity = $newCapacity;
    }

    public function hasMember(Student $student): bool
    {
        foreach ($this->memberships as $membership) {
            if ($membership->studentId === $student->id) {
                return true;
            }
        }
        return false;
    }

    public function isFull(): bool
    {
        return count($this->memberships) >= $this->capacity;
    }

    public function freePlaces(): int
    {
        return $this->capacity - count($this->memberships);
    }

    public function capacity(): int
    {
        return $this->capacity;
    }

    /** @return list<Membership> */
    public function memberships(): array
    {
        return $this->memberships; // массив передаётся по значению: внешний код получает копию
    }
}
