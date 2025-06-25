<?php

declare(strict_types=1);

namespace App\Scheduler\Interfaces;

interface SchedulerInterface
{
    public function schedule(TaskInterface $task, string $cronExpression): void;
    public function runDueTasks(): void;
}
