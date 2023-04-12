<?php

namespace App\Http\Controllers\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
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
        $timesheet = new TimesheetsModel();


        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => date('Y-m-01'),
            'to' => date('Y-m-d'),
            'month' => date('m'),
        ];

        $list_timesheets = $timesheet->getTimesheetsByEmployeeId(['id' => Auth::user()->employee_id, 'from' => $condition['from'], 'to' => $condition['to']]);
        $totalWeekDay = [0, 0, 0, 0, 0, 0, 0];
        $from = Carbon::createFromFormat('Y-m-d', $condition['from']);
        $to = Carbon::createFromFormat('Y-m-d', $condition['to']);
        while (1) {
            if (!($from <= $to)) {
                break;
            }
            $totalWeekDay[$from->dayOfWeek]++;
            $from = $from->addDays(1);
        }

        $lateList = 0;
        $earlyList = 0;
        $presentList = 0;
        $offList = 0;
        $total = 0;
        $daylist = explode('|', $user->working_day);
        foreach ($daylist as $day) {
            if ((int)$day >= 1 && (int)$day <= 7) {
                $total += $totalWeekDay[(int)$day - 1];
            }
            foreach ($list_timesheets as $item) {
                if (Carbon::parse($item->timekeeping_at)->dayOfWeek == ((int)$day - 1)) {
                    $presentList++;
                    if ($item->check_in > $item->start_time) {
                        $lateList++;
                    }
                    if ($item->check_out < $item->end_time) {
                        $earlyList++;
                    }
                }
            }
        }
        $list = [
            'late' => $lateList,
            'early' => $earlyList,
            'present' => $presentList,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'id' => $user->id,
            'office_name' => $user->office_name,
            'department' => $user->department,
            'working_day' => $user->working_day,
            'total' => $total,
            'off' => $total - count($list_timesheets),
        ];

        return view('user.profile', compact('titlePage', 'user', 'account', 'list', 'list_timesheets', 'condition'));
    }
}
