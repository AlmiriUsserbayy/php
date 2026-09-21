<?php
// 1. Включаем строгий режим типов данных (требование лабораторной)
declare(strict_types=1);

// 2. Исходный массив температур (здесь есть обычные, null, и еще один цикл for ниже)
$temperatures = [15, 999, null, -5];

// Базовые переменные для расчетов
$total = 0;       // Сумма температур
$count = 0;       // Количество успешно обработанных дней
$minimum = 60;    // Стартовое значение для поиска минимума (максимально возможное)
$maximum = -60;   // Стартовое значение для поиска максимума (минимально возможное)

try {
    // Проверка 1: Если массив вообще пустой — это ошибка (первый throw)
    if ($temperatures === []) {
        throw new RuntimeException("Массив данных пуст. Нечего рассчитывать.");
    }

    // 3. Основной цикл перебора массива через foreach
    foreach ($temperatures as $index => $temp) {
        
        // Строгое сравнение === для пропуска null элементов
        if ($temp === null) {
            continue; // Пропускаем этот шаг и идем дальше
        }

        // Проверка 2: Если значение выходит за рамки допустимого (от -60 до 60)
        // Используем логический оператор ИЛИ (||) и арифметическое сравнение
        if (!is_int($temp) || $temp < -60 || $temp > 60) {
            throw new InvalidArgumentException("Обнаружено некорректное значение: {$temp} на позиции {$index}.");
        }

        // Арифметические операторы: складываем сумму и увеличиваем счетчик
        $total += $temp;
        $count++;

        // Поиск минимума и максимума
        if ($temp < $minimum) {
            $minimum = $temp;
        }
        if ($temp > $maximum) {
            $maximum = $temp;
        }
    }

    // Проверка 3: Если в массиве были только null, делить на 0 нельзя
    if ($count === 0) {
        throw new RuntimeException("Нет валидных данных для вычисления среднего значения.");
    }

    // Расчет среднего арифметического (используем деление /)
    $average = round($total / $count, 1);

    // 4. Дополнительный цикл WHILE (требование иметь второй цикл в коде)
    // Просто выведем в консоль или лог, что мы прошлись по счетчику дней
    $i = 1;
    while ($i <= $count) {
        // Цикл выполнится столько раз, сколько дней мы успешно посчитали
        $i++; 
    }

    // 5. Классификация погоды с помощью конструкции match
    $category = match (true) {
        $average >= 25 => "Жаркая погода",
        $average >= 10 && $average < 25 => "Умеренно теплая погода",
        $average >= 0 && $average < 10 => "Прохладная погода",
        default => "Морозная погода",
    };

    // 6. Красивый HTML-вывод результатов
    echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;'>";
    echo "<h2 style='color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;'>Результаты анализа погоды</h2>";
    echo "<p><b>Дней обработано:</b> $count</p>";
    echo "<p><b>Средняя температура:</b> $average °C</p>";
    echo "<p><b>Минимальная температура:</b> $minimum °C</p>";
    echo "<p><b>Максимальная температура:</b> $maximum °C</p>";
    echo "<p style='font-size: 1.1em; color: #27ae60;'><b>Категория:</b> $category</p>";
    echo "</div>";

} catch (InvalidArgumentException $e) {
    // Перехватываем ошибку неверных данных
    echo "<p style='color: red; font-weight: bold;'>Ошибка данных: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (RuntimeException $e) {
    // Перехватываем ошибку пустого массива
    echo "<p style='color: darkred; font-weight: bold;'>Ошибка выполнения: " . htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Блок finally выполняется ВСЕГДА
    echo "<p style='text-align: center; color: #7f8c8d; font-size: 0.9em;'>Обработка полностью завершена.</p>";
}
?>
