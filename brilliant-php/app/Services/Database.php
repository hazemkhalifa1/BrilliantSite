<?php
declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOStatement;

class Database
{
    private static ?PDO $pdo = null;
    private static array $columnsCache = [];

    public static function instance(): Database
    {
        return new self();
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $driver = config('db.driver', 'sqlite');
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            if ($driver === 'mysql') {
                $m = config('db.mysql');
                $dsn = 'mysql:host=' . $m['host'] . ';port=' . $m['port'] . ';dbname=' . $m['name'] . ';charset=utf8mb4';
                self::$pdo = new PDO($dsn, $m['user'], $m['pass'], $opts);
            } else {
                $path = config('db.sqlite.path');
                $dir = dirname($path);
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                self::$pdo = new PDO('sqlite:' . $path, null, null, $opts);
                self::$pdo->exec('PRAGMA foreign_keys = ON');
            }
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchValue(string $sql, array $params = [])
    {
        $v = self::query($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function lastInsertId(): int
    {
        return (int)self::pdo()->lastInsertId();
    }

    public static function column(string $name): string
    {
        return "\x60" . $name . "\x60";
    }

    /** Return the columns of a table (cached). */
    public static function columns(string $table): array
    {
        if (isset(self::$columnsCache[$table])) return self::$columnsCache[$table];
        if (config('db.driver') === 'mysql') {
            $rows = self::fetchAll('SHOW COLUMNS FROM ' . $table);
            self::$columnsCache[$table] = array_map(fn($r) => $r['Field'], $rows);
        } else {
            $rows = self::fetchAll('PRAGMA table_info(' . $table . ')');
            self::$columnsCache[$table] = array_map(fn($r) => $r['name'], $rows);
        }
        return self::$columnsCache[$table];
    }

    public static function insert(string $table, array $data): int
    {
        $cols = self::columns($table);
        $t = now();
        if (in_array('created_at', $cols) && !isset($data['created_at'])) $data['created_at'] = $t;
        if (in_array('updated_at', $cols) && !isset($data['updated_at'])) $data['updated_at'] = $t;
        $names = array_keys($data);
        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', array_map(fn($c) => self::column($c), $names)) . ') VALUES (' . implode(',', array_fill(0, count($names), '?')) . ')';
        self::query($sql, array_values($data));
        return self::lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int
    {
        $cols = self::columns($table);
        if (in_array('updated_at', $cols) && !isset($data['updated_at'])) $data['updated_at'] = now();
        $sets = [];
        $params = [];
        foreach ($data as $k => $v) {
            $sets[] = self::column($k) . ' = ?';
            $params[] = $v;
        }
        $wheres = [];
        foreach ($where as $k => $v) {
            $wheres[] = self::column($k) . ' = ?';
            $params[] = $v;
        }
        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $sets) . ' WHERE ' . implode(' AND ', $wheres);
        return self::execute($sql, $params);
    }

    public static function delete(string $table, array $where): int
    {
        $wheres = [];
        $params = [];
        foreach ($where as $k => $v) {
            $wheres[] = self::column($k) . ' = ?';
            $params[] = $v;
        }
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . implode(' AND ', $wheres);
        return self::execute($sql, $params);
    }

    public static function transaction(callable $fn)
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $fn();
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}
