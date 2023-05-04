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
        $ex = new EmployeesModel();
        $titlePage = 'Attendance';
        $id = Auth::user()->employee_id;
        $user = EmployeesModel::find($id);

        $count = 1;
        $perPage = $request->show == null ? 10 : $request->show;
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

        return view('user.attendance', compact('titlePage', 'user', 'condition', 'count', 'dayOfWeekArr', 'request','pagination'));
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
