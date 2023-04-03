<?php

namespace App\Http\Controllers\user;

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
        $titlePage = 'Attendance';
        $user = EmployeesModel::find(Auth::user()->employee_id);
        $list_timesheets = TimesheetsModel::where('employee_id', Auth::user()->employee_id)->get();

        // dd($user,$list_timesheets);
        $count = 1;

        $perPage = $request->show == null ? 5 : $request->show;
        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
        $list = $user->pagination($condition, $request->page, $perPage);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];

        return view('user.attendance', compact('titlePage', 'user', 'pagination', 'list_timesheets', 'count'));
    }

    public function pagination(Request $request)
    {
        $timesheet = new TimesheetsModel();

        $perPage = $request->show == null ? 50 : $request->show;

        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'office' => $request->input('office')
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
