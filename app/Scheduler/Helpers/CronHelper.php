<?php

declare(strict_types=1);

namespace App\Scheduler\Helpers;

class CronHelper
{
    public static function everyMinute(): string
    {
        return '* * * * *';
    }

    public static function hourly(): string
    {
        return '0 * * * *';
    }

    public static function everyMidnight(): string
    {
        return '0 0 * * *';
    }

    public static function dailyAt(string $time): string
    {
        [$hour, $minute] = explode(':', $time);
        return "{$minute} {$hour} * * *";
    }

}
