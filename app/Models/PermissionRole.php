<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    protected $guard_name = 'web';
	
	public $timestamps 		= false;
	protected $table 		= 'permission_role';
	protected $primaryKey	= 'id';
}
