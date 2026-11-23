<?php
/** Общая форма для режимов create и edit. */
$value = static fn (string $key): string => e($item[$key] ?? '');
?>
<div class="page-head">
    <h1><?= e($title) ?></h1>
</div>

<form class="form-card" method="post" action="<?= e($action) ?>" novalidate>
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <label>Код
        <input type="text" name="code" value="<?= $value('code') ?>" maxlength="20">
        <?php if (isset($errors['code'])): ?><span class="error"><?= e($errors['code']) ?></span><?php endif; ?>
    </label>

    <label>Название
        <input type="text" name="name" value="<?= $value('name') ?>" maxlength="150">
        <?php if (isset($errors['name'])): ?><span class="error"><?= e($errors['name']) ?></span><?php endif; ?>
    </label>

    <label>Количество
        <input type="text" name="quantity" inputmode="numeric" value="<?= $value('quantity') ?>">
        <?php if (isset($errors['quantity'])): ?><span class="error"><?= e($errors['quantity']) ?></span><?php endif; ?>
    </label>

    <label>Единица измерения
        <select name="unit">
            <?php foreach ($units as $u): ?>
                <option value="<?= e($u) ?>"<?= ($item['unit'] ?? '') === $u ? ' selected' : '' ?>><?= e($u) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['unit'])): ?><span class="error"><?= e($errors['unit']) ?></span><?php endif; ?>
    </label>

    <div class="actions">
        <a class="btn btn-secondary" href="/items">← Назад</a>
        <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Сохранить' : 'Добавить' ?></button>
    </div>
</form>