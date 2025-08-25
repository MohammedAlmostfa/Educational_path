<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeviceToken
 *
 * Represents a device token for push notifications (FCM).
 * Each device token may belong to a user.
 */
class DeviceToken extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'device_id', // Unique identifier for the device
        'fcm_token', // Firebase Cloud Messaging token
    ];

    /**
     * Get the user associated with this device token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
