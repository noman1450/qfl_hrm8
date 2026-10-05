<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeActivity extends Model
{
  	protected $table 		= 'hrm_employee_activity';
	protected $guarded	= ['id'];
	public $timestamps 		= false;
}
