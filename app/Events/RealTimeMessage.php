<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Sample broadcasting
 *
 * References:
 * - https://dterumalai.medium.com/add-laravel-websockets-to-sail-in-5-mins-71c8d9ceeb8a
 * - https://christoph-rumpel.com/2020/11/laravel-real-time-notifications
 */
class RealTimeMessage implements ShouldBroadcast
{
    use SerializesModels;

    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('events');
    }
}
