<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeesModel;
use App\Models\OfficesModel;
use App\Models\TimesheetsModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $report = new ReportsModel();
        $employees = new EmployeesModel();
        $office = new OfficesModel();
        $timesheet = new TimesheetsModel();
        $notification = [];
        $page = 'report';
        // $list = $report->selectAllReports()->get();
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $office = $office->getOffices([]);

        $perPage = $request->show == null ? 50 : $request->show;
        $condition = [
            'status' => $request->input('status') == null ? 0 : $request->input('status'),
            'sort' => 1,
            'from' => $request->get('from') != null && $request->get('from') <= date('Y-m-d') ? $request->input('from') : date('Y-m-01'),
            'to' => $request->get('to') != null && $request->get('to') <= date('Y-m-d') ? $request->input('to') : date('Y-m-d'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];

        $list = $report->pagination($condition, $request->page, $perPage);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];

        return view('admin.report', compact('notification', 'office', 'profile', 'page', 'list', 'request','condition', 'pagination', 'waitConfirm'));
    }

    public function pagination(Request $request) {
        $report = new ReportsModel();

        $perPage = $request->show == null ? 50 : $request->show;
        $condition = [
            'status' => $request->input('status') == null ? 0 : $request->input('status'),
            'sort' => 1,
            'from' => $request->get('from') != null && $request->get('from') <= date('Y-m-d') ? $request->input('from') : date('Y-m-01'),
            'to' => $request->get('to') != null && $request->get('to') <= date('Y-m-d') ? $request->input('to') : date('Y-m-d'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];

        $list = $report->pagination($condition, $request->page, $perPage);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        $returnHTML = view('admin.pagination.report', compact('list', 'pagination', 'condition'))->render();
        return response()->json($returnHTML);
    }
}
