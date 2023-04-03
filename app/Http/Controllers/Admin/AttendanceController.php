<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceCsv;
use App\Http\Controllers\Controller;
use App\Models\NoticesModel;
use App\Models\OfficesModel;
use App\Models\TimesheetsModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Cache\RateLimiting\Limit;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $timesheet = new TimesheetsModel();
        $notification = new NoticesModel();
        $office = new OfficesModel();

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

        $page = 'attendance';
        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        return view('admin.attendance', compact('notification', 'list', 'office', 'page', 'pagination', 'condition'));

    }

    public function exportCsv(Request $request)
    {
        $csv = new AttendanceCsv($request);
        return Excel::download($csv, 'timesheet'.date("Ymd-His").'.csv');
    }

    public function exportPdf(Request $request)
    {
        $attendance = new TimesheetsModel();

        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'office' => $request->input('office')
        ];
        $list = $attendance->getAttendances($condition);

        $pdf = PDF::loadView('admin.templates.attendancepdf',  compact('list'))->setPaper('a4', 'landscape');
    	return $pdf->download('attendancelist'.date("Ymd-His").'.pdf');
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

        $returnHTML = view('admin.pagination.attendance', compact('list', 'pagination'))->render();
        return response()->json($returnHTML);
    }
}
