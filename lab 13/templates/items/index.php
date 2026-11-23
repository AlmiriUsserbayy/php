<div class="page-head">
    <h1>Складские позиции</h1>
    <a class="btn btn-primary" href="/items/create">+ Добавить позицию</a>
</div>

<form class="filter" method="get" action="/items">
    <label>Поиск (код или название)
        <input type="text" name="q" value="<?= e($search) ?>">
    </label>
    <label>Единица измерения
        <select name="unit">
            <option value="">Все</option>
            <?php foreach ($units as $u): ?>
                <option value="<?= e($u) ?>"<?= $u === $unit ? ' selected' : '' ?>><?= e($u) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn btn-primary">Применить</button>
    <a class="btn btn-secondary" href="/items">Сбросить</a>
</form>

<?php if ($items === []): ?>
    <p class="empty">Позиции не найдены.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Код</th><th>Название</th><th>Количество</th><th>Ед.</th><th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['code']) ?></td>
                <td><a href="/items/<?= (int) $item['id'] ?>"><?= e($item['name']) ?></a></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td><span class="badge"><?= e($item['unit']) ?></span></td>
                <td>
                    <div class="row-actions">
                        <a class="btn btn-secondary btn-sm" href="/items/<?= (int) $item['id'] ?>/edit">Изменить</a>
                        <form method="post" action="/items/<?= (int) $item['id'] ?>/delete"
                              onsubmit="return confirm('Удалить позицию?');">
                            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>