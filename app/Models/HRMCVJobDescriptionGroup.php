<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVJobDescriptionGroup extends Model
{
    public $timestamps = false;

    protected $table = 'hrm_cv_job_description_group';

    public function jobDesccriptions()
    {
        return $this->hasMany(HRMCVJobDescription::class);
    }
}
