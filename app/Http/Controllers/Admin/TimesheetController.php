<?php

namespace App\Http\Controllers\Admin;

use stdClass;
use Carbon\Carbon;
use App\Models\NoticesModel;
use App\Models\OfficesModel;
use Illuminate\Http\Request;
use App\Exports\TimesheetCsv;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();
        $timesheet = new TimesheetsModel();
        $office = new OfficesModel();
        // $list = new stdClass();
        // dd($list);

        $perPage = $request->show == null ? 50 : $request->show;
        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from') == null ? date('Y-m-01') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $employeesList = $employees->pagination($condition, $request->page, $perPage);
        // dd($employeesList);
        $list = [];
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];
        $from = Carbon::createFromFormat('Y-m-d', $condition['from']);
        $to = Carbon::createFromFormat('Y-m-d', $condition['to']);
        while (1) {
            if (!($from <= $to)) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            $from = $from->addDays(1);
        }

        foreach ($employeesList as $item) {
            // dd(gettype($item));
            $timesheetList = $timesheet->getTimesheetsByEmployeeId(['id' => $item->id, 'from' => $condition['from'], 'to' => $condition['to']]);
            // dd($timesheetList);
            $lateList = 0;
            $earlyList = 0;
            $presentList = 0;
            $offList = 0;
            $total = 0;
            $daylist = explode('|', $item->working_day);

            foreach ($daylist as $day) {
                if ((int)$day >= 1 && (int)$day <= 7) {
                    $total += $totalWeekDay[(int)$day - 1];
                }
                foreach ($timesheetList as $timesheetItem) {
                    if (Carbon::parse($timesheetItem->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                        $presentList++;
                        if ($timesheetItem->check_in > $timesheetItem->start_time) {
                            $lateList++;
                        }
                        if ($timesheetItem->check_out < $timesheetItem->end_time) {
                            $earlyList++;
                        }
                    }
                }
            }

            // foreach ($timesheetList as $item) {
            //     if ($item->check_in > $item->start_time) {
            //         $lateList++;
            //     }
            //     if ($item->check_out < $item->end_time) {
            //         $earlyList++;
            //     }
            // }

            $arr_ = [
                'present' => $presentList,
                'late' => $lateList,
                'early' => $earlyList,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'id' => $item->id,
                'office_name' => $item->office_name,
                'department' => $item->department,
                'working_day' => $item->working_day,
                'total' => $total,
                'off' => $total - count($timesheetList),
            ];
            array_push($list, $arr_);
        }
        // dd($list);

        $pagination = [
            'perPage' => $employeesList->perPage(),
            'lastPage' => $employeesList->lastPage(),
            'currentPage' => $employeesList->currentPage()
        ];

        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        // $notification = [];
        $page = 'timesheet';
        return view('admin.timesheet', compact('notification', 'page', 'office', 'employeesList', 'page', 'pagination', 'condition', 'list','waitConfirm'));
    }

    public function exportCsv(Request $request)
    {
        $csv = new TimesheetCsv($request);
        return Excel::download($csv, 'stafflist' . date("Ymd-His") . '.csv');
    }

    public function exportPdf(Request $request)
    {
        $employees = new EmployeesModel();

        $condition = [
            'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'search' => $request->input('search'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $list = $employees->getEmployees($condition);

        $pdf = PDF::loadView('admin.templates.staffpdf',  compact('list'))->setPaper('a4', 'landscape');
        return $pdf->download('stafflist' . date("Ymd-His") . '.pdf');
    }

    public function detail(Request $request)
    {
        $office = new OfficesModel();
        $office = $office->getOffices([]);

        $notification = [];
        $page = 'timesheet';
        return view('admin.detail', compact('notification', 'page', 'office'));
    }

    public function attendance(Request $request)
    {
        $notification = [];
        $page = 'timesheet';
        return view('admin.attendance-detail', compact('notification', 'page'));
    }

    public function pagination(Request $request)
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();
        $timesheet = new TimesheetsModel();
        $office = new OfficesModel();

        $search = $request->input('search');
        $perPage = $request->show == null ? 50 : $request->show;

        $condition = [
            // 'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            // 'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'status' => 1,
            'sort' => 1,
            'search' => $search,
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
            'from' => $request->input('from') == null ? date('Y-m-d') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
        ];
        // dd($list,$condition);
        $employeesList = $employees->pagination($condition, $request->page, $perPage);
        // dd($employeesList);
        $list = [];
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];
        $from = Carbon::createFromFormat('Y-m-d', $condition['from']);
        $to = Carbon::createFromFormat('Y-m-d', $condition['to']);
        while (1) {
            if (!($from <= $to)) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            $from = $from->addDays(1);
        }

        foreach ($employeesList as $item) {
            // dd(gettype($item));
            $timesheetList = $timesheet->getTimesheetsByEmployeeId(['id' => $item->id, 'from' => $condition['from'], 'to' => $condition['to']]);
            $lateList = 0;
            $earlyList = 0;
            $presentList = 0;
            $offList = 0;
            $total = 0;
            $daylist = explode('|', $item->working_day);

            foreach ($daylist as $day) {
                if ((int)$day >= 1 && (int)$day <= 7) {
                    $total += $totalWeekDay[(int)$day - 1];
                }
                foreach ($timesheetList as $timesheetItem) {
                    if (Carbon::parse($timesheetItem->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                        $presentList++;
                        if ($timesheetItem->check_in > $timesheetItem->start_time) {
                            $lateList++;
                        }
                        if ($timesheetItem->check_out < $timesheetItem->end_time) {
                            $earlyList++;
                        }
                    }
                }
            }

            $arr_ = [
                'present' => $presentList,
                'late' => $lateList,
                'early' => $earlyList,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'id' => $item->id,
                'office_name' => $item->office_name,
                'department' => $item->department,
                'working_day' => $item->working_day,
                'total' => $total,
                'off' => $total - count($timesheetList),
            ];
            array_push($list, $arr_);
        }
        // dd($list);

        $pagination = [
            'perPage' => $employeesList->perPage(),
            'lastPage' => $employeesList->lastPage(),
            'currentPage' => $employeesList->currentPage()
        ];

        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        // $notification = [];
        $page = 'timesheet';
        $returnHTML = view('admin.pagination.timesheet', compact('list', 'pagination', 'notification', 'page', 'office', 'employeesList', 'page', 'condition',))->render();
        return response()->json($returnHTML);
    }
}
