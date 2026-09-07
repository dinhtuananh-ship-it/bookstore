<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        render($view, $data);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): void
    {
        redirect($path);
    }

    protected function isPost(): bool
    {
        return isPost();
    }
}