<?php
require_once __DIR__ . '/../_stock.php';
[$cls, $label] = stock_status((int) $item['quantity']);
?>
<div class="page-head">
    <h1><?= e($item['name']) ?></h1>
</div>

<div class="item-card">
    <div class="item-top">
        <span class="sku"><?= e($item['code']) ?></span>
        <span class="status <?= $cls ?>"><?= e($label) ?></span>
    </div>
    <div class="item-body">
        <div>
            <div class="qty-label">Остаток на складе</div>
            <div class="qty"><?= (int) $item['quantity'] ?> <small><?= e($item['unit']) ?></small></div>
        </div>
    </div>
    <dl>
        <dt>Код позиции</dt><dd><?= e($item['code']) ?></dd>
        <dt>Единица измерения</dt><dd><?= e($item['unit']) ?></dd>
    </dl>
</div>

<div class="actions">
    <a class="btn btn-secondary" href="/items">← Назад</a>
    <a class="btn btn-primary" href="/items/<?= (int) $item['id'] ?>/edit">Изменить</a>

    <form class="spacer" method="post" action="/items/<?= (int) $item['id'] ?>/delete"
          onsubmit="return confirm('Удалить позицию?');">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <button type="submit" class="btn btn-danger">Удалить</button>
    </form>
</div>