<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTLog extends Model
{
    protected $table 		= 'log_ot';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
