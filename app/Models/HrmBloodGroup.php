<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmBloodGroup extends Model
{
	protected $table 		= 'hrm_blood_group';
	protected $guarded	= ['id'];
	public $timestamps 		= false;
}
