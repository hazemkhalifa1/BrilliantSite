<?php
declare(strict_types=1);
// Built-in dev-server front controller. Serves existing static files directly.
$url = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $url;
if ($url !== '/' && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
