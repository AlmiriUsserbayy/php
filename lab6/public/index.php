<?php
declare(strict_types=1);

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function postString(string $key): ?string {
    $value = $_POST[$key] ?? null;
    return is_string($value) ? $value : null;
}

$allowedGroups = ['ИС-23-21', 'ИС-23-22', 'ИС-23-23'];
$allowedDisciplines = ['Программирование на PHP', 'Базы данных', 'Веб-разработка'];
$allowedFormats = ['offline', 'online'];

$values = ['full_name' => '', 'email' => '', 'group' => '', 'discipline' => '', 'format' => ''];
$errors = [];
$success = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $fullName = postString('full_name');
    $email = postString('email');
    $group = postString('group');
    $discipline = postString('discipline');
    $format = postString('format');

    $values['full_name'] = $fullName === null ? '' : trim($fullName);
    $values['email'] = $email === null ? '' : trim($email);
    $values['group'] = $group ?? '';
    $values['discipline'] = $discipline ?? '';
    $values['format'] = $format ?? '';

    if ($fullName === null || $values['full_name'] === '') {
        $errors['full_name'] = 'Укажите ФИО.';
    }
    if ($email === null || $values['email'] === '') {
        $errors['email'] = 'Укажите E-mail.';
    } elseif (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Некорректный E-mail.';
    }
    if (!in_array($values['group'], $allowedGroups, true)) {
        $errors['group'] = 'Выберите группу.';
    }
    if (!in_array($values['discipline'], $allowedDisciplines, true)) {
        $errors['discipline'] = 'Выберите дисциплину.';
    }
    if (!in_array($values['format'], $allowedFormats, true)) {
        $errors['format'] = 'Выберите формат.';
    }
    if (!isset($_POST['agreement']) || $_POST['agreement'] !== '1') {
        $errors['agreement'] = 'Подтвердите согласие.';
    }

    $success = $errors === [];
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Лабораторная работа 6</title>
    <style>
        body { font-family: sans-serif; margin: 30px; background: #f4f4f9; }
        main { max-width: 500px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 0 auto; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="email"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; margin: 5px 0; }
        .ok { background: #e2f0d9; color: #385723; padding: 15px; border-radius: 5px; }
        button { margin-top: 20px; padding: 10px; width: 100%; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
<main>
    <h1>Регистрация на дисциплину</h1>
    <?php if ($success): ?>
        <div class="ok">
            <h3>Заявка принята!</h3>
            <p>Студент: <?= h($values['full_name']) ?></p>
            <p>E-mail: <?= h($values['email']) ?></p>
            <p>Дисциплина: <?= h($values['discipline']) ?></p>
        </div>
    <?php else: ?>
        <form method="post" action="">
            <label>ФИО: <input type="text" name="full_name" value="<?= h($values['full_name']) ?>" required></label>
            <?php if (isset($errors['full_name'])): ?><p class="err"><?= h($errors['full_name']) ?></p><?php endif; ?>

            <label>E-mail: <input type="email" name="email" value="<?= h($values['email']) ?>" required></label>
            <?php if (isset($errors['email'])): ?><p class="err"><?= h($errors['email']) ?></p><?php endif; ?>

            <label>Группа:
                <select name="group" required>
                    <option value="">-- Выберите группу --</option>
                    <?php foreach ($allowedGroups as $item): ?>
                        <option value="<?= h($item) ?>" <?= $values['group'] === $item ? 'selected' : '' ?>><?= h($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['group'])): ?><p class="err"><?= h($errors['group']) ?></p><?php endif; ?>

            <label>Дисциплина:
                <select name="discipline" required>
                    <option value="">-- Выберите предмет --</option>
                    <?php foreach ($allowedDisciplines as $item): ?>
                        <option value="<?= h($item) ?>" <?= $values['discipline'] === $item ? 'selected' : '' ?>><?= h($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['discipline'])): ?><p class="err"><?= h($errors['discipline']) ?></p><?php endif; ?>

            <fieldset style="margin-top:15px;">
                <legend>Формат</legend>
                <?php foreach ($allowedFormats as $item): ?>
                    <label style="font-weight:normal; display:inline-block; margin-right:10px;">
                        <input type="radio" name="format" value="<?= h($item) ?>" <?= $values['format'] === $item ? 'checked' : '' ?>> <?= h($item) ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <?php if (isset($errors['format'])): ?><p class="err"><?= h($errors['format']) ?></p><?php endif; ?>

            <label style="font-weight:normal; margin-top:15px;"><input type="checkbox" name="agreement" value="1"> Согласен с правилами</label>
            <?php if (isset($errors['agreement'])): ?><p class="err"><?= h($errors['agreement']) ?></p><?php endif; ?>

            <button type="submit">Отправить</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
