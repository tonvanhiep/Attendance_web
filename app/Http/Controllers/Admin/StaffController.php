<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateImageRecognition;
use Illuminate\Support\Str;
use App\Models\NoticesModel;
use App\Models\OfficesModel;
use Illuminate\Http\Request;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Exports\StaffExportCsv;
use App\Models\TimesheetsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\FaceEmployeeImagesModel;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelIgnition\Http\Requests\UpdateConfigRequest;

class StaffController extends Controller
{

    public function updatedescription()
    {
        $face = new FaceEmployeeImagesModel();
        $list = $face->getImages();
        // dd($list);
        return view('admin.staff.update', compact('list'));
    }

    public function pupdatedescription(Request $request)
    {
        $data = $request->data;

        foreach ($data as $key => $value) {
            $face = FaceEmployeeImagesModel::find($value['id']);
            $face->update([
                'description' => $value['descrip'],
                "updated_at" => now(),
                "updated_user" => Auth::user()->employee_id
            ]);
        }
        return response()->json(['message' => 'Update successfull']);
    }

    public function index(Request $request)
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();
        $office = new OfficesModel();
        $timesheet = new TimesheetsModel();

        $perPage = $request->show == null ? 50 : $request->show;

        $condition = [
            'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'search' => $request->input('search'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $list = $employees->pagination($condition, $request->page, $perPage);

        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];

        $notification = $notification->getNotifications([]);
        $office = $office->getOffices([]);
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $page = 'staff';
        return view('admin.staff', compact('notification', 'profile', 'list', 'page', 'pagination', 'office', 'condition', 'waitConfirm'));
    }

    public function exportCsv(Request $request)
    {
        $csv = new StaffExportCsv($request);
        return Excel::download($csv, 'stafflist' . date("Ymd-His") . '.csv');
    }

    public function exportPdf(Request $request)
    {
        $employees = new EmployeesModel();

        $condition = [
            'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'search' => $request->input('search'),
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $list = $employees->getEmployees($condition);

        $pdf = PDF::loadView('admin.templates.staffpdf',  compact('list'))->setPaper('a4', 'landscape');
        return $pdf->download('stafflist' . date("Ymd-His") . '.pdf');
    }

    public function pagination(Request $request)
    {
        $employees = new EmployeesModel();

        $search = $request->input('search');
        $perPage = $request->show == null ? 50 : $request->show;

        $condition = [
            'status' => $request->input('status') == null ? [0, 1, 2] : $request->input('status'),
            'sort' => $request->input('sort') == null ? 1 : $request->input('sort'),
            'search' => $search,
            'office' => $request->input('office'),
            'depart' => $request->input('depart'),
        ];
        $list = $employees->pagination($condition, $request->page, $perPage);

        $pagination = [
            'perPage' => $list->perPage(),
            'lastPage' => $list->lastPage(),
            'currentPage' => $list->currentPage()
        ];
        $returnHTML = view('admin.pagination.staff', compact('list', 'pagination'))->render();
        return response()->json($returnHTML);
    }

    public function create()
    {
        $notification = new NoticesModel();
        $employees = new EmployeesModel();
        $timesheet = new TimesheetsModel();
        $office = new OfficesModel();
        $notification = $notification->getNotifications([]);
        $page = 'staff';
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $office = $office->getOffices([]);

        return view('admin.staff.add', compact('profile', 'office', 'waitConfirm', 'notification', 'page'));
    }

    public function store(Request $request)
    {
        $this->saveAvatar($request);

        $staff =  EmployeesModel::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_day' => $request->birth_day,
            'gender' => $request->gender,
            'address' => $request->address,
            'numberphone' => $request->numberphone,
            'department' => $request->department,
            'position' => $request->position,
            'avatar' =>  isset($image) ? 'storage/avatar/' . $image : randomAvatarUrl(rand(1, 20)),
            'working_day' => implode('|', $request->working_day),
            'status' => 1,
            'salary' => $request->salary,
            'office_id' => $request->office_id,
            'join_day' => $request->join_day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        AccountsModel::create([
            'user_name' => $request->name,
            'fl_admin' => $request->fl_admin,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'employee_id' => $staff->id,
        ]);

        $this->saveImageRecognition($request, $staff->id);

        return redirect()->route('admin.staff.list')->with('success', 'Create successfully');
    }

    public function edit($id)
    {
        $notification = new NoticesModel();
        $notification = $notification->getNotifications([]);
        $employees = new EmployeesModel();
        $timesheet = new TimesheetsModel();
        $office = new OfficesModel();
        $face = new FaceEmployeeImagesModel();
        $staff = EmployeesModel::find($id);

        if ($staff == null) return redirect()->route('admin.staff.list');
        $employee_id = $staff->id;
        $account =  AccountsModel::where('employee_id', $employee_id)->first();
        $profile = $employees->getEmployees(['id' => Auth::user()->employee_id])[0];
        $waitConfirm = $timesheet->getCountAttendanceWithStatus(['status' => 2]);
        $office = $office->getOffices([]);
        $list = $face->getImages(['id' => $employee_id]);
        $page = 'staff';

        return view('admin.staff.edit', compact('staff', 'list', 'office', 'profile', 'notification', 'account', 'page', 'waitConfirm'));
    }

    public function update(Request $request, $id)
    {
        //dd($request, $request->has('arr_detections'), $request->arr_detections[0]);

        $this->saveAvatar($request);

        $staff = EmployeesModel::find($id);
        $employee_id = $staff->id;
        $account =  AccountsModel::where('employee_id', $employee_id)->first();

        $staff->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_day' => $request->birth_day,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone_number' => $request->numberphone,
            'department' => $request->department,
            'position' => $request->position,
            'avatar' => isset($image) ? 'storage/avatar/' . $image : $staff->avatar,
            'salary' => $request->salary,
            'office_id' => $request->office_id,
            'join_day' => $request->join_day,
            'left_day' => $request->left_day,
            'working_day' => implode('|', $request->working_day),
            'status' => $request->status,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'updated_at' => now(),
            'updated_user' => Auth::user()->employee_id,
        ]);

        $data = [
            'user_name' => $request->name,
            'fl_admin' => $request->fl_admin,
            'email' => $request->email,
            'updated_at' => now(),
            'updated_user' => Auth::user()->employee_id,
        ];

        if ($request->password) {
            $this->validate($request, [
                'password' => 'required|min:5|max:32',
                'confirm' => 'same:password'
            ]);
            $data['password'] = bcrypt($request->password);
        };

        if ($account == null) {
            $data['employee_id'] = $employee_id;
            $data['created_at'] = now();
            $data['created_user'] = Auth::user()->employee_id;
            AccountsModel::create($data);
        } else {
            $account =  AccountsModel::where('employee_id', $employee_id);
            $account->update($data);
        }

        $this->saveImageRecognition($request, $employee_id);

        return redirect()->route('admin.staff.edit', ['id' => $id])->with('success', 'Update successfully');
    }

    public function delete($id)
    {
        $notification = new NoticesModel();
        $notification = $notification->getNotifications([]);

        $staff = EmployeesModel::find($id);

        $staff->delete();

        return redirect()->route('admin.staff.list', compact('staff', 'notification',))->with('success', 'Delete sucessfully');
    }

    public function saveImageRecognition($request, $employee_id)
    {
        // dd($request->file('face'));
        if (!$request->hasFile('face') || !$request->has('arr_detections')) return;

        $facesFile = $request->file('face');
        foreach ($facesFile as $key => $faceFile) {
            $name_face_file = $faceFile->getClientOriginalName();
            $extension_face_file = $faceFile->getClientOriginalExtension();
            if (strcasecmp($extension_face_file, 'png') === 0 || strcasecmp($extension_face_file, 'jpg') === 0 || strcasecmp($extension_face_file, 'jpeg') === 0) {
                $image_face = Str::random(length: 5) . "_" . $name_face_file;  //tránh lưu trùng tên file
                while (file_exists("storage/face-recognition/" . $image_face)) {
                    $image_face = Str::random(length: 5) . "_" . $name_face_file;
                }
                $faceFile->move('storage/face-recognition/', $image_face);
                FaceEmployeeImagesModel::create([
                    'employee_id' => $employee_id,
                    'image_url' => 'storage/face-recognition/' . $image_face,
                    'description' => $request->arr_detections[$key],
                    'status' => 1
                ]);
            }
        }

        broadcast(new UpdateImageRecognition($employee_id))->toOthers();
    }

    public function saveAvatar($request)
    {
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $name_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            if (strcasecmp($extension, 'png') === 0 || strcasecmp($extension, 'jpg') === 0 || strcasecmp($extension, 'jpeg') === 0) {
                $image = Str::random(length: 5) . "_" . $name_file;  //tránh lưu trùng tên file
                while (file_exists("storage/avatar/" . $image)) {
                    $image = Str::random(length: 5) . "_" . $name_file;
                }
                $file->move('storage/avatar/', $image);
            }
        }
    }
}
