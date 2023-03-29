<?php

namespace Database\Factories;

use App\Models\EmployeesModel;
use App\Models\TimekeepersModel;
use App\Models\TimesheetsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\timesheets>
 */
class TimesheetsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = TimesheetsModel::class;

    public function definition()
    {
        $employee = new EmployeesModel;
        $listId = $employee->getIdEmployees(['status' => 1]);
        $employeeid = $listId[rand(0, count($listId) - 1)]->id;
        

        $tkerid = new TimekeepersModel();
        $tkerid = $tkerid->getTimekeepers();
        return [
            'timekeeper_id' => ($tkerid == null) ? null : $tkerid[rand(0, count($tkerid) - 1)]->id,
            'employee_id' => $employeeid,
            'timekeeping_at' => $this->faker->dateTimeBetween($startDate = '-3 months', $endDate = 'now', $timezone = 'asia/ho_chi_minh'),
            'face_image' => $this->faker->imageUrl,
            'status' => 1,
            'created_user' => $employeeid,
            'updated_user' => $employeeid
        ];
    }
}
