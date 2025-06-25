<?php

declare(strict_types=1);

namespace App\Scheduler\Interfaces;

interface TaskInterface
{
    public function getName(): string;
    public function execute(): void;
}
