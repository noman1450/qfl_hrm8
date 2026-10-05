<?php

namespace App\Jobs;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\AttendanceDataProcessController;

class ManualAttendanceProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $hrmLocationId;
    public $date;
    public $employees;
    public $userId;

    public function __construct($hrmLocationId, $date, $employees, $userId)
    {
        $this->hrmLocationId = $hrmLocationId;
        $this->date          = $date;
        $this->employees     = $employees;
        $this->userId        = $userId;
    }

    public function handle()
    {
        Auth::loginUsingId($this->userId);

        $attendance = new AttendanceDataProcessController();
        $attendance->attandanceProcess($this->hrmLocationId, $this->date, $this->employees);
    }
}
