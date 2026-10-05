<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HRMCVDropMaster extends Model
{
    protected $guarded = ['id'];

    protected $table = 'hrm_cv_drop_master';

    public function requsition(): BelongsTo
    {
        return $this->belongsTo(HRMCVJobRequsition::class, 'hrm_cv_job_requsition_id');
    }
}
