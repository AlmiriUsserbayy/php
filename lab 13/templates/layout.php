<?php
/** @var string $content */
$pageTitle = isset($title) ? (string) $title : 'Склад';
$flash = \App\Support\Flash::consume();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — Склад</title>
    <style>
        :root {
            --bg: #e9ecef;
            --card: #ffffff;
            --text: #1f2933;
            --muted: #6b7684;
            --steel: #1f2933;
            --steel-2: #323f4b;
            --amber: #f5b800;
            --amber-dark: #d99c00;
            --border: #d5d9de;
            --danger: #c81e1e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 0 1rem; }
        a { color: #8a5a00; }
        a:hover { color: var(--text); }

        /* Шапка в стиле склада */
        header.top { background: var(--steel); }
        header.top::after {
            content: ""; display: block; height: 10px;
            background: repeating-linear-gradient(-45deg, var(--amber) 0 14px, var(--steel) 14px 28px);
        }
        header.top nav { display: flex; align-items: center; gap: .4rem; padding: .85rem 0; }
        header.top a {
            color: #e5e7eb; text-decoration: none; font-weight: 600;
            padding: .4rem .85rem; border-radius: 4px; transition: background .15s, color .15s;
        }
        header.top a:hover { background: var(--amber); color: var(--steel); }
        header.top a.brand {
            color: var(--amber); font-size: 1.25rem; font-weight: 800; letter-spacing: .12em;
            margin-right: auto; padding-left: 0; text-transform: uppercase;
        }
        header.top a.brand:hover { background: none; color: #fff; }

        /* Контент */
        main {
            background: var(--card);
            margin: 1.5rem auto 1rem;
            padding: 1.5rem 1.75rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            border-top: 4px solid var(--amber);
            box-shadow: 0 2px 10px rgba(31, 41, 51, .08);
        }
        h1 { margin: 0; font-size: 1.55rem; letter-spacing: .01em; }
        .page-head {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap;
            margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
        }
        footer.foot { text-align: center; color: var(--muted); font-size: .85rem; padding: 0 0 2rem; }

        .flash {
            background: #e6f6ec; color: #15803d; border: 1px solid #a7e3bd;
            border-left: 5px solid #15803d;
            padding: .65rem .9rem; border-radius: 4px; margin-top: 1.25rem; font-weight: 600;
        }

        /* Кнопки */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            padding: .55rem 1.1rem; margin: 0;
            font: inherit; font-size: .95rem; font-weight: 700; line-height: 1.2;
            border: 1px solid transparent; border-radius: 5px;
            text-decoration: none; cursor: pointer;
            transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
        }
        .btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(245, 184, 0, .5); }
        .btn-primary { background: var(--amber); color: var(--steel); border-color: var(--amber-dark); }
        .btn-primary:hover { background: var(--amber-dark); color: var(--steel); }
        .btn-secondary { background: #fff; color: var(--steel-2); border-color: #b8bec6; }
        .btn-secondary:hover { background: #f1f3f5; color: var(--steel); }
        .btn-danger { background: #fff; color: var(--danger); border-color: #f0a3a3; }
        .btn-danger:hover { background: var(--danger); border-color: var(--danger); color: #fff; }
        .btn-sm { padding: .3rem .7rem; font-size: .85rem; }

        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; margin-top: 1.5rem; }
        .actions form { margin: 0; }
        .actions .spacer { margin-left: auto; }

        /* Формы */
        label { display: block; margin-top: 1rem; font-weight: 700; font-size: .9rem; }
        input[type="text"], select {
            display: block; width: 100%; margin-top: .3rem;
            padding: .55rem .7rem; font: inherit;
            border: 1px solid #b8bec6; border-radius: 5px; background: #fff;
        }
        input[type="text"]:focus, select:focus {
            outline: none; border-color: var(--amber-dark);
            box-shadow: 0 0 0 3px rgba(245, 184, 0, .35);
        }
        .form-card { max-width: 480px; }
        .error { display: block; color: var(--danger); font-size: .85rem; font-weight: 500; margin-top: .25rem; }

        /* Сводка */
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-bottom: 1.25rem; }
        .stat {
            background: #f8f9fa; border: 1px solid var(--border); border-left: 5px solid var(--steel-2);
            border-radius: 5px; padding: .6rem 1rem;
        }
        .stat span { display: block; color: var(--muted); font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; }
        .stat b { font-size: 1.7rem; line-height: 1.2; }
        .stat.warn { border-left-color: var(--amber); }
        .stat.bad { border-left-color: var(--danger); }

        /* Фильтр */
        .filter {
            display: flex; flex-wrap: wrap; gap: .75rem; align-items: flex-end;
            padding: 1rem; margin-bottom: 1.25rem;
            background: #f8f9fa; border: 1px solid var(--border); border-radius: 5px;
        }
        .filter label { margin: 0; flex: 1 1 200px; }
        .filter .btn { height: 2.45rem; }

        /* Таблица */
        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--steel); color: #fff; text-align: left;
            font-size: .75rem; text-transform: uppercase; letter-spacing: .06em;
            padding: .65rem .8rem; white-space: nowrap;
        }
        td { padding: .65rem .8rem; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover { background: #fff8e0; }
        th:nth-child(3), td:nth-child(3) { text-align: right; font-variant-numeric: tabular-nums; font-weight: 700; }
        th:last-child, td:last-child { text-align: right; }
        .empty { color: var(--muted); padding: 1.5rem 0; text-align: center; }

        .sku {
            display: inline-block; padding: .1rem .5rem; border-radius: 4px;
            background: #f1f3f5; border: 1px solid var(--border);
            font-family: ui-monospace, Consolas, monospace; font-size: .85rem; font-weight: 700; color: var(--steel-2);
        }
        .unit { color: var(--muted); font-weight: 600; }

        .status {
            display: inline-flex; align-items: center; gap: .4rem; white-space: nowrap;
            padding: .15rem .65rem; border-radius: 4px; font-size: .8rem; font-weight: 700;
        }
        .status::before { content: ""; width: .5rem; height: .5rem; border-radius: 50%; background: currentColor; }
        .status.ok  { background: #e6f6ec; color: #15803d; }
        .status.low { background: #fff4d1; color: #8a5a00; }
        .status.out { background: #fde8e8; color: #b91c1c; }

        .row-actions { display: flex; gap: .4rem; justify-content: flex-end; }
        .row-actions form { margin: 0; }

        /* Карточка позиции */
        .item-card { border: 1px solid var(--border); border-radius: 5px; overflow: hidden; }
        .item-top {
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            background: var(--steel); color: #fff; padding: .7rem 1.1rem;
        }
        .item-top .sku { background: var(--amber); border-color: var(--amber-dark); color: var(--steel); }
        .item-body { display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; padding: 1.25rem 1.1rem; }
        .qty { font-size: 2.8rem; font-weight: 800; line-height: 1; }
        .qty small { font-size: 1.1rem; font-weight: 600; color: var(--muted); }
        .qty-label { color: var(--muted); font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem; }
        dl {
            display: grid; grid-template-columns: 160px 1fr; gap: .5rem 1rem;
            margin: 0; padding: 1rem 1.1rem; border-top: 1px solid var(--border); background: #f8f9fa;
        }
        dt { color: var(--muted); }
        dd { margin: 0; font-weight: 600; }

        @media (max-width: 700px) {
            main { padding: 1rem; border-radius: 0; margin: 0 -1rem 1rem; border-left: 0; border-right: 0; }
            .stats { grid-template-columns: 1fr; }
            dl { grid-template-columns: 1fr; }
            .actions .spacer { margin-left: 0; }
        }
    </style>
</head>
<body>
<header class="top">
    <div class="wrap">
        <nav>
            <a class="brand" href="/items">📦 Склад</a>
            <a href="/items">Позиции</a>
            <a href="/items/create">+ Принять товар</a>
        </nav>
    </div>
</header>

<div class="wrap">
    <?php foreach ($flash['success'] ?? [] as $message): ?>
        <div class="flash"><?= e($message) ?></div>
    <?php endforeach; ?>

    <main>
<?= $content ?>
    </main>
    <footer class="foot">Складской учёт · Лабораторная работа 13</footer>
</div>
</body>
</html>