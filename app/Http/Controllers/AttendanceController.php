<?php

namespace App\Http\Controllers;

use App\Events\Attendance;
use Illuminate\Http\Request;
use App\Models\EmployeesModel;
use App\Models\TimesheetsModel;
use Illuminate\Support\Facades\Auth;
use App\Models\FaceEmployeeImagesModel;
use Illuminate\Support\Facades\Response;

class AttendanceController extends Controller
{
    public function index()
    {
        $image = new FaceEmployeeImagesModel;
        $image = $image->getImages(['status' => 1]);
        //dd($image->toArray());
        $arr = [];
        foreach ($image as $key => $value) {
            $name = $value->id;
            if (isset($arr[$name])) {
                array_push($arr[$name], $value->image_url);
            }
            else {
                $arr[$name] = [];
                array_push($arr[$name], $value->image_url);
            }
        }
        return view('attendance.check-in', compact('arr'));
    }

    public function recognition()
    {
        $image = new FaceEmployeeImagesModel;
        $image = $image->getImages(['status' => 1]);
        //dd($image->toArray());
        $arr = [];
        foreach ($image as $key => $value) {
            $name = $value->id;
            if (isset($arr[$name])) {
                array_push($arr[$name], $value->image_url);
            }
            else {
                $arr[$name] = [];
                array_push($arr[$name], $value->image_url);
            }
        }
        return view('attendance.face-recognition', compact('arr'));
    }

    public function attendance(Request $request)
    {
        $employee = new EmployeesModel();
        $attendance = new TimesheetsModel();

        $employee = $employee->getEmployees(['id' => $request->id, 'status' => 1]);
        if (count($employee) == 0) return response()->json([
            'success' => false,
            'message'   =>  'Nguoi dung khong hop le.'
        ]);
        $folderPath = public_path('storage\image-checkin\\');
        $image_parts = explode(";base64,", $request->image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $file_name = $employee[0]->last_name . $employee[0]->first_name . time(). rand(0,10000) . '.' . $image_type;

        file_put_contents($folderPath . $file_name, $image_base64);
        $data = [
            'employee_id' => $employee[0]->id,
            'face_image' => $file_name,
            'status' => $request->identity == 'true' ? 1 : 2,
            'timekeeper_id' => 4 //$request->timekeeper_id
        ];
        $id_attendance = $attendance->saveAttendance($data);

        if ($request->identity == 'true') {
            $message = 'ID:' . $employee[0]->id . '| ' . $employee[0]->last_name . ' ' . $employee[0]->first_name . ' diem danh thanh cong!!!';
        } else {
            $message = 'ID:' . $employee[0]->id . '| ' . $employee[0]->last_name . ' ' . $employee[0]->first_name . ' da gui yeu cau diem danh.';
        }
        $data = [
            'success' => true,
            'message'   =>  $message
        ];

        broadcast(new Attendance($id_attendance))->toOthers();
        return response()->json($data);
    }

    //API
    public function ApiGetAttendance(Request $request)
    {
        // SELECT ts.timekeeper_id, ts.timekeeping_at, ts.face_image, ts.status, date(ts.timekeeping_at) AS 'date', MIN(time(ts.timekeeping_at)) AS 'check-in', MAX(time(ts.timekeeping_at)) AS 'check-out'
        // FROM `timesheets` as ts
        // WHERE ts.employee_id = 1 and ts.status = 1
        // GROUP BY date(ts.timekeeping_at);

        // SELECT epl.start_time, epl.end_time, epl.working_day, epl.join_day, epl.left_day
        // FROM `employees` as epl
        // WHERE epl.id = 1
        $employee = new EmployeesModel();
        $timesheets = new TimesheetsModel();

        $employee = $employee->getEmployees([
            'id' => Auth::user()->employee_id
        ]);
        $timesheets = $timesheets->getTimesheetsByEmployeeId([
            'id' => Auth::user()->employee_id,
            'from' => date('Y-m').'-1',
            'to' => date('Y-m-d'),
            'status' => 1
        ]);
        return response()->json([
            'message' => 'success',
            'data' => [
                'list' => $timesheets,
                'start_time' => $employee[0]->start_time,
                'end_time' => $employee[0]->end_time,
                'working_day' => $employee[0]->working_day,
                'join_day' => $employee[0]->join_day,
                'left_day' => $employee[0]->left_day
            ],
            'code' => 200
        ]);
    }
}
