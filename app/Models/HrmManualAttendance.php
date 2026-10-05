<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmManualAttendance extends Model
{
  	protected $table 		= 'hrm_manual_attendance_data';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
