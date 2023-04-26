<?php

namespace App\Http\Controllers\Admin;

use App\Models\NoticesModel;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();
        $timesheet = new TimesheetsModel();

        $list = $employees->pagination([
            'status' => [1, 2],
            'sort' => 1,
        ]);
        $perPage = $request->show == null ? 10 : $request->show;
        //dd($list);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        $notification = $notification->getNotifications([]);

        $info = [
            'employees' => $employees->getCountEmployees([
                'status' => [1, 2],
            ]),
            'male' => $employees->getCountEmployees([
                'gender' => 1,
                'status' => [1, 2],
            ]),
            'female' => $employees->getCountEmployees([
                'gender' => 0,
                'status' => [1, 2],
            ]),
            'active' => $employees->getCountEmployees(['status' => 1]),
        ];

        // dd($info);
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $page = 'dashboard';
        return view('admin.dashboard', compact('list', 'notification', 'info', 'page', 'pagination','request','waitConfirm'));
    }

    public function pagination (Request $request)
    {
        $employees = new EmployeesModel();
        $perPage = $request->show == null ? 10 : $request->show;

        $list = $employees->pagination([
            'status' => [1, 2],
            'sort' => 1,
        ], $request->page);

        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        $returnHTML = view('admin.pagination.dashboard', compact('list', 'pagination'))->render();
        return response()->json($returnHTML);
    }

    //Test
    public function ApiGetUser()
    {
        $employees = new EmployeesModel();

        $list = $employees->pagination([
            'status' => [1, 2],
            'sort' => 1,
        ]);

        return response()->json(['data' => $list, 'code' => 200]);
    }
}
