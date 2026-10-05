<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLeaveLedger extends Model
{
	protected $table 		= 'hrm_leave_ledger';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
