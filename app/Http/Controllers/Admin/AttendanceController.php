<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceCsv;
use App\Http\Controllers\Controller;
use App\Models\EmployeesModel;
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
            'status' => $request->input('status') == null ? 1 : $request->input('status'),
            'sort' => 1,
            'from' => $request->input('from') == null ? date('Y-m-d') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
            'office' => $request->input('office'),
            'today' => date('Y-m-d')
        ];
        $list = $timesheet-> paginationAttenndance($condition, $request->page, $perPage);
        // dd($list);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        $waitConfirm = $timesheet->getCountAttendanceWaitingForConfirm([]);
        $page = 'attendance';
        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        return view('admin.attendance', compact('notification', 'list', 'office', 'page', 'pagination', 'condition', 'waitConfirm'));
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
            'status' => $request->input('status') == null ? 1 : $request->input('status'),
            'sort' => 1,
            'from' => $request->input('from') == null ? date('Y-m-d') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
            'office' => $request->input('office'),
            'today' => date('Y-m-d')
        ];
        $list = $timesheet->paginationAttenndance($condition, $request->page, $perPage);

        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];

        $returnHTML = view('admin.pagination.attendance', compact('list', 'pagination', 'condition'))->render();
        return response()->json($returnHTML);
    }

    public function detail($id = null, Request $request)
    {
        if($id == null) return redirect()->route('admin.attendance.list');

        $timesheet = new TimesheetsModel();
        $notification = new NoticesModel();
        $office = new OfficesModel();
        $employee = new EmployeesModel();

        $detail = $timesheet->getDetailInfoAttendance(['id' => $id]);
        // dd($detail);
        if($detail == []) return redirect()->route('admin.attendance.list');

        $page = 'attendance';
        $notification = $notification->getNotifications([]);

        return view('admin.detail-attendance', compact('detail', 'page', 'notification'));
    }

    public function updateStatus(Request $request)
    {
        $timesheet = new TimesheetsModel();
        $timesheet->updateStatus([
            'id' => $request->id,
            'status' => $request->status
        ]);

        return response()-> json(['message' => 'success', 'code' => 200]);
    }
}
