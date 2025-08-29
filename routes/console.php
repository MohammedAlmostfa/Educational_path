<?php

use App\Jobs\SendReminderNotificationJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
schedule::job(new SendReminderNotificationJob())->twiceDaily(1, 13);
