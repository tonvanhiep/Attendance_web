<?php

namespace App\Http\Controllers\Admin;

use App\Events\Attendance;
use App\Models\NoticesModel;
use App\Models\OfficesModel;
use Illuminate\Http\Request;
use App\Exports\AttendanceCsv;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $timesheet = new TimesheetsModel();
        $notification = new NoticesModel();
        $office = new OfficesModel();
        $employees = new EmployeesModel();

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
        // dd($list, $condition);
        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        // $waitConfirm = $timesheet->getCountAttendanceWaitingForConfirm([]);
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $page = 'attendance';
        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];

        return view('admin.attendance', compact('notification', 'profile', 'list', 'office', 'page', 'pagination', 'condition', 'waitConfirm'));
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
        $employees = new EmployeesModel();

        $detail = $timesheet->getDetailInfoAttendance(['id' => $id]);
        // dd($detail);
        if($detail == []) return redirect()->route('admin.attendance.list');

        $page = 'attendance';
        $notification = $notification->getNotifications([]);
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];

        return view('admin.detail-attendance', compact('detail', 'profile', 'page', 'notification','waitConfirm'));
    }

    public function updateStatus(Request $request)
    {
        $timesheet = new TimesheetsModel();
        $timesheet->updateStatus([
            'id' => $request->id,
            'status' => $request->status
        ]);
        broadcast(new Attendance($request->id))->toOthers();

        return response()-> json(['message' => 'success', 'code' => 200]);
    }
}
