<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmDeletedDataLog extends Model
{
	protected $table 		= 'hrm_deleted_data_log';
	protected $primaryKey	= 'id';
	public $timestamps 		= true;

}
