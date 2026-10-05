<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HrmLeaveType extends Model
{
   	protected $table 		= 'hrm_employee_leave_type';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
