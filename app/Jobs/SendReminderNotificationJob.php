<?php

namespace App\Jobs;

use App\Notifications\FcmNotification;
use App\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $tokens = DeviceToken::where('user_id',null)->pluck('fcm_token')->filter()->toArray();

        if (empty($tokens)) {
            Log::info('No tokens found for reminder notification.');
            return;
        }

        $title = "تذكير بالاشتراك";
        $body = "لا تنسى الاشتراك للاستفادة من كامل مميزات التطبيق!";

        try {
            (new FcmNotification($title, $body, $tokens))->toFcm();
            Log::info('Reminder notifications sent to ' . count($tokens) . ' devices.');
        } catch (\Throwable $e) {
            Log::error('Error sending reminder notifications: ' . $e->getMessage());
        }
    }
}
