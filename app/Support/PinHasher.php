<?php

namespace App\Support;

class PinHasher
{
    private const PREFIX = 'hmac:';

    public static function hash(string $pin): string
    {
        $normalizedPin = trim($pin);

        return self::PREFIX.hash_hmac('sha256', $normalizedPin, (string) config('app.key'));
    }

    public static function verify(string $plainPin, string $storedPin): bool
    {
        if (self::isHashed($storedPin)) {
            return hash_equals($storedPin, self::hash($plainPin));
        }

        return hash_equals($storedPin, trim($plainPin));
    }

    public static function needsUpgrade(string $storedPin): bool
    {
        return ! self::isHashed($storedPin);
    }

    public static function isHashed(string $storedPin): bool
    {
        return str_starts_with($storedPin, self::PREFIX);
    }
}
