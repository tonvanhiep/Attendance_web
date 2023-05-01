<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $report = new ReportsModel();
        $timesheet = new TimesheetsModel();
        $employees = new EmployeesModel();

        $notification = [];
        $count = 0;
        $page = 'report';
        $list = $report->selectAllReports()->get();
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];

        return view('admin.report', compact('notification', 'profile', 'page', 'list', 'count','waitConfirm'));
    }
}
