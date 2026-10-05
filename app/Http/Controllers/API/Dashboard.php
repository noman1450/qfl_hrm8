<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Functions;

class Dashboard extends Controller
{
    public $successStatus = 200;

    public function attendance_summary(Request $request)
    {
        $status     = false;
        $message    = "";
        $error      = "";
        $data       = "";
        //--
        $employee_id    = $request->employee_id;
        $month_id       = $request->month_id;
        $year_id        = $request->year_id;
        
        try {
            $data = DB::SELECT("SELECT 
                                        b.alies attendance_status, COUNT(a.id) days
                                    FROM
                                        hrm_attendance a
                                            JOIN
                                        hrm_attendance_status b ON a.attendance_status = b.id
                                    WHERE
                                        a.hrm_employee_id = $employee_id
                                            AND MONTH(a.punche_date) = '$month_id'
                                            AND YEAR(a.punche_date) = '$year_id'
                                    GROUP BY a.attendance_status");

            return Functions::response(true, 'success', $error, $data);
        } catch (Exception $ex) {
            return Functions::response($status, 'failed', $error, $data);
        }
    }
}
