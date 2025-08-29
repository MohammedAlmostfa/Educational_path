<?php

namespace App\Notifications;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Throwable;

class FcmNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected array $tokens;

    /**
     * Initialize the notification with title, body, and device tokens.
     */
    public function __construct(string $title, string $body, array $tokens)
    {
        $this->title  = $title;
        $this->body   = $body;
        $this->tokens = array_filter($tokens, fn($t) => is_string($t) && !empty($t));
    }

    /**
     * Specify the notification channels.
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Prepare and send the FCM notification.
     */
    public function toFcm($notifiable = null)
    {
        if (empty($this->tokens)) {
            Log::warning('No FCM tokens provided');
            return;
        }

        $credentialsPath = config('services.fcm.credentialsPath');
        $fullPath = storage_path($credentialsPath);

        if (!file_exists($fullPath)) {
            Log::error("FCM credentials not found at: $fullPath");
            return;
        }

        try {
            $messaging = (new Factory)
                ->withServiceAccount($fullPath)
                ->createMessaging();

            $message = CloudMessage::new()
                ->withNotification([
                    'title' => $this->title,
                    'body'  => $this->body,
                ])
                ->withData([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);

            $response = $messaging->sendMulticast($message, $this->tokens);

            Log::info('FCM sent to ' . count($this->tokens) . ' devices');

        } catch (Throwable $e) {
            Log::error('FCM error: ' . $e->getMessage());
        }
    }
}
