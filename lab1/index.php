<?php

const UNIVERSITY = "Алматинский технологический университет";
const DISCIPLINE = "Программирование на PHP";

$studentName = "Альмири Усербай";
$group = "ИС24-22";
$course = 3;

$length = 6;
$width = 4;

$area = $length * $width;
$perimeter = 2 * ($length + $width);

if ($area > 0) {
    $status = "Расчёт выполнен успешно";
} else {
    $status = "Некорректные данные";
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Лабораторная №1</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #f4f6f8;
        }

        .card {
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>

<div class="card">

    <h1><?= UNIVERSITY ?></h1>

    <h2><?= DISCIPLINE ?></h2>

    <h3>Карточка студента</h3>

    <p>
        <b>Студент:</b> <?= $studentName ?>
    </p>

    <p>
        <b>Группа:</b> <?= $group ?>
    </p>

    <p>
        <b>Курс:</b> <?= $course ?>
    </p>

    <hr>

    <h3>Вариант 5 — Площадь помещения</h3>

    <h4>Исходные данные:</h4>

    <p>
        Длина помещения:
        <b><?= $length ?> м</b>
    </p>

    <p>
        Ширина помещения:
        <b><?= $width ?> м</b>
    </p>

    <hr>

    <h4>Результат:</h4>

    <p>
        Площадь:
        <b><?= $area ?> м²</b>
    </p>

    <p>
        Периметр:
        <b><?= $perimeter ?> м</b>
    </p>

    <p class="success">
        <b>Статус:</b> <?= $status ?>
    </p>

    <p>
        <b>Дата формирования:</b>
        <?= date("d.m.Y") ?>
    </p>

</div>

</body>

</html>