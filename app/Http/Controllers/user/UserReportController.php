<?php

namespace App\Http\Controllers\user;

use App\Events\Report;
use Carbon\Carbon;
use App\Models\ReportsModel;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $titlePage = 'Report';
        $id = Auth::user()->employee_id;
        $user = EmployeesModel::find($id);
        $report = new ReportsModel();


        $condition = [
            'from' => $request->get('from') == null || $request->get('from') > date('Y-m-d') ? date('Y-m-01') : $request->input('from'),
            'to' => $request->get('to') == null || $request->get('to') > date('Y-m-d') ? date('Y-m-d') : $request->input('to'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];

        $list = $report->getReportbyEmployeeId(['from' => $condition['from'], 'to' => $condition['to']]);
        // dd($list);

        return view('user.report', compact('titlePage', 'user', 'list', 'request', 'condition'));
    }

    public function store(Request $request)
    {
        $report = new ReportsModel();
        $validator = Validator::make($request->all(), [
            'comment' => 'required | string'
        ]);

        if (Auth::check()) {
            // $data = ReportsModel::create([
            //     'comment' => $request->comment_body,
            //     'employee_id' => Auth::user()->employee_id,
            //     'status' => 0,
            // ]);
            $data = [
                'comment' => $request->comment_body,
                'employee_id' => Auth::user()->employee_id,
                'status' => 0,
            ];

            $id_report = $report->saveReport($data);
            // dd($id_report);
            broadcast(new Report($id_report))->toOthers();
            return redirect()->route('user.report.list')->with('success', 'Your report is successful! We will check later');
        } else {
            return redirect()->view('admin.login.home-login')->with('warning', 'Login first to comment');
        }
    }

    public function edit()
    {
    }

    public function delete()
    {
    }
}
