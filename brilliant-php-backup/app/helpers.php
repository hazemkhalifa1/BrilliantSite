<?php
declare(strict_types=1);

use App\Services\Auth;
use App\Services\Database;
use App\Services\I18n;
use App\Services\Slugger;

if (!function_exists('config')) {
    function config(?string $key = null, $default = null) {
        static $cfg = null;
        if ($cfg === null) {
            $cfg = require __DIR__ . '/../config.php';
        }
        if ($key === null) return $cfg;
        $v = $cfg;
        foreach (explode('.', $key) as $p) {
            if (!is_array($v) || !array_key_exists($p, $v)) return $default;
            $v = $v[$p];
        }
        return $v;
    }
}

function base_path(string $p = ''): string {
    return config('app.base_path') . ($p ? '/' . ltrim($p, '/') : '');
}

function app_url(): string {
    return rtrim((string)config('app.site_url'), '/');
}

function url(string $path = ''): string {
    return app_url() . '/' . ltrim($path, '/');
}

/** Internal, locale-prefixed link path (matches the Next.js localized routing). */
function lnk(string $path = ''): string {
    return '/' . current_locale() . ($path ? '/' . ltrim($path, '/') : '');
}

function asset(string $path): string {
    return url(ltrim($path, '/'));
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path, int $status = 302): void {
    header('Location: ' . $path, true, $status);
    exit;
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function db(): Database {
    return Database::instance();
}

function now(): string {
    return gmdate('Y-m-d H:i:s');
}

function slugify(string $value): string {
    return Slugger::slugify($value);
}

function current_locale(): string {
    return I18n::locale();
}

function set_locale(string $locale): void {
    I18n::setLocale($locale);
}

function tarr(string $key, ?string $locale = null): array {
    return I18n::section($key, $locale);
}

function t(string $key, array $params = [], ?string $locale = null): string {
    return I18n::trans($key, $params, $locale);
}

function localized(?string $locale, ?string $en, ?string $ar): string {
    if ($locale === 'ar' && $ar !== null && trim($ar) !== '') return $ar;
    return (string)$en;
}

function __l($en, $arValue = null): string {
    return localized(current_locale(), $en, $arValue);
}

// localized field pair: returns the value for the current locale
function lpair(string $enVal, ?string $arVal): string {
    return localized(current_locale(), $enVal, $arVal);
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    if (($_POST['_csrf'] ?? '') !== csrf_token()) {
        http_response_code(419);
        echo 'Invalid CSRF token.';
        exit;
    }
}

function flash(string $type, string $message): void {
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array {
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

function old(string $key, string $default = ''): string {
    return (string)($_SESSION['_old'][$key] ?? $default);
}

function request_method(): string {
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function is_post(): bool {
    return request_method() === 'POST';
}

function input(string $key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function json_body(): array {
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function current_localized_path(): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $segments = explode('/', trim($path, '/'));
    if (isset($segments[0]) && $segments[0] === current_locale()) {
        array_shift($segments);
    }
    return '/' . implode('/', $segments);
}

function other_locale(): string {
    return current_locale() === 'en' ? 'ar' : 'en';
}

function locale_switch_href(string $locale): string {
    $path = current_localized_path();
    $base = '/' . $locale;
    return $path === '/' ? $base : $base . $path;
}

function request_base_url(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    $host = $_SERVER['HTTP_HOST'] ?? parse_url(config('app.site_url'), PHP_URL_HOST) ?? 'localhost';
    return $scheme . '://' . $host;
}

/**
 * Strips HTML tags from a string, preserving words for word counts.
 */
function strip_html(string $value): string {
    $text = preg_replace('/<[^>]*>/', ' ', $value);
    $text = preg_replace('/&nbsp;/i', ' ', (string)$text);
    $text = preg_replace('/\s+/', ' ', (string)$text);
    return trim((string)$text);
}

function reading_minutes(string $html): int {
    $words = preg_split('/\s+/', strip_html($html));
    $words = array_filter($words, static fn($w) => $w !== '');
    return max(1, (int)round((float)count($words) / 200));
}

/**
 * Removes leading empty paragraphs and a leading paragraph/heading that
 * duplicates the article title (mirrors lib/article.ts cleanArticleHtml).
 */
function clean_article_html(string $content, string $title): string {
    $html = trim($content);
    if ($html === '') return '';

    // Drop leading empty paragraphs left by the rich-text editor.
    $html = preg_replace('/^(?:<(p|div)[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/\1>[ \t\r\n]*)+/i', '', $html);

    // Drop a leading paragraph/heading that only repeats the article title.
    if (preg_match('/^<([a-z][a-z0-9]*)[^>]*>([\s\S]*?)<\/\1>/i', $html, $m)) {
        $normBlock = mb_strtolower(trim(preg_replace('/\s+/', ' ', strip_html($m[2]))));
        $normTitle = mb_strtolower(trim(preg_replace('/\s+/', ' ', $title)));
        if ($normBlock !== '' && $normBlock === $normTitle) {
            $html = trim(substr($html, strlen($m[0])));
        }
    }

    // Remove any leading empty paragraphs exposed by dropping the title.
    $html = preg_replace('/^(?:<(p|div)[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/\1>[ \t\r\n]*)+/i', '', $html);

    return trim((string)$html);
}

function snake_case(string $key): string {
    $s = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $key));
    if ($s === 'order') return 'sort_order';
    if ($s === 'value') return 'stat_value';
    return $s;
}

/**
 * Notify Google/Bing that the sitemap changed. Failures are swallowed so the
 * publish flow is never blocked. Uses the localized sitemap.xml URL.
 */
function ping_search_engines(): void
{
    $sitemap = url('sitemap.xml');
    $endpoints = [
        'https://www.google.com/ping?sitemap=' . rawurlencode($sitemap),
        'https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap),
    ];
    foreach ($endpoints as $endpoint) {
        try {
            if (function_exists('curl_init')) {
                $ch = curl_init($endpoint);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 3,
                    CURLOPT_CONNECTTIMEOUT => 1,
                    CURLOPT_FOLLOWLOCATION => true,
                ]);
                curl_exec($ch);
                curl_close($ch);
            } else {
                $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
                @file_get_contents($endpoint, false, $ctx);
            }
        } catch (\Throwable $e) {
            // Ignore ping failures.
        }
    }
}

function require_admin_login(): void {
    if (!Auth::check()) {
        redirect(lnk('/login'));
    }
}

function admin_url(string $path = ''): string {
    return '/' . current_locale() . '/admin' . ($path ? '/' . ltrim($path, '/') : '');
}
