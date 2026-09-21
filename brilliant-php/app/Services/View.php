<?php
declare(strict_types=1);

namespace App\Services;

class View
{
    private static string $layout = 'layouts/public';

    /** Render a view within the active layout. */
    public static function render(string $template, array $data = [], ?string $layout = null): void
    {
        extract($data, EXTR_SKIP);
        $templateFile = base_path('/views/' . $template . '.php');
        ob_start();
        require $templateFile;
        $content = ob_get_clean();
        $layout = $layout ?: self::$layout;
        $layoutFile = base_path('/views/' . $layout . '.php');
        require $layoutFile;
    }

    public static function withoutLayout(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require base_path('/views/' . $template . '.php');
    }

    public static function setLayout(string $layout): void
    {
        self::$layout = $layout;
    }
}
