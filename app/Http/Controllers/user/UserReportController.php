<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Http\Controllers\Controller;
use App\Models\ReportsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserReportController extends Controller
{
    //
    public function index() {
        $titlePage = 'Report';
        $user = EmployeesModel::find(Auth::user()->employee_id);

        $report = new ReportsModel();
        $list = $report->selectReportbyEmployeeId()->get();
        // dd($list);

        return view('user.report', compact('titlePage','user', 'list'));
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'comment' => 'required | string'
        ]);

        if(Auth::check()) {
            ReportsModel::create([
                'comment' => $request->comment_body,
                'employee_id' => Auth::user()->employee_id,
            ]);
            return redirect()->route('user.report.list')->with('success', 'Your report is successful! We will check later');
        }
        else {
            return redirect()->view('admin.login.home-login')->with('warning', 'Login first to comment');
        }
    }

    public function edit() {

    }

    public function delete() {
        
    }
}
