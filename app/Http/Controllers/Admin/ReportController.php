<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TimesheetsModel;

class ReportController extends Controller
{
    public function index()
    {
        $report = new ReportsModel();
        $timesheet = new TimesheetsModel();

        $notification = [];
        $count = 0;
        $page = 'report';
        $list = $report->selectAllReports()->get();
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);

        return view('admin.report', compact('notification', 'page', 'list', 'count','waitConfirm'));
    }
}
