<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../src/functions.php';

$jsonPath = __DIR__ . '/../var/data/schedule.json';
$fullSchedule = loadSchedule($jsonPath);

// Обработка фильтров (GET)
$filterDay = $_GET['day'] ?? '';
$filterGroup = $_GET['group'] ?? '';
$filterTeacher = $_GET['teacher'] ?? '';
$filterType = $_GET['type'] ?? '';

$filteredSchedule = filterSchedule($fullSchedule, $filterDay, $filterGroup, $filterTeacher, $filterType);
$sortedSchedule = sortSchedule($filteredSchedule);

// Поиск конфликтов в отфильтрованном или полном списке
$scheduleConflicts = checkConflicts($fullSchedule);

// Фича повышенной сложности: Экспорт выбранного дня в JSON файл
if (isset($_GET['action']) && $_GET['action'] === 'export_json' && $filterDay !== '') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="schedule_' . urlencode($filterDay) . '.json"');
    echo json_encode(array_values($sortedSchedule), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Обработка добавления в избранное (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_favorite') {
    $subject = $_POST['subject'] ?? '';
    if ($subject !== '') {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        if (!in_array($subject, $_SESSION['favorites'], true)) {
            $_SESSION['favorites'][] = $subject;
        }
    }
    header('Location: index.php?' . http_build_query($_GET));
    exit;
}

// Очистка избранного
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_favorites') {
    $_SESSION['favorites'] = [];
    header('Location: index.php?' . http_build_query($_GET));
    exit;
}

require_once __DIR__ . '/../templates/header.php';
?>

<h2>Фильтрация расписания</h2>
<form method="GET" action="index.php">
    <select name="day">
        <option value="">-- Выберите день --</option>
        <option value="Понедельник" <?= $filterDay === 'Понедельник' ? 'selected' : '' ?>>Понедельник</option>
        <option value="Вторник" <?= $filterDay === 'Вторник' ? 'selected' : '' ?>>Вторник</option>
        <option value="Среда" <?= $filterDay === 'Среда' ? 'selected' : '' ?>>Среда</option>
    </select>

    <input type="text" name="group" placeholder="Группа" value="<?= htmlspecialchars($filterGroup) ?>">
    <input type="text" name="teacher" placeholder="Преподаватель" value="<?= htmlspecialchars($filterTeacher) ?>">

    <select name="type">
        <option value="">-- Тип занятия --</option>
        <option value="Лекция" <?= $filterType === 'Лекция' ? 'selected' : '' ?>>Лекция</option>
        <option value="Практика" <?= $filterType === 'Практика' ? 'selected' : '' ?>>Практика</option>
        <option value="Лабораторная" <?= $filterType === 'Лабораторная' ? 'selected' : '' ?>>Лабораторная</option>
    </select>

    <button type="submit">Применить фильтры</button>
    <a href="index.php"><button type="button">Сбросить</button></a>
    
    <?php if ($filterDay !== ''): ?>
        <a href="index.php?<?= htmlspecialchars(http_build_query($_GET)) ?>&action=export_json">
            <button type="button" style="background-color: #17a2b8;">📥 Экспортировать день в JSON</button>
        </a>
    <?php endif; ?>
</form>

<?php if (!empty($scheduleConflicts)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <h3>⚠️ Обнаружены наложения в расписании:</h3>
        <ul>
            <?php foreach ($scheduleConflicts as $conflict): ?>
                <li><?= htmlspecialchars($conflict) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h2>Список занятий</h2>
<?php if (empty($sortedSchedule)): ?>
    <p style="color: red;">Занятий по выбранным критериям не найдено.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>День</th>
                <th>Время</th>
                <th>Дисциплина</th>
                <th>Группа</th>
                <th>Преподаватель</th>
                <th>Тип</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sortedSchedule as $lesson): ?>
                <tr>
                    <td><?= htmlspecialchars($lesson['day']) ?></td>
                    <td><?= htmlspecialchars($lesson['time']) ?></td>
                    <td><?= htmlspecialchars($lesson['subject']) ?></td>
                    <td><?= htmlspecialchars($lesson['group']) ?></td>
                    <td><?= htmlspecialchars($lesson['teacher']) ?></td>
                    <td><?= htmlspecialchars($lesson['type']) ?></td>
                    <td>
                        <form method="POST" action="index.php?<?= htmlspecialchars(http_build_query($_GET)) ?>" style="display:inline; margin:0; padding:0; box-shadow:none;">
                            <input type="hidden" name="action" value="add_favorite">
                            <input type="hidden" name="subject" value="<?= htmlspecialchars($lesson['subject']) ?>">
                            <button type="submit" class="favorite-btn">⭐ В избранное</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>⭐ Мои избранные дисциплины (в сессии)</h2>
<?php if (!empty($_SESSION['favorites'])): ?>
    <ul class="fav-list">
        <?php foreach ($_SESSION['favorites'] as $fav): ?>
            <li><?= htmlspecialchars($fav) ?></li>
        <?php endforeach; ?>
    </ul>
    <form method="POST" action="index.php?<?= htmlspecialchars(http_build_query($_GET)) ?>">
        <input type="hidden" name="action" value="clear_favorites">
        <button type="submit" style="background-color: #dc3545;">Очистить избранное</button>
    </form>
<?php else: ?>
    <p>Список избранного пуст.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>