<?php
declare(strict_types=1);

namespace App\Services;

class Jwt
{
    public static function encode(array $payload, string $secret): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $headerEnc = self::b64(self::json($header));
        $payloadEnc = self::b64(self::json($payload));
        $signingInput = $headerEnc . '.' . $payloadEnc;
        $signature = hash_hmac('sha256', $signingInput, $secret, true);
        return $signingInput . '.' . self::b64($signature);
    }

    public static function decode(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$headerEnc, $payloadEnc, $signatureEnc] = $parts;
        $signingInput = $headerEnc . '.' . $payloadEnc;
        $expected = self::b64(hash_hmac('sha256', $signingInput, $secret, true));
        if (!hash_equals($expected, $signatureEnc)) return null;
        $payload = json_decode(self::unb64($payloadEnc), true);
        if (!is_array($payload)) return null;
        // expiration check
        if (isset($payload['exp']) && (int)$payload['exp'] < time()) return null;
        return $payload;
    }

    public static function makeToken(int $userId, string $email, array $roles, int $expiryMinutes = null): array
    {
        $expiry = ($expiryMinutes ?? config('jwt.expiry_minutes', 60)) * 60;
        $now = time();
        $claims = [
            'sub' => (string)$userId,
            'email' => $email,
            'roles' => $roles,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $expiry,
            'iss' => config('jwt.issuer', 'BrilliantEngineering'),
            'aud' => config('jwt.audience', 'BrilliantEngineering.Client'),
        ];
        return [
            'token' => self::encode($claims, config('jwt.secret')),
            'expires_at' => gmdate('Y-m-d\TH:i:s\Z', $now + $expiry),
            'expires_timestamp' => $now + $expiry,
        ];
    }

    public static function makeRefreshToken(int $userId): string
    {
        $expiry = config('jwt.refresh_expiry_days', 7) * 86400;
        $claims = [
            'sub' => (string)$userId,
            'typ' => 'refresh',
            'iat' => time(),
            'exp' => time() + $expiry,
            'iss' => config('jwt.issuer', 'BrilliantEngineering'),
            'aud' => config('jwt.audience', 'BrilliantEngineering.Client'),
        ];
        return self::encode($claims, config('jwt.secret'));
    }

    public static function validateRefreshToken(string $token): ?array
    {
        $payload = self::decode($token, config('jwt.secret'));
        if ($payload === null || ($payload['typ'] ?? '') !== 'refresh') return null;
        return $payload;
    }

    private static function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    private static function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function unb64(string $data): string
    {
        $data = strtr($data, '-_', '+/');
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode($data, false) ?: '';
    }
}
