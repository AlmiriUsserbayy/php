<?php

/** Порог «мало на складе» (в единицах позиции). */
const STOCK_LOW_LIMIT = 50;

/** @return array{0: string, 1: string} [css-класс, подпись] */
function stock_status(int $quantity): array
{
    if ($quantity <= 0) {
        return ['out', 'Нет в наличии'];
    }
    if ($quantity <= STOCK_LOW_LIMIT) {
        return ['low', 'Мало'];
    }

    return ['ok', 'В наличии'];
}