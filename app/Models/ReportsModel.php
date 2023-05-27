<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportsModel extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'id',
        'employee_id',
        'comment',
        'status',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function selectReportbyEmployeeId()
    {
        $result = DB::table($this->table)
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                $this->table.'.comment',
                $this->table.'.created_at',
                $this->table.'.updated_at',
                // DB::raw('date(created_at) as date'),
            )
            ->where('employee_id', '=', Auth::user()->employee_id)
            ->orderByDesc($this->table . '.employee_id');
        return $result;
    }

    public function getReportbyEmployeeId($condition = null) {
        $report = $this->selectReportbyEmployeeId($condition);
        if (isset($condition['id'])) {
            if (is_array($condition['id'])) {
                $report = $report->where(function ($query) use ($condition) {
                    foreach ($condition['id'] as $value) {
                        $query->orWhere($this->table . '.employee_id', $value);
                    }
                });
            } else $report = $report->where($this->table . '.employee_id', $condition['id']);
        }

        if (isset($condition['from'])) {
            $report = $report->where($this->table.'.created_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $report = $report->where($this->table.'.created_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status'])) {
            $report = $report->where($this->table . '.status', $condition['status']);
        }
        return $report == [] ? [] : $report->get();
    }

    public function selectAllReports($condition = null)
    {
        $result = DB::table($this->table)
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->join('offices', 'offices.id', '=', 'employees.office_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.department',
                'offices.office_name',
                $this->table.'.comment',
                $this->table.'.status',
                $this->table.'.created_at',
                DB::raw('date(reports.created_at) as date'),
            )
            // ->orderByDesc($this->table . '.employee_id')
            ->orderByDesc($this->table.'.created_at');

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
            $result = $result->where($this->table.'.created_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $result = $result->where($this->table.'.created_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status'])) {
            $result = $result->where($this->table . '.status', $condition['status']);
        }
        if (isset($condition['office']) && $condition['office'] != 0) {
            $result = $result->where('offices.id', $condition['office']);
        }
        return $result;
    }

    public function getAllReports($condition = null)
    {
        $report = $this->selectAllReports($condition);
        return $report == [] ? [] : $report->get();
    }

    public function pagination($condition = [], $page = 1, $perPage = 50)
    {
        return $this->selectAllReports($condition)->paginate($perPage, '*', 'page', $page);
    }

    public function saveReport($data = null)
    {
        if ($data == null) return;
        DB::table($this->table)->insert([
            'employee_id' => $data['employee_id'],
            'status' => $data['status'],
            'comment' => $data['comment'],
            'created_user' => $data['employee_id'],
            'updated_user' => $data['employee_id'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function getCountReportWithStatus($condition = null)
    {
        $result = DB::table($this->table)->select('id')->where('status', $condition['status']);
        return $result->count();
    }
}
