<?php

namespace App\Support;

class IpRange
{
    public static function normalize(string $ip): string
    {
        if ($ip === '::1' || $ip === '::ffff:127.0.0.1') {
            return '127.0.0.1';
        }

        return $ip;
    }

    public static function allows(string $clientIp, ?string $allowedPattern): bool
    {
        if ($allowedPattern === null || trim($allowedPattern) === '' || trim($allowedPattern) === '*') {
            return true;
        }

        $normalizedIp = self::normalize($clientIp);

        foreach (explode(',', $allowedPattern) as $pattern) {
            $pattern = trim($pattern);
            if ($pattern === '*' || $pattern === $normalizedIp) {
                return true;
            }
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -2);
                if (str_starts_with($normalizedIp, $prefix)) {
                    return true;
                }
            }
            if (str_ends_with($pattern, '*')) {
                $prefix = substr($pattern, 0, -1);
                if (str_starts_with($normalizedIp, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
