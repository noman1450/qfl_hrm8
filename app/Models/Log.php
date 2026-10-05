<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table 		= 'log';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
}
