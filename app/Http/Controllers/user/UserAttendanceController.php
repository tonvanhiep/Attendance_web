<?php

namespace App\Http\Controllers\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    //
    public function index(Request $request)
    {
        $timesheet = new TimesheetsModel();
        $ex = new EmployeesModel();
        $titlePage = 'Attendance';
        $user = EmployeesModel::find(Auth::user()->employee_id);
        $list_timesheets = TimesheetsModel::where('employee_id', Auth::user()->employee_id)->get();

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
        ];
        $list = $timesheet->getTimesheetsforUser();


        $arr = [];
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];

        $lateList = 0;
        $earlyList = 0;
        $offList = 0;
        $total = 0;

        $daylist = explode('|', $user->working_day);
        // foreach ($daylist as $day) {
        //     if ((int)$day >= 1 && (int)$day <= 7) {
        //         $total += $totalWeekDay[(int)$day - 1];
        //         dd((int)$day,$totalWeekDay[(int)$day]);
        //     }
        // }

        // dd($list, $list_timesheets, $user->working_day,$total,$daylist,$totalWeekDay);

        return view('user.attendance', compact('titlePage', 'user', 'list', 'condition', 'count'));
    }

    public function pagination(Request $request)
    {
        $timesheet = new TimesheetsModel();

        $search = $request->input('search');
        $perPage = $request->show == null ? 50 : $request->show;

        $condition = [
            'status' => 1,
            'sort' => 1,
            'search' => $search,
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
        $list = $timesheet->pagination($condition, $request->page, $perPage);

        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];

        $returnHTML = view('user.pagination.attendance', compact('list', 'pagination'))->render();
        return response()->json($returnHTML);
    }
}
