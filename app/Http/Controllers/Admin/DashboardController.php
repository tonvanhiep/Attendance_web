<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeesModel;
use App\Models\NoticesModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();

        $list = $employees->pagination([
            'status' => [1, 2],
            'sort' => 1,
        ]);
        //dd($list);
        $pagination = [
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
        $page = 'dashboard';
        return view('admin.dashboard', compact('list', 'notification', 'info', 'page', 'pagination'));
    }

    public function pagination (Request $request)
    {
        $employees = new EmployeesModel();

        $list = $employees->pagination([
            'status' => [1, 2],
            'sort' => 1,
        ], $request->page);

        $pagination = [
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
