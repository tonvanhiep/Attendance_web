<?php

namespace App\Events;

use App\Models\GroupMemberModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Chat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        $groupMemberModel = new GroupMemberModels();
        $member = $groupMemberModel->getGroupMember(['status' => 1, 'id_group' => $this->message->id_receiver]);
        $arr = [];
        foreach ($member as $item) {
            if ($item->id_employee == $this->message->id_sender) continue;
            array_push($arr, new PrivateChannel('user.' . $item->id_employee));
        }
        return $arr;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
