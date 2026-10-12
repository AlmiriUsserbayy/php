<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/session.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
$email = '';

$_SESSION['login_csrf'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailValue = $_POST['email'] ?? null;
    $passwordValue = $_POST['password'] ?? null;
    $tokenValue = $_POST['csrf_token'] ?? null;

    if (is_string($emailValue) && is_string($passwordValue) && is_string($tokenValue) && hash_equals($_SESSION['login_csrf'], $tokenValue)) {
        $email = strtolower(trim($emailValue));
        
        $studentHash = password_hash('Student123!', PASSWORD_DEFAULT);
        $teacherHash = password_hash('Teacher123!', PASSWORD_DEFAULT);

        if ($email === 'student@atu.edu.kz' && password_verify($passwordValue, $studentHash)) {
            loginUser(101, $email, 'student');
            header('Location: /dashboard.php');
            exit;
        } elseif ($email === 'teacher@atu.edu.kz' && password_verify($passwordValue, $teacherHash)) {
            loginUser(202, $email, 'teacher');
            header('Location: /dashboard.php');
            exit;
        } else {
            $error = 'Неверный e-mail или пароль.';
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в Электронный журнал</title>
</head>
<body>
    <h1>Вход в систему</h1>
    <?php if (isset($_GET['expired'])): ?>
        <p style="color: red;">Сеанс завершён из-за бездействия.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p style="color: red;"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/login.php">
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['login_csrf']) ?>">
        <p>
            <label>E-mail:<br>
            <input name="email" type="email" required value="<?= h($email) ?>"></label>
        </p>
        <p>
            <label>Пароль:<br>
            <input name="password" type="password" required></label>
        </p>
        <button type="submit">Войти</button>
    </form>
    <p>Для теста:<br>student@atu.edu.kz / Student123!<br>teacher@atu.edu.kz / Teacher123!</p>
</body>
</html>