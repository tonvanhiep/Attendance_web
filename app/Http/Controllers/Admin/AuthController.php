<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //WEB
    public function login()
    {
        return view('admin.login.login');
    }

    public function checkLogin(Request $request)
    {
        // dd($request->name);
        if (Auth::attempt(['email' => $request->name, 'password' => $request->password])) {
            if (Auth::user()->fl_admin == 1) {
                return redirect()->route('admin.dashboard')->with('success', 'Login successfully');
            }
            if (Auth::user()->fl_admin == 0) {
                return redirect()->route('user.index')->with('success', 'Login successfully');
            }
        }
        return redirect()->route('admin.auth.login')->with('error', 'Login failed');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.auth.login');
    }

    //API
    public function ApiCheckLogin(Request $request)
    {
        if (Auth::attempt(['email' => $request->name, 'password' => $request->password])) {
            $user = Auth::user();
            $info = new EmployeesModel();

            $info = $info->getEmployees(['id' => $user->employee_id]);
            $token = $user->createToken('face_recognition')->accessToken;
            return response()->json([
                'message' => 'Login successful',
                'code' => 200,
                'token' => $token,
                'email' => $user->email,
                'avatar' => $info[0]->avatar,
                'first_name' => $info[0]->first_name,
                'last_name' => $info[0]->last_name
            ]);
        }
        return response()->json(['message' => 'Login fail', 'code' => 401]);
    }

    public function ApiLogout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logout successfully', 'code' => 200]);
    }

    public function ApiGetInfo(Request $request)
    {
        $auth = Auth::user();
        $user = new EmployeesModel();

        $result = $user->getEmployees(['id' => $auth->employee_id]);

        return response()->json(['data' => $result, 'code' => 200]);
    }
}
