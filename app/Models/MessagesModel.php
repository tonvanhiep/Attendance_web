<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagesModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'messenger';

    protected $fillable = [
        'id',
        'id_sender',
        'id_receiver',
        'content',
        'reply',
        'status',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function getInfoGroup($condition = null)
    {
        $messages = DB::table($this->table)
            ->rightJoin('messenger_group', $this->table . '.id_receiver', 'messenger_group.id')
            ->selectRaw("(
                CASE
                    WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_avatar`
                    WHEN `messenger_group`.`type` = 1 THEN (SELECT `employees`.`avatar` FROM `employees` WHERE `employees`.`id` IN (SELECT grm.id_employee FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group WHERE mssg.type = 1 AND grm.id_employee != " . $condition['id_sender'] . " AND mssg.id = " . $condition['id_receiver'] . "))
                END) AS avatar_group, (
                CASE
                    WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_name`
                    WHEN `messenger_group`.`type` = 1 THEN (SELECT CONCAT(`employees`.`last_name`, ' ', `employees`.`first_name`) FROM `employees` WHERE `employees`.`id` IN (SELECT grm.id_employee FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group WHERE mssg.type = 1 AND grm.id_employee != " . $condition['id_sender'] . " AND mssg.id = " . $condition['id_receiver'] . "))
                END) AS name_group");

        if (isset($condition['id_receiver'])) {
            if (is_array($condition['id_receiver'])) {
                $messages = $messages->where(function ($query) use ($condition) {
                    foreach ($condition['id_receiver'] as $value) {
                        $query->orWhere($this->table . '.id_receiver', $value);
                    }
                });
            } else $messages = $messages->where('messenger_group.id', $condition['id_receiver']);
        }

        return $messages->get();
    }

    public function selectMessages($condition = null)
    {
        if (isset($condition['broadcast']) && $condition['broadcast'] == true) $equal = '=';
        else $equal = '!=';
        $messages = DB::table($this->table)
            ->join('employees', $this->table . '.id_sender', 'employees.id')
            ->join('messenger_group', $this->table . '.id_receiver', 'messenger_group.id')
            ->join('group_member', $this->table . '.id_receiver', 'group_member.id_group')
            ->selectRaw("`messenger`.`id`, `id_sender`, `id_receiver`, `content`, `reply`, `messenger`.`status`, `messenger`.`created_at`, `avatar`, (
                CASE
                    WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_avatar`
                    WHEN `messenger_group`.`type` = 1 THEN (SELECT `employees`.`avatar` FROM `employees` WHERE `employees`.`id` IN (SELECT grm.id_employee FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group WHERE mssg.type = 1 AND grm.id_employee " . $equal . $condition['id_sender'] . " AND mssg.id = " . $condition['id_receiver'] . "))
                END) AS avatar_group, (
                CASE
                    WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_name`
                    WHEN `messenger_group`.`type` = 1 THEN (SELECT CONCAT(`employees`.`last_name`, ' ', `employees`.`first_name`) FROM `employees` WHERE `employees`.`id` IN (SELECT grm.id_employee FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group WHERE mssg.type = 1 AND grm.id_employee " . $equal . $condition['id_sender'] . " AND mssg.id = " . $condition['id_receiver'] . "))
                END) AS name_group, CONCAT(`employees`.`last_name`, ' ', `employees`.`first_name`) as name_sender, messenger_group.type");

        if (isset($condition['id_receiver'])) {
            if (is_array($condition['id_receiver'])) {
                $messages = $messages->where(function ($query) use ($condition) {
                    foreach ($condition['id_receiver'] as $value) {
                        $query->orWhere($this->table . '.id_receiver', $value);
                    }
                });
            } else $messages = $messages->where($this->table . '.id_receiver', $condition['id_receiver']);
        }

        if (isset($condition['id_message'])) {
            if (is_array($condition['id_message'])) {
                $messages = $messages->where(function ($query) use ($condition) {
                    foreach ($condition['id_message'] as $value) {
                        $query->orWhere($this->table . '.id', $value);
                    }
                });
            } else $messages = $messages->where($this->table . '.id', $condition['id_message']);
        }

        $messages = $messages->groupBy($this->table . '.id')
            ->orderBy($this->table . ".created_at");
        return $messages;
    }

    public function getMessages($condition = null)
    {
        return $condition = null ? [] : $this->selectMessages($condition)->get();
    }

    public function getListMessages($condition = null)
    {
        $result = DB::table($this->table . " as mess")
            ->join('messenger_group', 'mess.id_receiver', 'messenger_group.id')
            ->selectRaw("mess.id, mess.id_sender, mess.id_receiver, mess.content, mess.status, mess.created_at, messenger_group.type,
            (
                CASE
                WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_avatar`
                WHEN `messenger_group`.`type` = 1 THEN (SELECT `employees`.`avatar` FROM `employees` WHERE `employees`.`id` IN (
                    SELECT grm.id_employee
                    FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group
                    WHERE mssg.type = 1 AND grm.id_employee != " . $condition['id_sender'] . " AND mssg.id = mess.id_receiver))
                END) AS avatar_group, (
                CASE
                WHEN `messenger_group`.`type` = 0 THEN `messenger_group`.`group_name`
                WHEN `messenger_group`.`type` = 1 THEN (
                    SELECT CONCAT(`employees`.`last_name`, ' ', `employees`.`first_name`)
                    FROM `employees`
                    WHERE `employees`.`id` IN (SELECT grm.id_employee
                                               FROM messenger_group mssg JOIN group_member grm ON mssg.id = grm.id_group
                                               WHERE mssg.type = 1 AND grm.id_employee != " . $condition['id_sender'] . " AND mssg.id = mess.id_receiver))
                END) AS name_group, (
                CASE
                WHEN 1 = 1 THEN (
                    SELECT COUNT('*')
                    FROM `messenger` mess1 JOIN messenger_readed mrdd ON mess1.id_receiver = mrdd.id_group AND " . $condition['id_sender'] . " = mrdd.id_employee
                    WHERE mess1.id > mrdd.id_message AND mess1.id_sender != " . $condition['id_sender'] . " AND mess1.id_receiver = mess.id_receiver)
                END) AS message_unread");


        $result = $result->whereIn('mess.id', function (Builder $query) use ($condition) {
            return $query->selectRaw('MAX(messenger.id)')
                ->fromRaw('messenger inner join group_member on messenger.id_receiver = group_member.id_group')
                ->where('group_member.id_employee', (int) $condition['id_sender'])
                ->groupBy('messenger.id_receiver')->get();
        });

        $result = $result->orderByDesc('mess.created_at');
        //dd($result->get());
        return $result->get();
    }

    public function storeMessage($data = null)
    {
        $message = new MessagesModel();
        $message->id_sender = $data['id_sender'];
        $message->id_receiver = $data['id_receiver'];
        $message->content = $data['content'];
        $message->reply = $data['reply'];
        $message->status = $data['status'];
        $message->created_at = $data['created_at'];
        $message->updated_at = $data['created_at'];
        $message->created_user = $data['id_sender'];
        $message->updated_user = $data['id_sender'];
        $message->save();

        return $message;
    }

    public function setStatus($condition = null, $status)
    {
        $result = DB::table($this->table);

        if (isset($condition['id_receiver'])) {
            if (is_array($condition['id_receiver'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id_receiver'] as $value) {
                        $query->orWhere($this->table . '.id_receiver', $value);
                    }
                });
            } else $result = $result->where($this->table . '.id_receiver', $condition['id_receiver']);
        }

        if (isset($condition['id_message'])) {
            if (is_array($condition['id_message'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id_message'] as $value) {
                        $query->orWhere($this->table . '.id', $value);
                    }
                });
            } else $result = $result->where($this->table . '.id', $condition['id_message']);
        }

        $result = $result->update(['status' => $status]);
        return $result;
    }
}
