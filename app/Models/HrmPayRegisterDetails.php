<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmPayRegisterDetails extends Model
{
	public $timestamps 		= false;
    protected $table 		= 'pay_register_details';
	protected $primaryKey	= 'id';
}
