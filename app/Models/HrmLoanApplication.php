<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLoanApplication extends Model
{
    protected $table 		= 'hrm_loan_application';
	protected $primaryKey	= 'id';
	public $timestamps 		= true;
}
