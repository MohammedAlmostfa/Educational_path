<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;
use Throwable;

class FcmNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected array $tokens;

   
    public function __construct(string $title, string $body, array $tokens)
    {
        $this->title = $title;
        $this->body = $body;
        $this->tokens = $tokens;
    }

   
    public function via($notifiable)
    {
        return ['fcm'];
    }

   
    public function toFcm($notifiable = null)
    {
        if (empty($this->tokens)) {
            Log::warning('No FCM tokens provided');
            return;
        }

        $firebaseFactory = (new Factory)
            ->withServiceAccount(storage_path(config('services.fcm.credentialsPath')));
        $messaging = $firebaseFactory->createMessaging();

       
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $this->title,
                'body'  => $this->body,
            ])
            ->withData([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);

        try {
            $response = $messaging->sendMulticast($message, $this->tokens);
            Log::info('FCM sent to ' . count($this->tokens) . ' devices');
        } catch (Throwable $e) {
            Log::error('FCM error: ' . $e->getMessage());
        }
    }
}
