<?php

namespace App\Http\Controllers\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TimekeepersModel;
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $timekeeper = new TimekeepersModel();
        $timesheet = new TimesheetsModel();
        $employees = new EmployeesModel();
        $titlePage = 'Attendance';
        $id = Auth::user()->employee_id;
        $user = EmployeesModel::find($id);

        $count = 1;
        $perPage = $request->show == null ? 10 : $request->show;
        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->get('from') == null || $request->get('from') > date('Y-m-d') ? date('Y-m-01') : $request->input('from'),
            'to' => $request->get('to') == null || $request->get('to') > date('Y-m-d') ? date('Y-m-d') : $request->input('to'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
            'id' => $id,
        ];

        $info = $employees->getEmployees(['id' => $id]);
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
                    $check_in = new Carbon($timesheetItem->check_in);
                    $check_out = new Carbon($timesheetItem->check_out);
                    if ($timesheetItem->check_in < $timesheetItem->start_time  && $timesheetItem->check_out > $timesheetItem->end_time) {
                        $arrTimesheetDetail[$timesheetItem->date]['status'] = 'OK';
                        $presentList++;
                    }
                    else if ($timesheetItem->check_in >= $timesheetItem->start_time && $timesheetItem->check_in < $check_in->addMinute(120)->toTimeString()) {
                        if ($timesheetItem->check_out > $timesheetItem->end_time) {
                            $lateList++;
                            $arrTimesheetDetail[$timesheetItem->date]['status'] = 'Late';
                        }
                    }
                    else if ($timesheetItem->check_out > $check_out->subMinute(120)->toTimeString() && $timesheetItem->check_out <= $timesheetItem->end_time) {
                        $earlyList++;
                        $arrTimesheetDetail[$timesheetItem->date]['status'] = 'Early';
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
        $pagination = [
            'perPage' => 0,
            'lastPage' => 0,
            'currentPage' => 0
        ];

        return view('user.attendance', compact('titlePage', 'arrTimesheetDetail', 'overview', 'user', 'condition', 'count', 'request','pagination'));
    }

    public function search(Request $request)
    {
        $timesheet = new TimesheetsModel();

        $titlePage = 'Attendance';
        $user = EmployeesModel::find(Auth::user()->employee_id);

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $dayOfWeekArr = [];

        $list_timesheets = $timesheet->getTimesheetsforUser()->where('date', '>=', $fromDate)->where('date', '<=', $toDate);

        $weekMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        foreach ($list_timesheets as $item) {
            $dayOfTheWeek = Carbon::parse($item->date)->dayOfWeek;
            $weekday = $weekMap[$dayOfTheWeek];
            $subArr = [
                'dayOfWeek' => $weekday,
                "timekeeper_name" => $item->timekeeper_name,
                "office_name" => $item->office_name,
                "face_image" => $item->face_image,
                "date" => $item->date,
                "check_in" => $item->check_in,
                "check_out" => $item->check_out,
            ];
            array_push($dayOfWeekArr, $subArr);
        }
        return view('user.attendance', compact('dayOfWeekArr', 'titlePage', 'user'));
    }

    public function pagination(Request $request)
    {
        $timesheet = new TimesheetsModel();

        $search = $request->input('search');
        $perPage = $request->show == null ? 50 : $request->show;

        $timekeeper = new TimekeepersModel();
        $timesheet = new TimesheetsModel();
        $ex = new EmployeesModel();
        $titlePage = 'Attendance';
        $id = Auth::user()->employee_id;
        $user = EmployeesModel::find($id);

        $fromDate = $request->input('fromDate') == null ? date('Y-m-01') : $request->input('fromDate');
        $toDate = $request->input('to') == null ? date('Y-m-d') : $request->input('to');
        $now = Carbon::now()->format('Y-m-d');

        $count = 1;
        $perPage = $request->show == null ? 50 : $request->show;
        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from') == null ? date('Y-m-01') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
            'id' => $id,
        ];

        $list_attendances = $timesheet->paginationTimesheetsforUser($condition, $request->page, $perPage);

        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0]; // [sun,mon,tue,wed,thur,fri,sat]
        $dayOfWeekArr = [];
        $weekMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        foreach ($list_attendances as $item) {

            $dayOfTheWeek = Carbon::parse($item->date)->dayOfWeek;
            $weekday = $weekMap[$dayOfTheWeek];
            $subArr = [
                'dayOfWeek' => $weekday,
                "timekeeper_name" => $item->timekeeper_name,
                "office_name" => $item->office_name,
                "face_image" => $item->face_image,
                "date" => $item->date,
                "check_in" => $item->check_in,
                "check_out" => $item->check_out,
            ];
            array_push($dayOfWeekArr, $subArr);
        }

        $pagination = [
            'perPage' => $list_attendances->perPage(),
            'lastPage' => $list_attendances->lastPage(),
            'currentPage' => $list_attendances->currentPage()
        ];

        $returnHTML = view('user.pagination.attendance', compact('pagination','titlePage', 'user', 'condition', 'count', 'dayOfWeekArr', 'request'))->render();
        return response()->json($returnHTML);
    }
}
