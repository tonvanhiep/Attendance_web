<?php

namespace App\Events;

use App\Models\TimesheetsModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Attendance implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $id_attendance;

    public function __construct($id)
    {
        $this->id_attendance = $id;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('attendance');
    }

    public function broadcastWith()
    {
        $timesheet = new TimesheetsModel();
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $attendance = $timesheet->getAttendances(['id' => $this->id_attendance]);
        return [
            'waiting_confirm' => $waitConfirm,
            'attendance' => $attendance[0],
        ];
    }



}
