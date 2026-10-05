<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeShift extends Model
{
	protected $table 		= 'hrm_employee_shift';
	protected $guarded	= ['id'];
	public $timestamps 		= true;
}
