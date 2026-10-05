<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmAttendanceDataProcessLog extends Model
{
    protected $table = 'hrm_attendance_data_process_log';
    protected $primaryKey   = 'id';
    public $timestamps = true;
}
