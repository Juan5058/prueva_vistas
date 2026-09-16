<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Throwable;

class FormatsDate
{
    public static function datetime(mixed $value, string $format = 'Y-m-d H:i'): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        try {
            if ($value instanceof DateTimeInterface) {
                return Carbon::instance(Carbon::parse($value))->format($format);
            }

            if (is_numeric($value) || is_string($value)) {
                return Carbon::parse((string) $value)->format($format);
            }
        } catch (Throwable) {
            return '—';
        }

        return '—';
    }
}
