<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FaceEmployeeImagesModel extends Model
{
    use HasFactory;

    protected $table = 'face_employee_images';

    protected $fillable = [
        'id',
        'employee_id',
        'image_url',
        'description',
        'status',
        'note',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function selectImages($condition = null)
    {
        $images = DB::table($this->table)
            ->join('employees', $this->table . '.employee_id', 'employees.id')
            ->select('employees.last_name', 'employees.first_name', 'employees.id', $this->table . '.image_url', $this->table . '.status', $this->table . '.description', $this->table . '.id as id_image');
        if (isset($condition['id'])) {
            $images = $images->where($this->table . '.employee_id', $condition['id']);
        }
        if (isset($condition['status'])) {
            $images = $images->where($this->table . '.status', $condition['status']);
        }
        if (isset($condition['office'])) {
            $images = $images->where('employees.office_id', $condition['office']);
        }
        if (isset($condition['employee_id'])) {
            $images = $images->where('employees.id', $condition['employee_id']);
        }
        return $images;
    }

    public function getImages($condition = null)
    {
        return $this->selectImages($condition)->get();
    }

    public function saveAttendance($data = null)
    {
        if ($data == null) return;
    }
}
