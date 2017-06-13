<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContactInfoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contactInfo;
    public $current_user;
    public $user_id;
    public $center_id;
    public function __construct($contactInfo,$current_user,$user_id=0,$center_id=0)
    {
        $this->contactInfo=$contactInfo;
        $this->current_user=$current_user;
        $this->user_id=$user_id;
        $this->center_id=$center_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
