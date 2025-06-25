<?php

declare(strict_types=1);

namespace App\Scheduler\Mutexes;

use App\Scheduler\Interfaces\MutexInterface;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class DatabaseMutex implements MutexInterface
{
    private const DB_TABLE = 'mutex_locks';

    public function acquire(string $key, int $cacheTimeToLiveInSeconds): bool
    {
        try {
            $now = new DateTime('now', new DateTimeZone('UTC'));
            $expiresAt = (clone $now)->modify("+{$cacheTimeToLiveInSeconds} seconds")->format('Y-m-d H:i:s');

            return DB::insert(
                "INSERT INTO " . self::DB_TABLE . " (`key`, `expires_at`) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE
                    `expires_at` = IF(`expires_at` < NOW(), VALUES(`expires_at`), `expires_at`)",
                [$key, $expiresAt]
            );
        } catch (\Throwable $e) {
            Log::error("DatabaseMutex acquire error: " . $e->getMessage());
            return false;
        }
    }

    public function release(string $key): void
    {
        DB::table(self::DB_TABLE)->where('key', $key)->delete();
    }
}
