<?php

namespace App\Events;

use App\Models\EmployeesModel;
use App\Models\FaceEmployeeImagesModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateImageRecognition implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $employee_id;
    public $office_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($employee_id)
    {
        $this->employee_id = $employee_id;
        $this->office_id = EmployeesModel::find($this->employee_id)->office_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('office.' . $this->office_id);
    }

    public function broadcastWith()
    {
        $imageModel = new FaceEmployeeImagesModel();
        $image = $imageModel->getImages(['status' => 1, 'office' => $this->office_id, 'employee_id' => $this->employee_id]);
        $arr = [];
        $arrName = [];
        foreach ($image as $key => $value) {
            $id = $value->id;
            if (isset($arr[$id])) {
                array_push($arr[$id], $value->description);
            } else {
                $arr[$id] = [];
                $arrName[$id] = $value->last_name . ' ' . $value->first_name;
                array_push($arr[$id], $value->description);
            }
        }

        return [
            'employee_id' => $this->employee_id,
            'name' => $arrName,
            'image' => $arr
        ];
    }
}
