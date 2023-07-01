<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageGroupModels extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'messenger_group';

    protected $fillable = [
        'id',
        'group_name',
        'group_avatar',
        'type',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function selectAllGroup($condition)
    {
        $result = DB::table($this->table)
            ->join('group_member', $this->table . '.id', 'group_member.id_group')
            ->select('*');

        if (isset($condition['id_employee'])) {
            if (is_array($condition['id_employee'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id_employee'] as $value) {
                        $query->orWhere('group_member.id_employee', $value);
                    }
                });
            } else $result = $result->where('group_member.id_employee', $condition['id_employee']);
        }

        return $result;
    }

    public function getAllGroupByEmployeeId($condition)
    {
        return $condition == null ? [] : $this->selectAllGroup($condition)->get();
    }

    public function checkGroupMessageExisted($condition = null)
    {
        //       SELECT COUNT(gm.id_group)
        //       FROM `group_member` gm JOIN messenger_group mg ON gm.id_group = mg.id
        //       WHERE (id_employee = 21 OR id_employee = 50) AND mg.type = 1
        //       GROUP BY gm.id_group
        //       HAVING COUNT(gm.id_group) = 2
        $result = DB::table($this->table)
            ->join('group_member', $this->table . '.id', 'group_member.id_group')
            ->selectRaw('COUNT(id), id');

        if (isset($condition['is_private']) && $condition['is_private'] == 1 && isset($condition['member'])) {
            if (is_array($condition['member'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['member'] as $value) {
                        $query->orWhere('group_member.id_employee', $value);
                    }
                });
            } else $result = $result->where('group_member.id_employee', $condition['member']);

            $result = $result->where($this->table . '.type', $condition['is_private']);
            $result = $result->groupBy('id_group')
                ->havingRaw('COUNT(id) = ' . count($condition['member']));
        }

        if (isset($condition['is_private']) && $condition['is_private'] != 1) {
            $result = $result->where($this->table . '.id', $condition['id_receiver'])
                ->where('group_member.id_employee', $condition['id_sender']);
        }
        $result = $result->get();

        return isset($result[0]->id) && $result[0]->id != null ? $result[0]->id : -1;
    }

    public function createGroupMessage($data = null)
    {
        if ($data == null) return -1;
        $group = new MessageGroupModels();
        $group->type = $data['is_private'];
        $group->created_at = Carbon::now();
        $group->updated_at = Carbon::now();
        $group->created_user = Auth::user()->employee_id;
        $group->updated_user = Auth::user()->employee_id;
        $group->save();
        return $group->id;
    }
}
