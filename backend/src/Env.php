<?php
declare(strict_types=1);

namespace DriveMaster;

/**
 * Lightweight Environment Variable (.env) Loader
 */
class Env
{
    private static bool $loaded = false;

    public static function load(?string $filePath = null): void
    {
        if (self::$loaded && $filePath === null) {
            return;
        }

        if ($filePath === null) {
            $candidates = [
                dirname(__DIR__, 2) . '/.env',
                dirname(__DIR__) . '/.env',
                __DIR__ . '/.env',
                getcwd() . '/.env'
            ];

            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_readable($cand)) {
                    $filePath = $cand;
                    break;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Strip outer quotes
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                }
                if (!array_key_exists($key, $_SERVER)) {
                    $_SERVER[$key] = $value;
                }
                putenv("{$key}={$value}");
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}

// Auto load immediately on inclusion
Env::load();
