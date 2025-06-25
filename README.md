# Framework-Agnostic Scheduler Adapter Demo

This is a demonstration of a pluggable task scheduling system with minimal coupling to specific frameworks or libraries. 
The project supports multiple scheduler backends (Laravel and Crunz) and uses a custom mutex implementation to avoid race conditions in distributed environments.

---

## Goal

To provide a unified abstraction over different scheduler implementations, with the ability to:

- Schedule tasks using cron expressions
- Swap scheduler backend (Laravel Scheduler or Crunz) easily
- Plug in a custom mutex (e.g. database-based) to safely execute tasks only once at a time
- Keep the system clean, testable, and decoupled from Laravel

---

## Project Structure

<pre lang="text"><code>
└── app/ 
    └── Scheduler/ 
        ├── Interfaces/ 
            │ ├── SchedulerInterface.php 
            │ ├── TaskInterface.php 
            │ └── MutexInterface.php 
        ├── Adapters/ 
            │ ├── LaravelSchedulerAdapter.php 
            │ └── CrunzSchedulerAdapter.php 
        ├── Mutexes/ 
            │ └── DatabaseMutex.php 
        ├── Tasks/ 
            │ └── SendBirthdayCardsTask.php 
        └── Helpers/ 
            └── CronHelper.php 
</code></pre>

---


### Demo Routes

Laravel Scheduler Adapter
Schedules and runs the task using Laravel's built-in scheduler:

`GET /demo-scheduler-laravel`

Crunz Scheduler Adapter
Schedules and runs the task using the Crunz-style adapter with our own logic:

`GET /demo-scheduler-crunz`

### Mutex Implementation

The custom DatabaseMutex stores locks in a dedicated table (mutex_locks) with an expiration timestamp.
This avoids issues with Laravel's native cache-based mutex in distributed or multi-process environments.

To customize or extend the mutex logic, simply implement the MutexInterface.

### Cron Expression Helper

CronHelper provides reusable static methods for common cron expressions:

`CronHelper::everyMinute();         // '* * * * *'`

`CronHelper::dailyAt('07:30');      // '30 7 * * *'`

`CronHelper::everyWeekdayAt('09:00');`

