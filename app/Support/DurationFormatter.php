<?php

namespace App\Support;

class DurationFormatter
{
    public static function humanize(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0m';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours === 0) {
            return sprintf('%dm', $minutes);
        }

        return sprintf('%dh %dm', $hours, $minutes);
    }
}
