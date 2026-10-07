<?php
namespace App\Libraries;

class TokenGenerator
{
    /**
     * Generate token random 64 karakter hex (32 bytes).
     * Return: ['plain' => token asli (untuk dikirim ke user), 'hash' => hash untuk disimpan di DB]
     */
    public static function generate(): array
    {
        $plain = bin2hex(random_bytes(32));
        return [
            'plain' => $plain,
            'hash'  => hash('sha256', $plain),
        ];
    }

    public static function hash(string $plain): string
    {
        return hash('sha256', $plain);
    }

    public static function verify(string $plain, string $hash): bool
    {
        return hash_equals($hash, self::hash($plain));
    }
}