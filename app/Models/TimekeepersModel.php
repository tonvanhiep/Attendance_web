<?php

namespace App\Models;

use Database\Factories\TimekeepersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class TimekeepersModel extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'timekeepers';

    protected $fillable = [
        'id',
        'office_id',
        'timekeeper_name',
        'account',
        'password',
        'status',
        'created_at',
        'created_user',
        'updated_at',
        'updated_user'
    ];

    public function selectTimekeepers($condition = null)
    {
        $timekeepers = DB::table($this->table)->select('*');

        return $timekeepers;
    }

    public function getTimekeepers($condition = null)
    {
        $result = $this->selectTimekeepers($condition);
        return $result == null ? [] : $result->get();
    }

    protected static function newFactory()
    {
        return TimekeepersFactory::new();
    }
}
