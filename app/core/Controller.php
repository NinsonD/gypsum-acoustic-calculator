<?php

declare(strict_types=1);

abstract class Controller
{
    public function __construct(protected array $config)
    {
    }

    protected function view(string $view, array $data = []): void
    {
        $data['config'] = $this->config;
        View::render($view, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    protected function requestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw ?: '{}', true);
            return is_array($decoded) ? $decoded : [];
        }

        return $_POST;
    }
}
