<?php
declare(strict_types=1);

namespace App\Services;

class I18n
{
    private static string $locale = 'en';
    /** @var array<string,array<string,mixed>> */
    private static array $messages = [];

    public static function load(string $locale): array
    {
        if (isset(self::$messages[$locale])) return self::$messages[$locale];
        $file = base_path('/i18n/' . $locale . '.php');
        self::$messages[$locale] = is_file($file) ? require $file : [];
        return self::$messages[$locale];
    }

    public static function setLocale(string $locale): void
    {
        if (in_array($locale, config('locales', ['en','ar']), true)) {
            self::$locale = $locale;
        }
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    public static function section(string $key, ?string $locale = null): array
    {
        $locale = $locale ?: self::$locale;
        $messages = self::load($locale);
        $segments = explode('.', $key);
        $val = $messages;
        foreach ($segments as $s) {
            if (!is_array($val) || !array_key_exists($s, $val)) {
                return [];
            }
            $val = $val[$s];
        }
        return is_array($val) ? $val : [];
    }

    public static function trans(string $key, array $params = [], ?string $locale = null): string
    {
        $locale = $locale ?: self::$locale;
        $messages = self::load($locale);
        $segments = explode('.', $key);
        $val = $messages;
        foreach ($segments as $s) {
            if (!is_array($val) || !array_key_exists($s, $val)) {
                return $key;
            }
            $val = $val[$s];
        }
        if (!is_string($val)) {
            return $key;
        }
        // {placeholder} replacement (wrap keys in braces)
        if (count($params)) {
            $keys = array_map(fn($k) => '{' . $k . '}', array_keys($params));
            $vals = array_map(fn($v) => (string)$v, array_values($params));
            $val = str_replace($keys, $vals, $val);
        }
        return $val;
    }
}
