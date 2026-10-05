<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeLevelingMaster extends Model
{
    protected $table      = 'hrm_employee_leveling_master';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $guarded    = ['id'];
}
