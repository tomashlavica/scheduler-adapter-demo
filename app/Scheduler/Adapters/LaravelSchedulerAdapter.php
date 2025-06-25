<?php

declare(strict_types=1);

namespace App\Scheduler\Adapters;

use App\Scheduler\Interfaces\SchedulerInterface;
use App\Scheduler\Interfaces\TaskInterface;
use App\Scheduler\Interfaces\MutexInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class LaravelSchedulerAdapter implements SchedulerInterface
{
    private const CACHE_TIME_TO_LIVE_IN_SECONDS = 300;

    public function __construct(
        public readonly Schedule $schedule,
        public readonly MutexInterface $mutex,
    ) {}

    public function schedule(TaskInterface $task, string $cronExpression): void
    {
        try {
            $this->schedule->call(function () use ($task) {
                $key = 'scheduler:' . $task->getName();
                if (!$this->mutex->acquire($key, self::CACHE_TIME_TO_LIVE_IN_SECONDS)) {
                    Log::warning("Mutex acquire failed for task {$task->getName()}");
                    return;
                }
                try {
                    $task->execute();
                } finally {
                    $this->mutex->release($key);
                }
            })->cron($cronExpression);
        } catch (\Throwable $e) {
            Log::error("Scheduling task {$task->getName()} failed: " . $e->getMessage());
        }
    }

    public function runDueTasks(): void
    {
        Artisan::call('schedule:run');
    }
}
