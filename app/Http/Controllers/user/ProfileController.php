<?php

namespace App\Http\Controllers\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //

    public function index()
    {
        $employee = new EmployeesModel();
        $user = $employee->getEmployees(['id' => Auth::user()->employee_id])[0];
        $account = AccountsModel::where('employee_id', Auth::user()->employee_id)->first();
        $titlePage = 'Profile';
        return view('user.profile', compact('titlePage', 'user', 'account'));
    }

    public function resetPass(Request $request)
    {
        $this->validate($request, [
            'old-pass' => 'required|min:5|max:32',
            'new-pass' => 'required|min:5|max:32',
            're-new-pass' => 'required|same:new-pass'
        ]);
        if(Hash::check($request->input('old-pass'), Auth::user()->password)) {
            $data = [
                'password' => bcrypt($request->input('new-pass')),
                'updated_at' => now(),
                'updated_user' => Auth::user()->employee_id
            ];
            AccountsModel::where('employee_id', Auth::user()->employee_id)->update($data);
            return redirect()->route('user.profile')->with('success', 'Update successfully');
        }
        return redirect()->route('user.profile')->with('error', 'Password incorrect');
    }
}
