<?php

use App\Scheduler\Adapters\CrunzSchedulerAdapter;
use App\Scheduler\Adapters\LaravelSchedulerAdapter;
use App\Scheduler\Helpers\CronHelper;
use App\Scheduler\Mutexes\DatabaseMutex;
use App\Scheduler\Tasks\SendBirthdayCardsTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo-scheduler-laravel', function () {
    $mutex = new DatabaseMutex();

    $scheduler = new LaravelSchedulerAdapter(App::make(Schedule::class), $mutex);
    $task = new SendBirthdayCardsTask();
    // scheduled only task
    // $scheduler->schedule($task, CronHelper::dailyAt('09:00'));

    // scheduled task to run now
    $scheduler->schedule($task, CronHelper::everyMinute());

    $scheduler->runDueTasks();

    return 'Laravel scheduler demo done';
});

Route::get('/demo-scheduler-crunz', function () {
    $mutex = new DatabaseMutex();

    $scheduler = new CrunzSchedulerAdapter($mutex);
    $task = new SendBirthdayCardsTask();
    // scheduled only task
    // $scheduler->schedule($task, CronHelper::dailyAt('09:00'));

    // scheduled task to run now
    $scheduler->schedule($task, CronHelper::everyMinute());

    $scheduler->runDueTasks();

    return 'Crunz scheduler demo done';
});
