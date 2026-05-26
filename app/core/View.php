<?php

declare(strict_types=1);

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $viewPath = APP_PATH . '/views/' . $view . '.php';
        $layoutPath = APP_PATH . '/views/' . $layout . '.php';

        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'View not found.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (is_file($layoutPath)) {
            require $layoutPath;
            return;
        }

        echo $content;
    }

    public static function partial(string $name, array $data = []): void
    {
        $path = APP_PATH . '/views/partials/' . $name . '.php';

        if (!is_file($path)) {
            return;
        }

        extract($data, EXTR_SKIP);
        require $path;
    }
}
