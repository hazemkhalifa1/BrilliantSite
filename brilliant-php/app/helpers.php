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
 * Renders inline Markdown (bold, italic, code, links, images) into safe HTML.
 * Input is escaped first, so markdown tokens are the only source of tags.
 */
function inline_markdown(string $text, bool $block = false): string
{
    // Escape raw HTML so stored content can never inject markup.
    $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $text = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>])/', '$1', (string)$text);

    // Images must run before links.
    $text = preg_replace('/!\[([^\]]*)\]\(([^)\s]+)(?:\s+"[^"]*")?\)/', '<img src="$2" alt="$1">', (string)$text);
    $text = preg_replace('/\[([^\]]+)\]\(([^)\s]+)(?:\s+"[^"]*")?\)/', '<a href="$2">$1</a>', (string)$text);

    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', (string)$text);

    $text = preg_replace('/\*{3}([^*\s][^*]*)\*{3}/', '<strong><em>$1</em></strong>', (string)$text);
    $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', (string)$text);
    $text = preg_replace('/__([^_]+)__/', '<strong>$1</strong>', (string)$text);

    $text = preg_replace('/(^|[^\w*])\*([^*\s][^*]*)\*(?=[^\w*]|$)/', '$1<em>$2</em>', (string)$text);
    $text = preg_replace('/(^|[^\w_])\_([^_\s][^_]*)\_(?=[^\w_]|$)/', '$1<em>$2</em>', (string)$text);

    $text = preg_replace('/~{2}([^~]+)~{2}/', '<del>$1</del>', (string)$text);

    // Keep explicit line breaks inside a paragraph/list item.
    if ($block) {
        $text = str_replace("\n", "<br>\n", (string)$text);
    }

    return (string)$text;
}

/**
 * Minimal, dependency-free Markdown → HTML converter. Handles the formatting
 * used by blog posts: ATX headings, unordered/numbered lists, blockquotes,
 * code fences, horizontal rules and inline emphasis/link/code.
 */
function markdown_to_html(string $markdown): string
{
    $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
    $lines = explode("\n", $markdown);
    $html = '';
    $i = 0;
    $n = count($lines);

    while ($i < $n) {
        $line = $lines[$i];

        if (trim($line) === '') { $i++; continue; }

        // Fenced code block.
        if (preg_match('/^\s*(```|~~~)/', $line, $fm)) {
            $fence = $fm[1];
            $i++;
            $code = [];
            while ($i < $n && !preg_match('/^\s*' . preg_quote($fence, '/') . '/', $lines[$i])) {
                $code[] = $lines[$i];
                $i++;
            }
            $i++; // skip closing fence (or EOF)
            $html .= '<pre><code>' . htmlspecialchars(implode("\n", $code), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</code></pre>\n";
            continue;
        }

        // ATX heading (#, ##, ### …).
        if (preg_match('/\A(#{1,6})\s+(.*?)\s*#*\s*\z/', trim($line), $hm)) {
            $level = strlen($hm[1]);
            $html .= '<h' . $level . '>' . inline_markdown($hm[2]) . '</h' . $level . ">\n";
            $i++;
            continue;
        }

        // Horizontal rule (---, ***, ___).
        if (preg_match('/\A\s{0,3}([-*_])(\s*\1){2,}\s*\z/', $line)) {
            $html .= "<hr>\n";
            $i++;
            continue;
        }

        // Unordered or ordered list (flat, top-level items).
        if (preg_match('/\A\s{0,3}(?:[-*+]|\d+[.)])\s+/', $line)) {
            $ordered = !preg_match('/\A\s{0,3}[-*+]\s+/', $line);
            $tag = $ordered ? 'ol' : 'ul';
            $html .= '<' . $tag . ">\n";
            while ($i < $n && trim($lines[$i]) !== '' && preg_match('/\A\s{0,3}(?:[-*+]|\d+[.)])\s+(.*)\z/', $lines[$i], $lim)) {
                $html .= '<li>' . inline_markdown($lim[1], true) . "</li>\n";
                $i++;
            }
            $html .= '</' . $tag . ">\n";
            continue;
        }

        // Blockquote.
        if (preg_match('/\A\s{0,3}>\s?/', $line)) {
            $quote = [];
            while ($i < $n && preg_match('/\A\s{0,3}>\s?(.*)\z/', $lines[$i], $qm)) {
                $quote[] = $qm[1];
                $i++;
            }
            $html .= '<blockquote>' . inline_markdown(implode("\n", $quote), true) . "</blockquote>\n";
            continue;
        }

        // GFM table: header | separator | rows
        if (
            strpos($line, '|') !== false &&
            $i + 1 < $n
        ) {
            $headerLine = str_replace('\|', '|', $line);
            $separatorLine = str_replace('\|', '|', $lines[$i + 1]);

            // Remove optional leading/trailing pipe before checking separator
            $separatorCheck = trim($separatorLine);
            $separatorCheck = trim($separatorCheck, '|');

            $separatorCells = array_map('trim', explode('|', $separatorCheck));

            $isSeparator = count($separatorCells) >= 1;

            foreach ($separatorCells as $separatorCell) {
                if (!preg_match('/^:?-+:?$/', $separatorCell)) {
                    $isSeparator = false;
                    break;
                }
            }

            if ($isSeparator) {

                // Parse header
                $headers = array_map(
                    'trim',
                    explode('|', trim($headerLine, " \t|"))
                );

                // Remove empty accidental columns
                $headers = array_values(
                    array_filter(
                        $headers,
                        static fn($value) => $value !== ''
                    )
                );

                $i += 2;

                $isRtl = function_exists('current_locale')
                    && current_locale() === 'ar';

                $rtlDir = $isRtl ? ' dir="rtl"' : '';
                $thAlign = $isRtl ? 'right' : 'left';

                $html .=
                    '<table' . $rtlDir .
                    ' style="width:100%;border-collapse:collapse;margin:1.5rem 0;' .
                    ($isRtl ? 'direction:rtl;' : '') .
                    '">' .
                    "\n";

                $html .= "<thead>\n<tr>";

                foreach ($headers as $header) {

                    $html .=
                        '<th style="' .
                        'background:#0f1e6c;' .
                        'color:#fff !important;' .
                        'padding:10px 14px;' .
                        'text-align:' . $thAlign . ';' .
                        'border:1px solid #0f1e6c;' .
                        '">' .
                        inline_markdown($header) .
                        '</th>';
                }

                $html .= "</tr>\n</thead>\n<tbody>\n";

                // Table body
                while (
                    $i < $n &&
                    trim($lines[$i]) !== '' &&
                    strpos($lines[$i], '|') !== false
                ) {
                    $rowLine = str_replace('\|', '|', $lines[$i]);

                    $cells = array_map(
                        'trim',
                        explode('|', trim($rowLine, " \t|"))
                    );

                    $html .= '<tr>';

                    foreach ($cells as $cell) {

                        $html .=
                            '<td style="' .
                            'padding:10px 14px;' .
                            'border-bottom:1px solid #e2e8f0;' .
                            'text-align:' . $thAlign . ';' .
                            'border-left:1px solid #e2e8f0;' .
                            'border-right:1px solid #e2e8f0;' .
                            '">' .
                            inline_markdown($cell) .
                            '</td>';
                    }

                    // Fill missing cells
                    for (
                        $k = count($cells);
                        $k < count($headers);
                        $k++
                    ) {
                        $html .=
                            '<td style="' .
                            'padding:10px 14px;' .
                            'border-bottom:1px solid #e2e8f0;' .
                            'border-left:1px solid #e2e8f0;' .
                            'border-right:1px solid #e2e8f0;' .
                            '"></td>';
                    }

                    $html .= "</tr>\n";

                    $i++;
                }

                $html .= "</tbody>\n</table>\n";

                continue;
            }
        }

        // Paragraph: gather until a blank line or a new block opener.
        $para = [];
        while ($i < $n && trim($lines[$i]) !== ''
            && !preg_match('/\A\s{0,3}(?:#{1,6}\s|[-*+]|[*_]{3,}|```)/', $lines[$i])) {
            $para[] = $lines[$i];
            $i++;
        }
        $html .= '<p>' . inline_markdown(implode("\n", $para), true) . "</p>\n";
    }

    return $html;
}

/**
 * True when the stored content is already rich HTML (Quill editor output),
 * which must pass through unchanged instead of being parsed as Markdown.
 */
function is_html_content(string $value): bool
{
    return (bool)preg_match('/<(?:p|h[1-6]|div|ul|ol|li|blockquote|pre|table|figure|img|hr)[\s>\/]/i', mb_substr(trim($value), 0, 2000));
}

function looks_like_markdown(string $value): bool
{
    if (trim($value) === '') {
        return false;
    }

    if (is_html_content($value)) {
        return false;
    }

    // Normal Markdown
    if (preg_match(
        '/(?:^|\n)\s{0,3}(?:#{1,6}\s+|[-*+]\s+|\d+[.)]\s+|>\s?)/m',
        $value
    )) {
        return true;
    }

    // Bold / italic / inline formatting
    if (preg_match(
        '/(?:\*\*|__|\*|_|~~|`)/',
        $value
    )) {
        return true;
    }

    // Markdown horizontal rule
    if (preg_match(
        '/(?:^|\n)\s{0,3}([-*_])(?:\s*\1){2,}\s*$/m',
        $value
    )) {
        return true;
    }

    // GFM table - supports both | and \|
    if (preg_match(
        '/(?:^|\n)\s*\\?\|.*\\?\|\s*\n\s*\\?\|?\s*:?-+:?(?:\s*\\?\|\s*:?-+:?)+\s*\\?\|?\s*(?:\n|$)/m',
        $value
    )) {
        return true;
    }

    return false;
}

function markdown_safe_html(string $value): string
{
    return looks_like_markdown($value) ? markdown_to_html($value) : $value;
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
    if (looks_like_markdown($html)) {
        $html = markdown_to_html($html);
    }
    $words = preg_split('/\s+/', strip_html($html));
    $words = array_filter($words, static fn($w) => $w !== '');
    return max(1, (int)round((float)count($words) / 200));
}

/**
 * Removes leading empty paragraphs and a leading paragraph/heading that
 * duplicates the article title (mirrors lib/article.ts cleanArticleHtml).
 * Content stored as Markdown is converted to HTML first so the article
 * renders with full formatting (headings, bold, lists, rules).
 */
/**
 * Convert any GFM markdown tables found inside HTML/markdown mixed content.
 * Used as fallback when is_html_content blocks full markdown conversion.
 */
function convert_gfm_tables(string $html): string {
    // 1. Strip <p> tags wrapping table rows that start with | (Quill: <p>| Facility Type |</p>)
    // Pattern as requested: <p>\s*(\|.*)\s*</p> → $1\n — handles both EN and AR
    $html = preg_replace('/<p[^>]*>\s*(\|.*)\s*<\/p>/is', "$1\n", $html);
    // Normalize remaining HTML line breaks
    $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
    $html = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n", $html);
    $html = preg_replace('/<p[^>]*>/i', '', $html);
    $html = preg_replace('/<\/p>/i', "\n", $html);
    $html = preg_replace('/<div[^>]*>/i', '', $html);
    $html = preg_replace('/<\/div>/i', "\n", $html);
    return preg_replace_callback(
        '/^([^\n]*\|[^\n]*)\n(\s*\|?\s*:?-+:?\s*(?:\|\s*:?-+:?\s*)+\|?\s*)\n((?:[^\n]*\|[^\n]*\n?)*)/m',
        function ($m) {
            $headerLine = trim(strip_tags($m[1]));
            $rowsBlock = trim(strip_tags($m[3]));
            $headers = array_map('trim', explode('|', trim(trim($headerLine), '|')));
            $isRtl = (function_exists('current_locale') && current_locale() === 'ar');
            $rtlDir = $isRtl ? ' dir="rtl"' : '';
            $thAlign = $isRtl ? 'right' : 'left';
            $out = "<table$rtlDir style=\"width:100%;border-collapse:collapse;margin:1.5rem 0;" . ($isRtl ? "direction:rtl;" : "") . "\">\n<thead>\n<tr>";
            foreach ($headers as $h) {
                $out .= '<th style="background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:' . $thAlign . ';border:1px solid #0f1e6c;">' . inline_markdown($h) . '</th>';
            }
            $out .= "</tr>\n</thead>\n<tbody>\n";
            if ($rowsBlock !== '') {
                $rows = preg_split('/\n/', $rowsBlock);
                foreach ($rows as $row) {
                    $row = trim($row);
                    if ($row === '' || strpos($row, '|') === false) continue;
                    $cells = array_map('trim', explode('|', trim(trim($row), '|')));
                    $out .= '<tr>';
                    foreach ($cells as $cell) {
                        $out .= '<td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;text-align:' . $thAlign . ';border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">' . inline_markdown($cell) . '</td>';
                    }
                    for ($k = count($cells); $k < count($headers); $k++) {
                        $out .= '<td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;"></td>';
                    }
                    $out .= "</tr>\n";
                }
            }
            $out .= "</tbody>\n</table>\n";
            return $out;
        },
        $html
    );
}

/**
 * If a <table> has no <thead> (Quill HTML: all <td>), promote its first <tr> to <thead><th>
 * with navy background. Handles EN LTR and AR RTL.
 */
function promote_html_table_headers(string $html): string {
    return preg_replace_callback(
        '/<table([^>]*)>(.*?)<\/table>/is',
        function ($m) {
            $attrs = $m[1];
            $inner = $m[2];
            if (stripos($inner, '<thead') !== false) return $m[0];
            if (!preg_match_all('/<tr[^>]*>.*?<\/tr>/is', $inner, $trs) || count($trs[0]) === 0) return $m[0];
            $isRtl = (function_exists('current_locale') && current_locale() === 'ar');
            $thAlign = $isRtl ? 'right' : 'left';
            // First row -> thead th
            $firstTr = $trs[0][0];
            $firstTh = preg_replace_callback(
                '/<td([^>]*)>(.*?)<\/td>/is',
                function ($td) use ($thAlign) {
                    $innerTd = $td[2];
                    return '<th style="background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:' . $thAlign . ';border:1px solid #0f1e6c;">' . $innerTd . '</th>';
                },
                $firstTr
            );
            // Ensure table has dir/rtl if AR and not already present
            if ($isRtl && stripos($attrs, 'dir=') === false) {
                $attrs = trim($attrs) . ' dir="rtl" style="direction:rtl;width:100%;border-collapse:collapse;margin:1.5rem 0;"';
            } elseif (stripos($attrs, 'style=') === false) {
                // Add base table style if missing
                $add = ' style="width:100%;border-collapse:collapse;margin:1.5rem 0;' . ($isRtl ? 'direction:rtl;' : '') . '"';
                $attrs = trim($attrs) . $add;
            }
            // Rebuild: thead from first row + tbody from remaining rows
            $firstTrInner = preg_replace('/^<tr[^>]*>(.*)<\/tr>$/is', '$1', $firstTh);
            // Collect remaining rows (skip first)
            $remaining = '';
            for ($i = 1; $i < count($trs[0]); $i++) {
                $row = $trs[0][$i];
                // Ensure td have bottom border style if missing
                $row = preg_replace_callback('/<td([^>]*)>/i', function ($td) use ($thAlign) {
                    $a = $td[1];
                    if (stripos($a, 'style=') !== false) return $td[0];
                    return '<td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;text-align:' . $thAlign . ';border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;"' . $a . '>';
                }, $row);
                $remaining .= $row . "\n";
            }
            $thead = "<thead>\n<tr>" . $firstTrInner . "</tr>\n</thead>";
            // Strip original tbody wrappers to avoid nested tbody
            $remaining = preg_replace('/<\/?tbody[^>]*>/i', '', $remaining);
            $tbody = "<tbody>\n" . trim($remaining) . "\n</tbody>";
            return "<table$attrs>\n$thead\n$tbody\n</table>";
        },
        $html
    );
}

function clean_article_html(string $content, string $title): string {
    $html = trim($content);
    if ($html === '') return '';

    // Convert Markdown-authored posts to HTML. HTML content (Quill output)
    // is left untouched — unless it contains a markdown table.
    if (looks_like_markdown($html)) {
        $html = markdown_to_html($html);
    } elseif (strpos($html, '|') !== false && preg_match('/[^\n]*\|[^\n]*\n\s*\|?\s*:?-+:?/m', strip_tags($html))) {
        // Fallback: HTML content that still contains a markdown table (e.g. Quill HTML + pasted markdown: <p>| a | b |</p>)
        $html = convert_gfm_tables($html);
    }

    // Fix Quill HTML tables that have <td> for header: promote first <tr> to <thead><th> with navy bg
    if (stripos($html, '<table') !== false) {
        $html = promote_html_table_headers($html);
    }

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
