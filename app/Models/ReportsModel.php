<?php

namespace App\Models;

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
            $report = $report->where('timekeeping_at', '>=', $condition['from']);
        }
        if (isset($condition['to'])) {
            $report = $report->where('timekeeping_at', '<=', $condition['to'] . ' 23:59:59');
        }
        if (isset($condition['status'])) {
            $report = $report->where($this->table . '.status', $condition['status']);
        }
        return $report == [] ? [] : $report->get();
    }

    public function selectAllReports()
    {
        $result = DB::table($this->table)
            ->join('employees', 'employees.id', '=', $this->table . '.employee_id')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                $this->table.'.comment',
                $this->table.'.created_at',
            )
            ->orderByDesc($this->table . '.employee_id');
        return $result;
    }
}
