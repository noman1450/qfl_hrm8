<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVJobRequsitionCompensation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'hrm_cv_job_requsition_compensation';
}
