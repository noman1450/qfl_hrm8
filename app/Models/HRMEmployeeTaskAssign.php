<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRMEmployeeTaskAssign extends Model
{
    protected $table = 'hrm_employee_task_assign';

    protected $guarded = ['id'];

    public $timestamps = false;
}
