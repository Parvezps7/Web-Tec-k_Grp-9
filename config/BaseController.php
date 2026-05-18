<?php

declare(strict_types=1);

abstract class BaseController
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(404);
            require ROOT_PATH . '/views/error/404.php';
            exit;
        }
        require ROOT_PATH . '/views/layout/header.php';
        require $viewFile;
        require ROOT_PATH . '/views/layout/footer.php';
    }

    protected function plain(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (is_file($viewFile)) {
            require $viewFile;
        }
    }
}
