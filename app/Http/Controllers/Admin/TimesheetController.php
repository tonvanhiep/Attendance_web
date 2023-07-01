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
            'from' => $request->get('from') != null && $request->get('from') <= date('Y-m-d') ? $request->input('from') : date('Y-m-01'),
            'to' => $request->get('to') != null && $request->get('to') <= date('Y-m-d') ? $request->input('to') : date('Y-m-d'),
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
        $isToday = 0;
        while (1) {
            if (!($from <= $to)) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            if($from->toDateString() == $condition['today']) $isToday = 1;
            $from = $from->addDays(1);
        }

        foreach ($employeesList as $item) {
            $timesheetList = $timesheet->getTimesheetsByEmployeeId(['id' => $item->id, 'from' => $condition['from'], 'to' => $condition['to']]);
            $lateList = 0;
            $earlyList = 0;
            $presentList = 0;
            $total = 0;
            $workDayList = explode('|', $item->working_day);

            foreach ($workDayList as $day) {
                if ((int)$day >= 1 && (int)$day <= 7) {
                    $total += $totalWeekDay[(int)$day - 1];
                }
                foreach ($timesheetList as $timesheetItem) {
                    if (Carbon::parse($timesheetItem->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                        $start_time = new Carbon($timesheetItem->start_time);
                        $end_time = new Carbon($timesheetItem->end_time);
                        if ($timesheetItem->check_in < $timesheetItem->start_time  && $timesheetItem->check_out > $timesheetItem->end_time) {
                            $presentList++;
                        }
                        else if ($timesheetItem->check_in >= $timesheetItem->start_time && $timesheetItem->check_in < $start_time->addMinute(120)->toTimeString()) {
                            if ($timesheetItem->check_out > $timesheetItem->end_time) {
                                $lateList++;
                            }
                        }
                        else if ($timesheetItem->check_out > $end_time->subMinute(120)->toTimeString() && $timesheetItem->check_out <= $timesheetItem->end_time) {
                            if ($timesheetItem->check_in < $timesheetItem->start_time) {
                                $earlyList++;
                            }
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
                'off' => $total - $presentList - $lateList - $earlyList - $isToday,
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
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];

        // $notification = [];
        $page = 'timesheet';
        return view('admin.timesheet', compact('notification', 'profile', 'page', 'office', 'employeesList', 'page', 'pagination', 'condition', 'list','waitConfirm'));
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

    public function detail(Request $request, $id)
    {
        $employees = new EmployeesModel();
        $timesheet = new TimesheetsModel();

        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $info = $employees->getEmployees(['id' => $id]);

        $condition = [
            'from' => $request->get('from') != null && $request->get('from') <= date('Y-m-d') ? $request->input('from') : date('Y-m-01'),
            'to' => $request->get('to') != null && $request->get('to') <= date('Y-m-d') ? $request->input('to') : date('Y-m-d'),
            'today' => date('Y-m-d'),
        ];
        $WEEKDAY = ['Sunday', 'Monday', 'Tuesday', 'Wesnesday', 'Thursday', 'Friday', 'Saturday'];
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];
        $from = Carbon::createFromFormat('Y-m-d', $condition['from']);
        $to = Carbon::createFromFormat('Y-m-d', $condition['to']);
        $timesheetList = $timesheet->getTimesheetsByEmployeeId(['id' => $info[0]->id, 'from' => $condition['from'], 'to' => $condition['to']]);

        $arrTimesheetDetail = [];
        $isToday = 0;
        while (1) {
            if ($from > $to) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            $arrTimesheetDetail[$from->toDateString()] = [
                'weekday' => $WEEKDAY[$from->dayOfWeek],
                'check_in' => '-',
                'check_out' => '-',
                'status' => $from->toDateString() == $condition['today'] ? 'Updating...' : 'Off',
                'day_off' => !str_contains($info[0]->working_day, ($from->dayOfWeek + 1))
            ];
            if($from->toDateString() == $condition['today']) $isToday = 1;
            $from = $from->addDays(1);
        }
        $lateList = 0;
        $earlyList = 0;
        $presentList = 0;
        $total = 0;
        $workDayList = explode('|', $info[0]->working_day);

        foreach ($workDayList as $day) {
            if ((int)$day >= 1 && (int)$day <= 7) {
                $total += $totalWeekDay[(int)$day - 1];
            }
            foreach ($timesheetList as $timesheetItem) {
                if (Carbon::parse($timesheetItem->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                    $start_time = new Carbon($timesheetItem->start_time);
                    $end_time = new Carbon($timesheetItem->end_time);
                    if ($timesheetItem->check_in < $timesheetItem->start_time  && $timesheetItem->check_out > $timesheetItem->end_time) {
                        $arrTimesheetDetail[$timesheetItem->date]['status'] = 'OK';
                        $presentList++;
                    }
                    else if ($timesheetItem->check_in >= $timesheetItem->start_time && $timesheetItem->check_in < $start_time->addMinute(120)->toTimeString()) {
                        if ($timesheetItem->check_out > $timesheetItem->end_time) {
                            $lateList++;
                            $arrTimesheetDetail[$timesheetItem->date]['status'] = 'Late';
                        }
                    }
                    else if ($timesheetItem->check_out > $end_time->subMinute(120)->toTimeString() && $timesheetItem->check_out <= $timesheetItem->end_time) {
                        if ($timesheetItem->check_in < $timesheetItem->start_time) {
                            $earlyList++;
                            $arrTimesheetDetail[$timesheetItem->date]['status'] = 'Early';
                        }
                    }
                }

                $arrTimesheetDetail[$timesheetItem->date]['check_in'] = $timesheetItem->check_in;
                $arrTimesheetDetail[$timesheetItem->date]['check_out'] = $timesheetItem->check_out;
            }
        }

        $overview = [
            'present' => $presentList,
            'late' => $lateList,
            'early' => $earlyList,
            'first_name' => $info[0]->first_name,
            'last_name' => $info[0]->last_name,
            'id' => $info[0]->id,
            'office_name' => $info[0]->office_name,
            'department' => $info[0]->department,
            'working_day' => $info[0]->working_day,
            'total' => $total,
            'off' => $total - $presentList - $lateList - $earlyList - $isToday,
        ];
        // dd($timesheetList);
        $notification = [];
        $page = 'timesheet';
        return view('admin.detail-timesheet', compact('notification', 'waitConfirm', 'condition', 'arrTimesheetDetail', 'overview', 'profile', 'page'));
    }

    public function attendance(Request $request, $id)
    {
        $employees = new EmployeesModel();
        $timesheet = new TimesheetsModel();
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);

        $condition = [
            'employee_id' => $id,
            'from' => $request->get('date') == null || $request->get('date') > date('Y-m-d') ? date('Y:m:d') : $request->get('date'),
            'to' => $request->get('date') == null || $request->get('date') > date('Y-m-d') ? date('Y:m:d') : $request->get('date'),
            'today' => date('Y-m-d')
        ];
        $notification = [];
        $attendanceList = $timesheet->getAttendances($condition);
        // dd($attendanceList);
        $page = 'timesheet';
        return view('admin.timesheet-detail-attendance', compact('notification', 'condition', 'waitConfirm', 'profile', 'attendanceList', 'page'));
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
            'from' => $request->get('from') != null && $request->get('from') <= date('Y-m-d') ? $request->input('from') : date('Y-m-01'),
            'to' => $request->get('to') != null && $request->get('to') <= date('Y-m-d') ? $request->input('to') : date('Y-m-d'),
            'today' => date('Y-m-d'),
        ];
        // dd($list,$condition);
        $employeesList = $employees->pagination($condition, $request->page, $perPage);
        // dd($employeesList);
        $list = [];
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];
        $from = Carbon::createFromFormat('Y-m-d', $condition['from']);
        $to = Carbon::createFromFormat('Y-m-d', $condition['to']);
        $isToday = 0;
        while (1) {
            if (!($from <= $to)) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            if($from->toDateString() == $condition['today']) $isToday = 1;
            $from = $from->addDays(1);
        }

        foreach ($employeesList as $item) {
            // dd(gettype($item));
            $timesheetList = $timesheet->getTimesheetsByEmployeeId(['id' => $item->id, 'from' => $condition['from'], 'to' => $condition['to']]);
            $lateList = 0;
            $earlyList = 0;
            $presentList = 0;
            $total = 0;
            $workDayList = explode('|', $item->working_day);

            foreach ($workDayList as $day) {
                if ((int)$day >= 1 && (int)$day <= 7) {
                    $total += $totalWeekDay[(int)$day - 1];
                }
                foreach ($timesheetList as $timesheetItem) {
                    if (Carbon::parse($timesheetItem->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                        $start_time = new Carbon($timesheetItem->start_time);
                        $end_time = new Carbon($timesheetItem->end_time);
                        if ($timesheetItem->check_in < $timesheetItem->start_time  && $timesheetItem->check_out > $timesheetItem->end_time) {
                            $presentList++;
                        }
                        else if ($timesheetItem->check_in >= $timesheetItem->start_time && $timesheetItem->check_in < $start_time->addMinute(120)->toTimeString()) {
                            if ($timesheetItem->check_out > $timesheetItem->end_time) {
                                $lateList++;
                            }
                        }
                        else if ($timesheetItem->check_out > $end_time->subMinute(120)->toTimeString() && $timesheetItem->check_out <= $timesheetItem->end_time) {
                            if ($timesheetItem->check_in < $timesheetItem->start_time) {
                                $earlyList++;
                            }
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
                'off' => $total - $presentList - $lateList - $earlyList - $isToday,
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
        $returnHTML = view('admin.pagination.timesheet', compact('list', 'pagination', 'notification', 'page', 'office', 'employeesList', 'condition',))->render();
        return response()->json($returnHTML);
    }

    public function timeSheet($list) {

        return 0;//$arr;
    }
}
