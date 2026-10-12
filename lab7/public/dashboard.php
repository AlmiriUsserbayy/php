<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/session.php';

requireLogin();

$allowedThemes = ['light', 'dark'];
$theme = $_COOKIE['theme'] ?? 'light';

if (!is_string($theme) || !in_array($theme, $allowedThemes, true)) {
    $theme = 'light';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    if (isset($_POST['theme'])) {
        $themeValue = $_POST['theme'];
        if (in_array($themeValue, $allowedThemes, true)) {
            setcookie('theme', $themeValue, [
                'expires' => time() + 2592000,
                'path' => '/',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        header('Location: /dashboard.php');
        exit;
    }

    if (isset($_POST['grade'])) {
        if ($_SESSION['role'] !== 'teacher') {
            http_response_code(403);
            exit('Доступ запрещен!');
        }
        $_SESSION['success_message'] = "Оценка успешно выставлена студенту!";
        header('Location: /dashboard.php');
        exit;
    }
}

$successMsg = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Электронный журнал - Кабинет</title>
    <style>
        body.light { background: #ffffff; color: #000000; }
        body.dark { background: #222222; color: #ffffff; }
        .box { border: 1px solid #ccc; padding: 15px; margin-top: 15px; }
    </style>
</head>
<body class="<?= h($theme) ?>">

    <h1>Электронный журнал</h1>
    <p>Вы вошли как: <b><?= h($_SESSION['email']) ?></b></p>
    <p>Ваша роль: <b><?= h($_SESSION['role']) ?></b></p>

    <div class="box">
        <h3>Настройка интерфейса</h3>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
            <select name="theme">
                <option value="light" <?= $theme === 'light' ? 'selected' : '' ?>>Светлая тема</option>
                <option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?>>Тёмная тема</option>
            </select>
            <button type="submit">Сохранить тему</button>
        </form>
    </div>

    <div class="box">
        <?php if ($_SESSION['role'] === 'student'): ?>
            <h3>Ваши оценки (Режим Студента)</h3>
            <ul>
                <li>Программирование на PHP: <b>95 (А)</b></li>
                <li>Базы данных: <b>90 (А-)</b></li>
            </ul>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'teacher'): ?>
            <h3>Управление оценками (Режим Преподавателя)</h3>
            <?php if ($successMsg !== ''): ?>
                <p style="color: green;"><?= h($successMsg) ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                <p>Студент: Иванов И.И.</p>
                <p>
                    <label>Оценка: 
                    <input type="number" name="grade" min="0" max="100" required></label>
                </p>
                <button type="submit">Выставить оценку</button>
            </form>
        <?php endif; ?>
    </div>

    <p>
        <form method="post" action="/logout.php">
            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
            <button type="submit" style="background: #ff4d4d; color:white; border:none; padding: 10px; cursor:pointer;">Безопасный выход</button>
        </form>
    </p>

</body>
</html>