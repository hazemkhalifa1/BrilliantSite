<?php
declare(strict_types=1);

namespace App\Services;

class Auth
{
    // ---- credentials / hashing ----
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** Validate credentials; returns user row or null. */
    public static function attempt(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user === null) return null;
        if (!self::verifyPassword($password, $user['password_hash'])) return null;
        return $user;
    }

    // ---- session (server-rendered admin) ----
    public static function check(): bool
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && in_array('Admin', $user['roles'] ?? [], true);
    }

    public static function loginAs(array $user): void
    {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'fullName' => $user['full_name'] ?? null,
            'roles' => [$user['role'] ?? 'Admin'],
        ];
        // Generate a JWT so the admin JS (uploads, etc.) can call the API.
        $token = Jwt::makeToken((int)$user['id'], $user['email'], [$user['role'] ?? 'Admin']);
        $_SESSION['jwt'] = $token['token'];
        $_SESSION['jwt_expires'] = $token['expires_timestamp'];
        // JS-accessible cookie (mirrors original localStorage token behaviour).
        setcookie('_be_token', $token['token'], [
            'expires' => $token['expires_timestamp'],
            'path' => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        unset($_SESSION['user'], $_SESSION['jwt'], $_SESSION['jwt_expires']);
        setcookie('_be_token', '', ['expires' => time() - 3600, 'path' => '/']);
    }

    // ---- token / API auth ----
    public static function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($header !== '' && preg_match('/Bearer\s+(.+)/i', $header, $m)) {
            return trim($m[1]);
        }
        return $_COOKIE['_be_token'] ?? null;
    }

    public static function apiUser(): ?array
    {
        $token = self::bearerToken();
        if ($token === null) return null;
        $payload = Jwt::decode($token, config('jwt.secret'));
        if ($payload === null) return null;
        $id = (int)($payload['sub'] ?? 0);
        $user = self::findById($id);
        if ($user === null) return null;
        $user['roles'] = $payload['roles'] ?? [$user['role'] ?? 'Admin'];
        return $user;
    }

    public static function apiRequireAdmin(): ?array
    {
        $user = self::apiUser();
        if ($user === null || !in_array('Admin', $user['roles'] ?? [], true)) {
            Response::fail(401, 'Unauthorized.');
            return null;
        }
        return $user;
    }
}
