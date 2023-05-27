<?php

namespace App\Events;

use App\Models\ReportsModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Report implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $id_report;

    public function __construct($id)
    {
        //
        $this->id_report = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('report');
    }

    public function broadcastWith()
    {
        $report = new ReportsModel();
        $waitConfirm = $report->getCountAttendanceWithStatus(['status' => 2]);
        $comments = $report->getAllReports(['id' => $this->id_report]);
        return [
            'test' => 2,
            'comments' =>$comments,
        ];
    }
}
