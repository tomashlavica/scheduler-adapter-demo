<?php

declare(strict_types=1);

namespace App\Scheduler\Tasks;

use App\Scheduler\Interfaces\TaskInterface;

class SendBirthdayCardsTask implements TaskInterface
{
    private const TASK_NAME = 'send-birthday-cards';

    public function getName(): string
    {
        return self::TASK_NAME;
    }

    public function execute(): void
    {
        foreach ($this->getUsersWithBirthdayToday() as $user) {
            $this->sendBirthdayCard($user);
        }
    }

    private function getUsersWithBirthdayToday(): array
    {
        // @todo: get users from DB
        // @todo: return it as array of User objects, not array

        // example response:
        return [
            ['id' => 1, 'email' => 'john.doe@google.com', 'name' => 'John'],
            ['id' => 2, 'email' => 'kate.doe@example.com', 'name' => 'Kate'],
        ];
    }

    private function sendBirthdayCard(array $user): void
    {
        // @todo: send message via real sender

        // example log message
        echo "Sending birthday card to {$user['email']} ({$user['name']})\n<br>" . PHP_EOL;
    }
}
