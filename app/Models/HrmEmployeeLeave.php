<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeLeave extends Model
{
   	protected $table 		= 'hrm_leave_application';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
