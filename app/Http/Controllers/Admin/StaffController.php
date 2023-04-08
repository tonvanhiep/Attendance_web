<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StaffExportCsv;
use App\Http\Controllers\Controller;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Models\FaceEmployeeImagesModel;
use App\Models\NoticesModel;
use App\Models\OfficesModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $employees = new EmployeesModel();
        $notification = new NoticesModel();
        $office = new OfficesModel();

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
        $page = 'staff';
        return view('admin.staff', compact('notification', 'list', 'page', 'pagination', 'office', 'condition'));
    }

    public function exportCsv(Request $request)
    {
        $csv = new StaffExportCsv($request);
        return Excel::download($csv, 'stafflist'.date("Ymd-His").'.csv');
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
    	return $pdf->download('stafflist'.date("Ymd-His").'.pdf');
    }

    public function pagination (Request $request)
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

    public function create() {
        $notification = new NoticesModel();
        $notification = $notification->getNotifications([]);
        $page = 'staff';
        return view('admin.staff.add', compact('notification', 'page'));
    }

    public function store(Request $request) {
        // dd($request);
        // $this->validate($request, [
        //     'name' => 'required|unique:accounts',
        //     'fl_admin' => 'required',
        //     'email' => 'required|unique:accounts|email',
        //     'password' => 'required|min:5|max:32',
        //     'confirm' => 'same:password',
        //     'first_name' => 'required',
        //     'last_name' => 'required',
        //     'birth_day' => 'required',
        //     'gender' => 'required',
        //     'address' => 'required',
        //     'numberphone' => 'required|unique:employees',
        //     'department' => 'required',
        //     'position' => 'required',
        //     'salary' => 'required',
        //     'office_id' => 'required',
        //     'join_day' => 'required',
        //     'left_day' => 'required',
        //     'image_url'=>'required',
        // ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $name_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            if(strcasecmp($extension, 'png') === 0 || strcasecmp($extension, 'jpg') === 0 || strcasecmp($extension, 'jpeg') === 0) {
                $image = Str::random(length: 5)."_".$name_file;  //tránh lưu trùng tên file
                while(file_exists("storage/avatar/".$image)) {
                    $image = Str::random(length: 5)."_".$name_file;
                }
                $file->move('storage/avatar/',$image);
            }
        }

        $staff =  EmployeesModel::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_day' => $request->birth_day,
            'gender' => $request->gender,
            'fl_admin' => $request->fl_admin,
            'address' => $request->address,
            'numberphone' => $request->numberphone,
            'department' => $request->department,
            'position' => $request->position,
            'avatar' => 'storage/avatar/' . $image,
            'working_day' => $request->working_day,
            'status' => $request->status,
            'salary' => $request->salary,
            'office_id' => $request->office_id,
            'join_day' => $request->join_day,
            'left_day' => $request->left_day,
        ]);

        AccountsModel::create([
            'name' => $request->name,
            'fl_admin' => $request->fl_admin,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'employee_id' => $staff->id,
        ]);

        if($request->hasFile('image_url')) {
            $facesFile = $request->file('image_url');
            // dd($facesFile);
            foreach($facesFile as $faceFile) {
                $name_face_file = $faceFile->getClientOriginalName();
                $extension_face_file = $faceFile->getClientOriginalExtension();
                if(strcasecmp($extension_face_file, 'png') === 0 || strcasecmp($extension_face_file, 'jpg') === 0 || strcasecmp($extension_face_file, 'jpeg') === 0) {
                    $image_face = Str::random(length: 5)."_".$name_face_file;  //tránh lưu trùng tên file
                    while(file_exists("storage/face-recognition/".$image_face)) {
                        $image_face = Str::random(length: 5)."_".$name_face_file;
                    }
                    $faceFile->move('storage/face-recognition/',$image_face);
                    // dd($image_face);
                    FaceEmployeeImagesModel::create([
                        'employee_id' => $staff->id,
                        'image_url' => 'storage/face-recognition/' . $image_face,
                        'status' => 1
                    ]);
                }
            }
        }

        return redirect()->route('admin.staff.list')->with('success', 'Create successfully');
    }

    public function edit($id) {
        $notification = new NoticesModel();
        $notification = $notification->getNotifications([]);

        $staff = EmployeesModel::find($id);

        $employee_id = $staff->id;
        $account =  AccountsModel::where('employee_id', $employee_id)->first();
        $page = 'staff';
        // dd($staff,$account);

        return view('admin.staff.edit', compact('staff', 'notification', 'account', 'page'));
    }

    public function update(Request $request, $id) {
        // dd($request);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $name_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            if(strcasecmp($extension, 'png') === 0 || strcasecmp($extension, 'jpg') === 0 || strcasecmp($extension, 'jpeg') === 0) {
                $image = Str::random(length: 5)."_".$name_file;  //tránh lưu trùng tên file
                while(file_exists("storage/avatar/".$image)) {
                    $name = Str::random(length: 5)."_".$name_file;
                }
                $file->move('storage/avatar/',$image);
            }
        }

        $staff = EmployeesModel::find($id);
        $employee_id = $staff->id;
        $account =  AccountsModel::where('employee_id', $employee_id);
        // dd($account);

        $staff->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_day' => $request->birth_day,
            'gender' => $request->gender,
            'address' => $request->address,
            'numberphone' => $request->numberphone,
            'department' => $request->department,
            'position' => $request->position,
            'avatar' => isset($image) ? 'storage/avatar/' . $image : $staff->avatar,
            'salary' => $request->salary,
            'office_id' => $request->office_id,
            'join_day' => $request->join_day,
            'left_day' => $request->left_day,
            'working_day' => $request->working_day,
            'status' => $request->status,
        ]);

        $data = [
            'name' => $request->name,
            'fl_admin' => $request->fl_admin,
            'email' => $request->email,
        ];

        if ($request->password) {
            $this->validate($request, [
                'password' => 'required|min:5|max:32',
                'confirm' => 'same:password'
            ]);
            $data['password'] = bcrypt($request->password);
        };
        $account->update($data);

        return redirect()->route('admin.staff.list')->with('success', 'Update successfully');
    }

    public function delete($id) {
        $notification = new NoticesModel();
        $notification = $notification->getNotifications([]);

        $staff = EmployeesModel::find($id);

        $staff->delete();

        return redirect()->route('admin.staff.list', compact('staff', 'notification',))->with('success', 'Delete sucessfully');
    }
}
