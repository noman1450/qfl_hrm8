<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVDropDetail extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $table = 'hrm_cv_drop_details';
}
