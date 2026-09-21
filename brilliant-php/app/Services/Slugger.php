<?php
declare(strict_types=1);

namespace App\Services;

class Slugger
{
    public static function slugify(string $value): string
    {
        $value = trim($value);
        if ($value === '') return '';
        // transliterate accented/latin chars
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        if ($value === false) {
            $value = trim($value);
        }
        $value = mb_strtolower($value, 'UTF-8');
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim((string)$value, '-');
        if ($value === '') {
            $value = substr(md5($value . microtime(true)), 0, 8);
        }
        return $value;
    }

    public static function unique(string $table, string $column, string $slug, int $ignoreId = 0): string
    {
        $candidate = $slug;
        $i = 1;
        while (true) {
            $sql = 'SELECT id FROM ' . $table . ' WHERE ' . $column . ' = ?';
            $params = [$candidate];
            if ($ignoreId > 0) {
                $sql .= ' AND id != ?';
                $params[] = $ignoreId;
            }
            $exists = Database::fetchValue($sql, $params);
            if ($exists === null) {
                return $candidate;
            }
            $i++;
            $candidate = $slug . '-' . $i;
        }
    }
}
