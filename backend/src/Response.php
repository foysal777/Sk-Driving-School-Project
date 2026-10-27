<?php
declare(strict_types=1);

namespace DriveMaster;

/**
 * Standard JSON API Response Helper for Pure PHP 8+
 */
final class Response
{
    public static function applyCors(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
        header("Content-Type: application/json; charset=UTF-8");

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit();
        }
    }

    public static function json(array $payload, int $statusCode = 200): void
    {
        self::applyCors();
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    public static function success(mixed $data = null, string $message = 'Success', int $statusCode = 200, array $meta = []): void
    {
        $response = [
            'success'   => true,
            'status'    => $statusCode,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => date('c'),
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        self::json($response, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, mixed $errors = null): void
    {
        $response = [
            'success'   => false,
            'status'    => $statusCode,
            'message'   => $message,
            'timestamp' => date('c'),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        self::json($response, $statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized access'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403);
    }

    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::error($message, 422, $errors);
    }
}
