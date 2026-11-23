<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\ItemRepository;
use App\Support\Flash;
use App\Validation\ItemValidator;
use App\View\Renderer;
use PDOException;

final readonly class ItemController
{
    public function __construct(
        private ItemRepository $items,
        private Renderer $views,
    ) {}

    public function index(array $query): Response
    {
        $unit   = is_string($query['unit'] ?? null) ? trim($query['unit']) : '';
        $search = is_string($query['q'] ?? null) ? trim($query['q']) : '';
        if (!in_array($unit, ItemValidator::UNITS, true)) {
            $unit = '';
        }

        return $this->page('items/index', [
            'title'  => 'Склад',
            'items'  => $this->items->all($unit, $search),
            'units'  => ItemValidator::UNITS,
            'unit'   => $unit,
            'search' => $search,
        ]);
    }

    public function create(): Response
    {
        return $this->form(['code' => '', 'name' => '', 'quantity' => '0', 'unit' => 'шт'], [], 'create');
    }

    public function store(array $input): Response
    {
        if (!csrf_valid($input['_csrf'] ?? null)) {
            return $this->forbidden();
        }

        [$errors, $data] = ItemValidator::validate($input);

        if ($errors === []) {
            try {
                $id = $this->items->create($data);
                Flash::success('Позиция добавлена.');

                return Response::redirect('/items/' . $id);
            } catch (PDOException $e) {
                $errors = $this->duplicateOrRethrow($e);
            }
        }

        return $this->form($data, $errors, 'create', 422);
    }

    public function show(string $id): Response
    {
        $item = $this->items->find((int) $id);
        if ($item === null) {
            return $this->notFound();
        }

        return $this->page('items/show', ['title' => $item['name'], 'item' => $item]);
    }

    public function edit(string $id): Response
    {
        $item = $this->items->find((int) $id);
        if ($item === null) {
            return $this->notFound();
        }

        return $this->form($item, [], 'edit');
    }

    public function update(string $id, array $input): Response
    {
        if (!csrf_valid($input['_csrf'] ?? null)) {
            return $this->forbidden();
        }
        $item = $this->items->find((int) $id);
        if ($item === null) {
            return $this->notFound();
        }

        [$errors, $data] = ItemValidator::validate($input);

        if ($errors === []) {
            try {
                $this->items->update((int) $id, $data);
                Flash::success('Позиция обновлена.');

                return Response::redirect('/items/' . (int) $id);
            } catch (PDOException $e) {
                $errors = $this->duplicateOrRethrow($e);
            }
        }

        return $this->form($data + ['id' => $item['id']], $errors, 'edit', 422);
    }

    public function destroy(string $id, array $input): Response
    {
        if (!csrf_valid($input['_csrf'] ?? null)) {
            return $this->forbidden();
        }
        if ($this->items->find((int) $id) === null) {
            return $this->notFound();
        }

        $this->items->delete((int) $id);
        Flash::success('Позиция удалена.');

        return Response::redirect('/items');
    }

    /** SQLSTATE 23000 — нарушение уникальности кода. */
    private function duplicateOrRethrow(PDOException $e): array
    {
        if ((string) $e->getCode() !== '23000') {
            throw $e;
        }

        return ['code' => 'Позиция с таким кодом уже существует.'];
    }

    private function form(array $item, array $errors, string $mode, int $status = 200): Response
    {
        $isEdit = $mode === 'edit';

        return $this->page('items/form', [
            'title'  => $isEdit ? 'Редактирование позиции' : 'Новая позиция',
            'item'   => $item,
            'errors' => $errors,
            'mode'   => $mode,
            'units'  => ItemValidator::UNITS,
            'action' => $isEdit ? '/items/' . (int) $item['id'] . '/edit' : '/items',
        ], $status);
    }

    private function page(string $template, array $data, int $status = 200): Response
    {
        return Response::html($this->views->render($template, $data), $status);
    }

    private function notFound(): Response
    {
        return $this->page('errors/404', ['title' => 'Не найдено'], 404);
    }

    private function forbidden(): Response
    {
        return $this->page('errors/403', ['title' => 'Запрещено'], 403);
    }
}
