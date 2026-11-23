<?php

declare(strict_types=1);

namespace App\Validation;

final class ItemValidator
{
    public const UNITS = ['шт', 'кг', 'л', 'м', 'упак'];

    /**
     * @return array{0: array<string, string>, 1: array{code: string, name: string, quantity: int|string, unit: string}}
     */
    public static function validate(array $input): array
    {
        $str = static fn (string $key): string => is_string($input[$key] ?? null) ? trim($input[$key]) : '';

        $code     = mb_strtoupper($str('code'));
        $name     = $str('name');
        $quantity = $str('quantity');
        $unit     = $str('unit');

        $errors = [];

        if ($code === '' || preg_match('/^[A-Z0-9-]{2,20}$/', $code) !== 1) {
            $errors['code'] = 'Код: 2–20 символов (латиница, цифры, дефис).';
        }
        if (mb_strlen($name) < 2 || mb_strlen($name) > 150) {
            $errors['name'] = 'Название: от 2 до 150 символов.';
        }
        if (filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]) === false) {
            $errors['quantity'] = 'Количество: целое число от 0 до 1 000 000.';
        }
        if (!in_array($unit, self::UNITS, true)) {
            $errors['unit'] = 'Выберите единицу измерения из списка.';
        }

        $data = [
            'code'     => $code,
            'name'     => $name,
            'quantity' => isset($errors['quantity']) ? $quantity : (int) $quantity,
            'unit'     => $unit,
        ];

        return [$errors, $data];
    }
}
