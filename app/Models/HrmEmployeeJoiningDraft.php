<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeJoiningDraft extends Model
{
    protected $table 		= 'hrm_employee_joining_draft';
    public $timestamps	= false;

    protected $guarded = ['id'];
}
