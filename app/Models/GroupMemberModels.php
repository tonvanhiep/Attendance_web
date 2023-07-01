<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupMemberModels extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'group_member';

    protected $fillable = [
        'id_group',
        'id_employee',
        'status',
        'nick_name',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function addMemberIntoGroup($data = null)
    {
        $arrMember = [];
        if (is_array($data['member'])) {
            $arrMember = $data['member'];
        } else if (is_int($data['member'])) $arrMember[0] = $data['member'];

        foreach ($arrMember as $item) {
            $member = new GroupMemberModels();
            $member->id_employee = $item;
            $member->id_group = $data['id_receiver'];
            $member->status = 1;
            if (isset($data['nick_name'])) $member->nick_name = $data['nick_name'];
            $member->created_at = Carbon::now();
            $member->updated_at = Carbon::now();
            $member->created_user = Auth::user()->employee_id;
            $member->updated_user = Auth::user()->employee_id;
            $member->save();
        }
    }

    public function getGroupMember($condition = null)
    {
        $result = DB::table($this->table)->select('id_employee');

        if (isset($condition['status'])) {
            if (is_array($condition['status'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['status'] as $value) {
                        $query->orWhere('status', $value);
                    }
                });
            } else $result = $result->where('status', $condition['status']);
        }

        if (isset($condition['id_group'])) {
            if (is_array($condition['id_group'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id_group'] as $value) {
                        $query->orWhere('id_group', $value);
                    }
                });
            } else $result = $result->where('id_group', $condition['id_group']);
        }

        return $result->distinct()->get();
    }
}
