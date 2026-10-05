<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedRoles extends Model
{
  	protected $table 		= 'assigned_roles';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
