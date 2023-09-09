<?php

namespace App\Events;

use Google\Service\AndroidPublisher\Timestamp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotification  implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public string  $user,
        public int  $type,
        public string  $message,
        public string  $link,
    )
    {
        
    }

    public function broadcastOn()
    {
        return ['notification'];
    }

    public function broadcastAs()
    {
        return 'notification';
    }
}
