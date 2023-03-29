<?php

namespace App\Exports;

use App\Models\TimesheetsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TimesheetCsv implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $condition = [];

    public function __construct(Request $request)
    {
        $this->condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
    }

    public function collection()
    {
        // return TimesheetsModel::all();
        $timesheet = new TimesheetsModel();
        $result = $timesheet->selectAttendances($this->condition)
            ->select(
                'employees.last_name',
                'employees.first_name',
                'employees.id',
                'office_name',
                'timesheets.timekeeper_id',
                'timekeeping_at',
            );
        return $result->get();
    }

    public function headings(): array
    {
        return [
            'Last Name',
            'First Name',
            'ID',
            'Office',
            'Timekeeper',
            'DateTime',
        ];
    }
}
