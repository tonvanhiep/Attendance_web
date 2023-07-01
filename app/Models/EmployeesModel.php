<?php

namespace App\Models;

use Database\Factories\EmployeesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeesModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'employees';

    protected $fillable = [
        'id',
        'last_name',
        'first_name',
        'birth_day',
        'gender',
        'address',
        'phone_number',
        'department',
        'position',
        'start_time',
        'end_time',
        'working_day',
        'salary',
        'office_id',
        'note',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user',
        'avatar',
        'join_day',
        'left_day',
        'status'
    ];

    public function selectEmployees($condition = null)
    {
        if ($condition == null) return;

        $employees = DB::table($this->table)
            ->leftJoin('offices', 'offices.id', $this->table . '.office_id')
            ->leftJoin('accounts', 'accounts.employee_id', $this->table . '.id')
            ->select(
                $this->table . '.id',
                'last_name',
                'first_name',
                'birth_day',
                'gender',
                $this->table . '.address',
                $this->table . '.phone_number',
                'department',
                'position',
                'start_time',
                'end_time',
                'working_day',
                'salary',
                'office_id',
                'avatar',
                'join_day',
                'left_day',
                'status',
                'office_name',
                'email'
            );

        if (isset($condition['gender'])) {
            $employees = $employees->where('gender', $condition['gender']);
        }

        if (isset($condition['id'])) {
            if (is_array($condition['id'])) {
                $employees = $employees->where(function ($query) use ($condition) {
                    foreach ($condition['id'] as $value) {
                        $query->orWhere($this->table . '.id', $value);
                    }
                });
            } else $employees = $employees->where($this->table . '.id', $condition['id']);
        }

        if (isset($condition['status'])) {
            if (is_array($condition['status'])) {
                $employees = $employees->where(function ($query) use ($condition) {
                    foreach ($condition['status'] as $value) {
                        $query->orWhere('status', $value);
                    }
                });
            } else $employees = $employees->where('status', $condition['status']);
        }

        if (isset($condition['search'])) {
            $employees = $employees->where(function ($query) use ($condition) {
                $query->orWhere('last_name', 'like', '%' . $condition['search'] . '%');
                $query->orWhere('first_name', 'like', '%' . $condition['search'] . '%');
                $query->orWhere($this->table . '.address', 'like', '%' . $condition['search'] . '%');
            });
        }

        if (isset($condition['office'])) {
            $employees = $employees->where('office_id', $condition['office']);
        }

        if (isset($condition['depart'])) {
            $employees = $employees->where('department', 'like', '%' . $condition['depart'] . '%');
        }

        if (isset($condition['sort'])) {
            switch ($condition['sort']) {
                case 2:
                    $employees = $employees->orderBy($this->table . '.id');
                    break;
                case 3:
                    $employees = $employees->orderBy($this->table . '.first_name');
                    break;
                case 4:
                    $employees = $employees->orderByDesc($this->table . '.first_name');
                    break;
                default:
                    $employees = $employees->orderByDesc($this->table . '.id');
                    break;
            }
        }



        return $employees;
    }

    public function getEmployees($condition = null)
    {
        $result = $this->selectEmployees($condition);
        return $result == null ? [] : $result->get();
    }

    public function getIdEmployees($condition = null)
    {
        $result = $this->selectEmployees($condition)->select($this->table . '.id');
        return $result == null ? [] : $result->get();
    }

    public function getCountEmployees($condition = null)
    {
        $result = $this->selectEmployees($condition);
        return $result == null ? 0 : $result->count();
    }

    public function email()
    {
        return $this->hasOne('App\Models\AccountsModel', 'employee_id', 'id');
    }

    public function pagination($condition = [], $page = 1, $perPage = 50)
    {
        return $this->selectEmployees($condition)->paginate($perPage, '*', 'page', $page);
    }

    protected static function newFactory()
    {
        return EmployeesFactory::new();
    }

    public function searchName($condition = null)
    {
        $employees = DB::table($this->table)
            ->leftJoin('accounts', 'accounts.employee_id', $this->table . '.id')
            ->selectRaw(
                "CONCAT(last_name, ' ', first_name) AS full_name,
                 email,
                 avatar,
                 id"
            );

        if (isset($condition['search'])) {
            $employees = $employees->where(function ($query) use ($condition) {
                $query->orWhereRaw("CONCAT(last_name, ' ', first_name) like '%" . $condition['search'] . "%'");
                $query->orWhereRaw("CONCAT(first_name, ' ', last_name) like '%" . $condition['search'] . "%'");
                $query->orWhere('accounts.email', 'like', '%' . $condition['search'] . '%');
            });
        }

        $employees = $employees->whereNot('id', $condition['id_sender']);
        return $employees->get();
    }
}
