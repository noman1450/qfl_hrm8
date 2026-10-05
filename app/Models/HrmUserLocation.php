<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmUserLocation extends Model
{
    protected $table 		= 'user_location';
	protected $primaryKey	= 'id';
	public $timestamps 		= false;
    protected $guarded = [];
}
