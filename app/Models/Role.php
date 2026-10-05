<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $guard_name = 'web';
    
	
    protected $table 		= 'roles';
	protected $primaryKey	= 'id';
}

