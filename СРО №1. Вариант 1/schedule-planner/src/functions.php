<?php
declare(strict_types=1);

// Безопасное чтение данных из JSON
function loadSchedule(string $filePath): array {
    if (!file_exists($filePath)) {
        return [];
    }
    $content = file_get_contents($filePath);
    if ($content === false) {
        return [];
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// Фильтрация расписания по параметрам
function filterSchedule(array $schedule, string $day, string $group, string $teacher, string $type): array {
    return array_filter($schedule, function(array $lesson) use ($day, $group, $teacher, $type): bool {
        if ($day !== '' && $lesson['day'] !== $day) return false;
        if ($group !== '' && $lesson['group'] !== $group) return false;
        if ($teacher !== '' && strpos(mb_strtolower($lesson['teacher']), mb_strtolower($teacher)) === false) return false;
        if ($type !== '' && $lesson['type'] !== $type) return false;
        return true;
    });
}

// Сортировка по времени занятий
function sortSchedule(array $schedule): array {
    usort($schedule, function(array $a, array $b): int {
        return strcmp($a['time'], $b['time']);
    });
    return $schedule;
}

// Функция повышенной сложности: Поиск наложений (пересечений) по времени для одной группы
function checkConflicts(array $schedule): array {
    $conflicts = [];
    // Группируем по дням и группам, чтобы искать пересечения
    foreach ($schedule as $i => $lessonA) {
        foreach ($schedule as $j => $lessonB) {
            if ($i >= $j) continue; // Избегаем повторных проверок одной и той же пары
            
            // Если занятие в один день, в одно время и для одной группы/преподавателя
            if ($lessonA['day'] === $lessonB['day'] && $lessonA['time'] === $lessonB['time']) {
                if ($lessonA['group'] === $lessonB['group'] || $lessonA['teacher'] === $lessonB['teacher']) {
                    $conflicts[] = "Внимание! Пересечение времени ({$lessonA['day']} {$lessonA['time']}): '{$lessonA['subject']}' и '{$lessonB['subject']}'";
                }
            }
        }
    }
    return $conflicts;
}