<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class FcmNotification
 *
 * Sends push notifications via Firebase Cloud Messaging (FCM).
 * Supports sending to multiple device tokens.
 */
class FcmNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected array $tokens;

    /**
     * Initialize the notification with title, body, and device tokens.
     *
     * @param string $title Notification title
     * @param string $body  Notification body
     * @param array  $tokens Device FCM tokens
     */
    public function __construct(string $title, string $body, array $tokens)
    {
        $this->title = $title;
        $this->body = $body;
        $this->tokens = $tokens;
    }

    /**
     * Specify the notification channels.
     * Here we use a custom FCM channel.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Prepare and send the FCM notification.
     *
     * @param mixed|null $notifiable
     * @return void
     */
    public function toFcm($notifiable = null)
    {
        if (empty($this->tokens)) {
            Log::warning('No FCM tokens provided'); // No devices to send
            return;
        }

        // Initialize Firebase messaging service
        $firebaseFactory = (new Factory)
            ->withServiceAccount(storage_path(config('services.fcm.credentialsPath')));
        $messaging = $firebaseFactory->createMessaging();

        // Create the notification message
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $this->title,
                'body'  => $this->body,
            ])
            ->withData([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // For Flutter apps
            ]);

        // Send message to multiple device tokens and log result
        try {
            $response = $messaging->sendMulticast($message, $this->tokens);
            Log::info('FCM sent to ' . count($this->tokens) . ' devices');
        } catch (Throwable $e) {
            Log::error('FCM error: ' . $e->getMessage());
        }
    }
}
