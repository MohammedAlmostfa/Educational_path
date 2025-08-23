<?php

namespace App\Jobs;

use App\Notifications\FcmNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $title;
    protected string $body;
    protected array $tokens;

    public function __construct(string $title, string $body, array $tokens)
    {
        $this->title = $title;
        $this->body = $body;
        $this->tokens = $tokens;
    }

    public function handle()
    {
        try {
            (new FcmNotification(
                $this->title,
                $this->body,
                $this->tokens
            ))->toFcm();
        } catch (\Throwable $e) {
            Log::error('FCM Job error: ' . $e->getMessage());
        }
    }
}
