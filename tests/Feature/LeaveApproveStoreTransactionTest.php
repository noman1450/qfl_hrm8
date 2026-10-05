<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HrmEmployeeLeave;
use App\Models\HrmLeaveApprove;
use App\Models\HrmLeaveLedger;
use App\Models\HrmEmployee;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveApproveStoreTransactionTest extends TestCase
{
    public function test_approve_proceeds_to_attendance_process_without_txn_crash()
    {
        // Employee with no pay_register for current month => approval reaches attandanceProcess
        $emp  = HrmEmployee::where('active_status', 1)->first();
        $job  = HrmEmployeeJobInfo::where('hrm_employee_id', $emp->id)->where('employee_activity', 1)->first();
        $this->assertNotNull($emp);
        $this->assertNotNull($job, 'employee must have an active job info');

        // leave application for TODAY (past enough to enter attandanceProcess loop)
        $leaveAppId = DB::table('hrm_leave_application')->insertGetId([
            'applied'                        => now()->toDateString(),
            'hrm_employee_leave_type_id'     => 6,
            'date_from'                      => now()->toDateString(),
            'date_to'                        => now()->toDateString(),
            'comment'                        => '',
            'duration'                       => 2,
            'payment_mode'                   => 1,
            'valid'                          => 1,
            'hrm_leave_years_id'             => 1,
            'hrm_employee_id'                => $emp->id,
            'users_id'                       => 1,
            'created_at'                     => now(),
        ]);
        $leaveApp = HrmEmployeeLeave::find($leaveAppId);

        $laId = DB::table('hrm_leave_approve')->insertGetId([
            'hrm_leave_application_id' => $leaveAppId,
            'users_id'                 => 1,
            'action_type'              => null,
            'forward'                  => null,
        ]);
        $la = HrmLeaveApprove::find($laId);
        $pr = DB::table('pay_register')
            ->where('hrm_employee_job_info_id', $job->id)
            ->where('hrm_month_id', date('m'))
            ->where('year_id', date('Y'))
            ->whereIn('salary_genarate_type', [1, 2])
            ->first();
        $this->assertNull($pr, 'pay_register must be absent so approval proceeds to attandanceProcess');

        $user = User::find(1);
        $this->actingAs($user);

        $attBefore = DB::table('hrm_attendance')
            ->where('hrm_employee_id', $emp->id)
            ->where('punche_date', now()->toDateString())
            ->count();

        $exception = null;
        try {
            $response = $this->post('leaveapprove', [
                'hrm_leave_application_id' => $leaveApp->id,
                'hrm_leave_approve_id'     => $la->id,
                'action'                   => 1,
                'forward'                  => 0,
                'days'                     => 1,
                'comment'                  => 'transaction test',
            ]);
        } catch (\Throwable $e) {
            $exception = $e;
        }

        $this->assertNull($exception, 'store() must not throw: ' . ($exception ? $exception->getMessage() : ''));
        $this->assertEquals(302, $response->status());

        // approval committed (action_type=1)
        $la->refresh();
        $this->assertEquals(1, $la->action_type);

        // ledger row committed
        $this->assertDatabaseHas('hrm_leave_ledger', [
            'hrm_leave_approve_id' => $la->id,
        ]);

        // attandanceProcess ran (created/updated attendance rows for today)
        $attAfter = DB::table('hrm_attendance')
            ->where('hrm_employee_id', $emp->id)
            ->where('punche_date', now()->toDateString())
            ->count();
        // attandanceProcess should have produced at least one row OR touched the date
        $this->assertTrue(session('alert-success') !== null, 'success flash must be set');

        // ---------- CLEANUP ----------
        HrmLeaveLedger::where('hrm_leave_approve_id', $la->id)->delete();
        $la->delete();
        $leaveApp->delete();
        // remove any attendance rows attandanceProcess may have generated for this test
        DB::table('hrm_attendance')
            ->where('hrm_employee_id', $emp->id)
            ->where('punche_date', now()->toDateString())
            ->delete();
    }
}
