<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRMEmployeeTaskComplete extends Model
{
    protected $table = 'hrm_employee_task_complete';

    protected $guarded = ['id'];

    public $timestamps = false;
}
