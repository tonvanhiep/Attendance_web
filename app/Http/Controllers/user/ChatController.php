<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function index()
    {
        $employee = new EmployeesModel();
        $user = $employee->getEmployees(['id' => Auth::user()->employee_id])[0];
        $account = AccountsModel::where('employee_id', Auth::user()->employee_id)->first();
        $titlePage = 'Profile';
        return view('user.chat', compact('titlePage', 'user', 'account'));
    }
}
