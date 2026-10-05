<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeLevelingDetails extends Model
{
    protected $table      = 'hrm_employee_leveling_details';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $guarded    = ['id'];
}
