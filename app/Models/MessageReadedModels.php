<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MessageReadedModels extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'messenger_readed';

    protected $fillable = [
        'id_group',
        'id_employee',
        'id_message',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function setMessageReaded($condition = null, $id_message = 0)
    {
        DB::table($this->table)
            ->updateOrInsert(
                ['id_group' => $condition['id_receiver'], 'id_employee' => $condition['id_sender']],
                [
                    'id_message' => $id_message,
                    'updated_at' => Carbon::now(),
                    'updated_user' => $condition['id_sender']
                ]
            );
    }
}
