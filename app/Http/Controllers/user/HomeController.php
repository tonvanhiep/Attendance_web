<?php

namespace App\Http\Controllers\user;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $titlePage = 'Home';
        $user = EmployeesModel::find(Auth::user()->employee_id);
        $timesheet = new TimesheetsModel();

        $condition = [
            'status' => 1,
            'sort' => 1,
            'from' => $request->input('from') == null ? date('Y-m-01') : $request->input('from'),
            'to' => $request->input('to') == null ? date('Y-m-d') : $request->input('to'),
            'today' => date('Y-m-d'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $perPage = $request->show == null ? 50 : $request->show;

        $list_timesheets = $timesheet->getTimesheetsByEmployeeId(['id' => Auth::user()->employee_id, 'from' => $condition['from'], 'to' => $condition['to']]);
        // $list_timesheets = $timesheet->pagination($condition, $request->page, $perPage);
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

        // dd($totalWeekDay);

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
        // dd($list_timesheets->isEmpty());
        $list = [
            'present' => $presentList,
            'late' => $lateList,
            'early' => $earlyList,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'id' => $user->id,
            'office_name' => $user->office_name,
            'department' => $user->department,
            'working_day' => $user->working_day,
            'total' => $total,
            'off' => $total - count($list_timesheets),
        ];
        // foreach ($list_timesheets as $item) {
        //     if ($item->check_in > $item->start_time) {
        //         $lateList++;
        //     }
        //     if ($item->check_out < $item->end_time) {
        //         $earlyList++;
        //     }

        //     $list = [
        //         'late' => $lateList,
        //         'early' => $earlyList,
        //         'first_name' => $item->first_name,
        //         'last_name' => $item->last_name,
        //         'id' => $item->id,
        //         'office_name' => $item->office_name,
        //         'department' => $item->department,
        //         'working_day' => $item->working_day,
        //         'total' => $total,
        //         'off' => $total - count($list_timesheets),
        //     ];
        // }
        // dd($list, $list_timesheets);
        return view('user.home', compact('request', 'titlePage', 'user', 'condition', 'list',));
    }
}
