<?php

namespace App\Exports;

use App\Models\EmployeesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;


class StaffExportCsv implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $table = 'employees';

    public $condition = [];
    public function __construct(Request $request)
    {
        $this->condition = [
            'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'search' => $request->input('search'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart')
        ];
    }

    public function collection()
    {
        $employees = new EmployeesModel();

        $result = $employees->selectEmployees($this->condition)
            ->select(
                'last_name',
                'first_name',
                'email',
                'birth_day',
                DB::raw('CASE gender WHEN 0 THEN "Female" WHEN 1 THEN "Male" ELSE "Error" END AS gender'),
                $this->table.'.address',
                $this->table.'.phone_number',
                'department',
                'position',
                'start_time',
                'end_time',
                'working_day',
                'salary',
                'office_name',
                'join_day',
                'left_day',
                DB::raw('CASE status WHEN 0 THEN "Quit Job" WHEN 1 THEN "Active" ELSE "Maternity Leave" END AS status')
            );
        return $result->get();
    }

    public function headings(): array
    {
        return [
            'Last Name',
            'First Name',
            'Email',
            'Birth Day',
            'Gender',
            'Address',
            'Phone Number',
            'Department',
            'Position',
            'Start Time',
            'End Time',
            'Working Day',
            'Salary',
            'Office',
            'Join Day',
            'Left Day',
            'Status'
        ];
    }
}
