<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeJoining extends Model
{
	protected $table 		= 'hrm_employee_joining';
	protected $guarded	= ['id'];
	public $timestamps 		= false;
}
