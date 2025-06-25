<?php

declare(strict_types=1);

namespace App\Scheduler\Interfaces;

interface MutexInterface
{
    public function acquire(string $key, int $cacheTimeToLiveInSeconds): bool;
    public function release(string $key): void;
}
