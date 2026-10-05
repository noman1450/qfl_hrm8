<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMEmployeeTaskType extends Model
{
    protected $table = 'hrm_employee_task_type';

    protected $guarded = ['id'];

    public $timestamps = false;
}
