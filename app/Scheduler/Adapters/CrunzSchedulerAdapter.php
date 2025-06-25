<?php

declare(strict_types=1);

namespace App\Scheduler\Adapters;

use App\Scheduler\Interfaces\SchedulerInterface;
use App\Scheduler\Interfaces\TaskInterface;
use App\Scheduler\Interfaces\MutexInterface;
use Cron\CronExpression;

class CrunzSchedulerAdapter implements SchedulerInterface
{
    private const CACHE_TIME_TO_LIVE_IN_SECONDS = 300;

    /** @var array<int, array{task: TaskInterface, cron: string}> */
    private array $tasks = [];

    public function __construct(
        private readonly MutexInterface $mutex
    ) {}

    public function schedule(TaskInterface $task, string $cronExpression): void
    {
        $this->tasks[] = [
            'task' => $task,
            'cron' => $cronExpression,
        ];
    }

    public function runDueTasks(): void
    {
        $now = new \DateTimeImmutable();

        foreach ($this->tasks as $entry) {
            $task = $entry['task'];
            $cron = $entry['cron'];

            $cronExpr = new CronExpression($cron);

            if (!$cronExpr->isDue($now)) {
                continue;
            }

            $key = 'scheduler:' . $task->getName();

            if (!$this->mutex->acquire($key, self::CACHE_TIME_TO_LIVE_IN_SECONDS)) {
                continue;
            }

            try {
                $task->execute();
            } finally {
                $this->mutex->release($key);
            }
        }
    }
}
