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
            )
            ->where('employee_id', '=', Auth::user()->employee_id)
            ->orderByDesc($this->table . '.employee_id');
        return $result;
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
