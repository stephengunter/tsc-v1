<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Notice;

class NoticeMailCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notice;
    public function __construct(Notice $notice)
    {
        $this->notice=$notice;
    }

    
}
