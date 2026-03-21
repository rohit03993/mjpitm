<?php

use Carbon\Carbon;
use Carbon\CarbonInterface;

if (! function_exists('display_date')) {
    /**
     * Format a calendar date for UI/PDF (default DD-MM-YYYY via config).
     * Does not alter stored values — display only.
     * Converts to app timezone so ISO-8601 / UTC strings match local wall date.
     */
    function display_date(mixed $value, string $default = 'N/A'): string
    {
        if ($value === null || $value === '') {
            return $default;
        }
        $format = config('app.date_format', 'd-m-Y');
        $tz = config('app.timezone', 'UTC');
        if ($value instanceof CarbonInterface) {
            return $value->copy()->timezone($tz)->format($format);
        }
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->timezone($tz)->format($format);
        }

        return Carbon::parse($value)->timezone($tz)->format($format);
    }
}

if (! function_exists('display_datetime')) {
    /**
     * Format a date-time for UI/PDF (default DD-MM-YYYY + 12h time via config).
     * Always shown in app timezone (fixes raw UTC / ISO "Z" strings looking "wrong").
     */
    function display_datetime(mixed $value, string $default = 'N/A'): string
    {
        if ($value === null || $value === '') {
            return $default;
        }
        $format = config('app.datetime_format', 'd-m-Y h:i A');
        $tz = config('app.timezone', 'UTC');
        if ($value instanceof CarbonInterface) {
            return $value->copy()->timezone($tz)->format($format);
        }
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->timezone($tz)->format($format);
        }

        return Carbon::parse($value)->timezone($tz)->format($format);
    }
}

if (! function_exists('display_audit_value')) {
    /**
     * Human-readable value for student audit rows (dates, datetimes, ISO strings).
     */
    function display_audit_value(?string $fieldName, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($value instanceof CarbonInterface || $value instanceof \DateTimeInterface) {
            $fieldName = (string) $fieldName;
            if (in_array($fieldName, ['date_of_birth', 'deposit_date'], true)) {
                return display_date($value);
            }

            return display_datetime($value);
        }
        if (! is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $fieldName = (string) $fieldName;

        if (in_array($fieldName, ['date_of_birth', 'deposit_date'], true)) {
            return display_date((string) $value);
        }

        if (str_ends_with($fieldName, '_at') || $fieldName === 'deleted_at') {
            return display_datetime((string) $value);
        }

        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}/', $value)) {
            return display_datetime($value);
        }

        return (string) $value;
    }
}

if (! function_exists('resolve_per_page')) {
    /**
     * Safe per-page resolver for listing screens.
     */
    function resolve_per_page(mixed $value, int $default = 10): int
    {
        $allowed = [5, 10, 15, 25, 50];
        $parsed = is_numeric($value) ? (int) $value : $default;

        return in_array($parsed, $allowed, true) ? $parsed : $default;
    }
}
