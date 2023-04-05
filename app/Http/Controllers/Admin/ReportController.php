<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $report = new ReportsModel();

        $notification = [];
        $count = 0;
        $page = 'report';
        $list = $report->selectAllReports()->get();

        return view('admin.report', compact('notification', 'page', 'list', 'count'));
    }
}
