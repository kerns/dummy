<?php

namespace Kerns\Dummy;

/** @internal Request-local state shared by the original global helpers. */
final class Runtime
{
    public static array $usedImages = [];
    public static array $loops = [];
    public static array $fileLists = [];
    public static array $textPools = [];
    public static ?array $query = null;
    public static ?string $requestPath = null;

    public static function reset(): void
    {
        self::$usedImages = [];
        self::$loops = [];
        self::$fileLists = [];
        self::$textPools = [];
        self::$query = null;
        self::$requestPath = null;
    }

    public static function resolve(string $root, string $path = ''): ?string
    {
        $root = realpath($root);
        if ($root === false || str_contains($path, "\0")) {
            return null;
        }
        $resolved = realpath($root . '/' . ltrim(str_replace('\\', '/', $path), '/'));
        if ($resolved === false || ($resolved !== $root && !str_starts_with($resolved, $root . DIRECTORY_SEPARATOR))) {
            return null;
        }
        return $resolved;
    }

    public static function error(string $message): void
    {
        echo '<em>Dummy: ' . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</em>';
    }
}
