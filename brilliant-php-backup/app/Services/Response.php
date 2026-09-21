<?php
declare(strict_types=1);

namespace App\Services;

class Response
{
    public static function ok($data, string $message = 'Success', int $status = 200): void
    {
        json_response(['success' => true, 'message' => $message, 'data' => $data, 'statusCode' => $status], $status);
    }

    public static function created($data, string $message = 'Created'): void
    {
        json_response(['success' => true, 'message' => $message, 'data' => $data, 'statusCode' => 201], 201);
    }

    public static function fail(int $status, string $message, $data = null): void
    {
        json_response(['success' => false, 'message' => $message, 'data' => $data, 'statusCode' => $status], $status);
    }
}
