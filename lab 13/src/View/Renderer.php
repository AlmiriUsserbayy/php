<?php

declare(strict_types=1);

namespace App\View;

final readonly class Renderer
{
    public function __construct(private string $basePath) {}

    /** $template задаётся контроллером (фиксированная строка), не пользователем. */
    public function render(string $template, array $data = []): string
    {
        $file = $this->basePath . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Template not found: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        $content = (string) ob_get_clean();

        ob_start();
        require $this->basePath . '/layout.php';

        return (string) ob_get_clean();
    }
}
