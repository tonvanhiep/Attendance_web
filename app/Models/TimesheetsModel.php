<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\TimesheetsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimesheetsModel extends Model
{
    use HasFactory;
    protected $table = 'timesheets';

    protected $fillable = [
        'id',
        'employee_id',
        'timekeeper_id',
        'timekeeping_at',
        'face_image',
        'status',
        'note',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];



    protected static function newFactory()
    {
        return TimesheetsFactory::new();
    }

    public function selectAttendances($condition = null)
    {
        // dd($condition);
        if ($condition === null) return [];
        $result = DB::table($this->table)->join('timekeepers', 'timekeepers.id', '=', $this->table . '.timekeeper_id')
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->join('offices', 'offices.id', '=', 'employees.office_id')
            ->select(
                'employees.id',
                $this->table . '.status',
                'employees.first_name',
                'employees.last_name',
                $this->table . '.id as attendance_id',
                'office_name',
                'timekeepers.timekeeper_name',
                'timekeeping_at',
                'face_image'
            )
            ->orderByDesc($this->table . '.timekeeping_at')
            ->orderByDesc($this->table . '.id');

        if (isset($condition['office']) && $condition['office'] != 0) {
            $result = $result->where('offices.id', $condition['office']);
        }
        if (isset($condition['employee_id']) && $condition['employee_id'] != 0) {
            $result = $result->where('employees.id', $condition['employee_id']);
        }
        if (isset($condition['id']) && $condition['id'] != 0) {
            $result = $result->where($this->table . '.id', $condition['id']);
        }
        if (isset($condition['from'])) {
            $result = $result->where('timekeeping_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $result = $result->where('timekeeping_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status']) && $condition['status'] != 0) {
            $result = $result->where($this->table . '.status', $condition['status']);
        }

        return $result;
    }

    public function getAttendances($condition = null)
    {
        $timesheet = $this->selectAttendances($condition);
        return $timesheet === [] ? [] : $timesheet->get();
    }


    public function selectTimesheetsByEmployeeId($condition = null)
    {
        // dd($condition);
        if ($condition == null) return [];
        $result = DB::table($this->table)->join('timekeepers', 'timekeepers.id', '=', $this->table . '.timekeeper_id')
            ->join('offices', 'offices.id', '=', 'timekeepers.office_id')
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            // ->select('employee_id', 'timekeeper_id', 'timekeeping_at', 'face_image', 'status', DB::raw('date(timekeeping_at) as date'), DB::raw('MIN(time(timekeeping_at)) as check_in'), DB::raw('MAX(time(timekeeping_at)) as check_out'), 'employees.first_name')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.department',
                'employees.start_time',
                'employees.end_time',
                'employees.working_day',
                'office_name',
                'timekeeper_id',
                'timekeeping_at',
                'face_image',
                DB::raw('date(timekeeping_at) as date'),
                DB::raw('MIN(time(timekeeping_at)) as check_in'),
                DB::raw('MAX(time(timekeeping_at)) as check_out'),
            )
            ->orderByDesc('date')
            ->orderByDesc($this->table . '.employee_id');

        if (isset($condition['id'])) {
            if (is_array($condition['id'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id'] as $value) {
                        $query->orWhere($this->table . '.employee_id', $value);
                    }
                });
            } else $result = $result->where($this->table . '.employee_id', $condition['id']);
        }

        if (isset($condition['from'])) {
            $result = $result->where('timekeeping_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $result = $result->where('timekeeping_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status'])) {
            $result = $result->where($this->table . '.status', $condition['status']);
        }
        $result = $result->groupByRaw('date(timekeeping_at)');
        return $result;
    }

    public function getTimesheetsByEmployeeId($condition = null)
    {
        $timesheet = $this->selectTimesheetsByEmployeeId($condition);
        return $timesheet == [] ? [] : $timesheet->get();
    }

    public function pagination($condition = [], $page = 1, $perPage = 50)
    {
        return $this->selectTimesheetsByEmployeeId($condition)->paginate($perPage, '*', 'page', $page);
    }

    public function paginationAttenndance($condition = [], $page = 1, $perPage = 50)
    {
        return $this->selectAttendances($condition)->paginate($perPage, '*', 'page', $page);
    }

    public function getCountAttendanceToCheck($condition = null) {
        $timesheet = $this->selectAttendances($condition);
        return $timesheet == [] ? [] : $timesheet->count();
    }
    //user
    public function selectTimesheetsforUser($condition = null)
    {
        if ($condition == null) return [];
        $result = DB::table($this->table)
            ->join('timekeepers', 'timekeepers.id', '=', $this->table . '.timekeeper_id')
            ->join('offices', 'offices.id', '=', 'timekeepers.office_id')
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.department',
                'employees.start_time',
                'employees.end_time',
                'employees.working_day',
                'timekeepers.timekeeper_name',
                'office_name',
                'timekeeper_id',
                'timekeeping_at',
                'face_image',
                DB::raw('date(timekeeping_at) as date'),
                DB::raw('MIN(time(timekeeping_at)) as check_in'),
                DB::raw('MAX(time(timekeeping_at)) as check_out'),
            )
            ->where('employee_id', '=', Auth::user()->employee_id)
            ->orderByDesc('date')
            ->orderByDesc($this->table . '.employee_id');
        if (isset($condition['id'])) {
            if (is_array($condition['id'])) {
                $result = $result->where(function ($query) use ($condition) {
                    foreach ($condition['id'] as $value) {
                        $query->orWhere($this->table . '.employee_id', $value);
                    }
                });
            } else $result = $result->where($this->table . '.employee_id', $condition['id']);
        }

        if (isset($condition['from'])) {
            $result = $result->where('timekeeping_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $result = $result->where('timekeeping_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status'])) {
            $result = $result->where($this->table . '.status', $condition['status']);
        }
        $result = $result->groupByRaw('date(timekeeping_at)');
        return $result;
    }

    public function getTimesheetsforUser($condition = null)
    {
        $timesheet = $this->selectTimesheetsforUser($condition);
        return $timesheet == [] ? [] : $timesheet->get();
    }

    public function paginationTimesheetsforUser($condition = [], $page = 1, $perPage = 50)
    {
        return $this->selectTimesheetsforUser($condition)->paginate($perPage, '*', 'page', $page);
    }
    //user

    public function saveAttendance($data = null)
    {
        if ($data == null) return;
        DB::table($this->table)->insert([
            'employee_id' => $data['employee_id'],
            'timekeeper_id' => $data['timekeeper_id'],
            'timekeeping_at' => Carbon::now(),
            'face_image' => $data['face_image'],
            'status' => $data['status'],
            'created_user' => $data['employee_id'],
            'updated_user' => $data['employee_id'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    public function searchbyFromTo()
    {
        $result = DB::table($this->table)
            ->join('timekeepers', 'timekeepers.id', '=', $this->table . '.timekeeper_id')
            ->join('offices', 'offices.id', '=', 'timekeepers.office_id')
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.department',
                'employees.start_time',
                'employees.end_time',
                'employees.working_day',
                'timekeepers.timekeeper_name',
                'office_name',
                'timekeeper_id',
                'timekeeping_at',
                'face_image',
                DB::raw('date(timekeeping_at) as date'),
                DB::raw('MIN(time(timekeeping_at)) as check_in'),
                DB::raw('MAX(time(timekeeping_at)) as check_out'),
            )
            ->where('employee_id', '=', Auth::user()->employee_id)
            ->orderByDesc('date')
            ->orderByDesc($this->table . '.employee_id');
        $result = $result->groupByRaw('date(timekeeping_at)');
        return $result;
    }

    public function getCountAttendanceWithStatus($condition = null)
    {
        $result = DB::table($this->table)->select('id')->where('status', $condition['status']);
        return $result->count();
    }

    public function selectDetailInfoAttendance($condition = null)
    {
        if ($condition == null) return [];
        $result = DB::table($this->table)->join('timekeepers', 'timekeepers.id', '=', $this->table . '.timekeeper_id')
            ->join('offices', 'offices.id', '=', 'timekeepers.office_id')
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->leftJoin('face_employee_images', 'face_employee_images.employee_id', '=', $this->table . '.employee_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.avatar',
                'employees.department',
                'office_name',
                'timekeepers.timekeeper_name',
                'image_url',
                'employees.gender',
                $this->table . '.status',
                $this->table . '.face_image',
                'timekeeping_at'
            )
            ->orderByDesc($this->table . '.timekeeping_at')
            ->orderByDesc($this->table . '.id');

        if (isset($condition['id'])) {
            $result = $result->where($this->table . '.id', $condition['id']);
        }

        return $result;
    }

    public function getDetailInfoAttendance($condition = null)
    {
        if ($condition == null) return [];

        $result = $this->selectDetailInfoAttendance($condition);
        return $result == [] ? [] : $result->get();
    }

    public function updateStatus($data = null)
    {
        if ($data == null) return;

        DB::table($this->table)
            ->where('id', $data['id'])
            ->update(['status' => $data['status']]);
    }
}
