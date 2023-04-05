<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //

    public function index()
    {
        $account = AccountsModel::where('employee_id', Auth::user()->employee_id)->first();
        $user = EmployeesModel::find(Auth::user()->employee_id);
        $titlePage = 'Profile';
        return view('user.profile', compact('titlePage', 'user', 'account'));
    }
}
