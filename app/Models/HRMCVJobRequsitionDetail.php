<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVJobRequsitionDetail extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $table = 'hrm_cv_job_requsition_details';

    public function jobDescription()
    {
        return $this->belongsTo(HRMCVJobDescription::class, 'hrm_cv_job_description_id');
    }

    public function jobRequsition()
    {
        return $this->belongsTo(HRMCVJobRequsition::class, 'hrm_cv_job_requsition_id');
    }
}
