<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final readonly class ItemRepository
{
    public function __construct(private PDO $pdo) {}

    /** Список с необязательным фильтром по единице и поиском по коду/названию. */
    public function all(?string $unit = null, ?string $search = null): array
    {
        $sql    = 'SELECT id, code, name, quantity, unit FROM items';
        $where  = [];
        $params = [];

        if ($unit !== null && $unit !== '') {
            $where[]        = 'unit = :unit';
            $params['unit'] = $unit;
        }
        if ($search !== null && $search !== '') {
            $where[]      = '(name LIKE :q1 OR code LIKE :q2)';
            $params['q1'] = '%' . $search . '%';
            $params['q2'] = '%' . $search . '%';
        }
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY name, id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, name, quantity, unit FROM items WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO items (code, name, quantity, unit)
             VALUES (:code, :name, :quantity, :unit)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE items
             SET code = :code, name = :name, quantity = :quantity, unit = :unit
             WHERE id = :id'
        );
        $stmt->execute($data + ['id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM items WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
