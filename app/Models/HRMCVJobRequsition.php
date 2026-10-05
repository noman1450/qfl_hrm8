<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMCVJobRequsition extends Model
{
    protected $guarded = ['id'];

    protected $table = 'hrm_cv_job_requsition';

    public function deparment()
    {
        return $this->belongsTo(HrmDepertment::class, 'hrm_depertment_id');
    }

    public function designation()
    {
        return $this->belongsTo(HrmDesignation::class, 'hrm_designation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_users_id');
    }
}
