<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVJobDescription extends Model
{
    protected $guarded = ['id'];

    protected $table = 'hrm_cv_job_description';

    public function group()
    {
        return $this->belongsTo(HRMCVJobDescriptionGroup::class, 'hrm_cv_job_description_group_id')->orderby('id');
    }
}
