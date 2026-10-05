<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{

    protected $guard_name = 'web';
	
	protected $table 		= 'permissions';
	protected $primaryKey	= 'id';
}
