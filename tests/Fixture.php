<?php

namespace Tests;

class Fixture
{
    public static function path(string $relativePath): string
    {
        $path = __DIR__ . '/data/tests/files/' . ltrim($relativePath, '/');

        if (!file_exists($path)) {
            throw new \RuntimeException("Fixture file not found: {$path}");
        }

        return $path;
    }
}