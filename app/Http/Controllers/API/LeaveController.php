<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\HrmLeaveApprove;
use App\Models\HrmEmployeeLeave;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function leaveApplicationList(Request $request)
    {
        $data = $this->leaveListEmployeeWise($request);

        if ($data) {
            $response = [
                'success' => true,
                'message' => $message ?? '',
                'data'    => $data ?? '',
                'error'   => $error ?? '',
                'error_code' => 200,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => $message ?? '',
                'data'    => $data ?? '',
                'error'   => $error ?? '',
                'error_code' => 500,
            ];
        }

        return response()->json($response);
    }

    private function leaveListEmployeeWise($request)
    {
        $employeesID = $request->hrm_employee_id;

        $whereCondition = " and a.hrm_employee_id = $employeesID";

        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));

        if(isset($request->from_date) && ! isset($request->to_date)) {
            $whereCondition .= " and a.applied = '$from_date'";
        }

        if(isset($request->from_date) && isset($request->to_date)) {
            $whereCondition .= " and a.applied between '$from_date' and '$to_date'" ;
        }

        $data = DB::SELECT("SELECT
                b.id,
                a.id as leave_app_id,
                concat(b.employee_name,' | ',d.employee_code) as employee_name,
                c.leave_type,
                (CASE
                    WHEN a.duration = 1 THEN '.50'
                    WHEN a.duration = 3 THEN '.25'
                    ELSE (DATEDIFF(a.date_to,a.date_from) + 1)
                END) as days,
                a.comment,
                a.date_from,
                a.date_to,
                (CASE
                    WHEN f.action_type = 1 THEN 'Approved'
                    WHEN f.action_type = 2 THEN 'Rejected'
                    ELSE 'Pending'
                END) as status
            FROM hrm_leave_application a
            JOIN hrm_employee b on b.id = a.hrm_employee_id and b.active_status = 1
            JOIN hrm_employee_leave_type c On a.hrm_employee_leave_type_id = c.id
            JOIN hrm_employee_job_info d On a.hrm_employee_id = d.hrm_employee_id and d.employee_activity = 1
            JOIN user_location e ON d.hrm_location_id = e.hrm_location_id
            JOIN hrm_leave_approve f ON f.hrm_leave_application_id = a.id
            $whereCondition
            GROUP BY a.id
            ORDER BY a.id DESC
            Limit 100");

        return $data;
    }

    public function store(Request $request)
    {
        $success = false;

        $validator = Validator::make($request->all(), [
            'employee_id'         => 'required',
            'leave_type'          => 'required',
            'duration'            => 'nullable',
            'payment_mode'        => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Form validation failed..!';
        } else {
            DB::beginTransaction();
            try {
                $applied   = date('Y-m-d', strtotime(str_replace('/', '-', $request->apply_date)));
                $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
                $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

                $hrm_leave_years_id = DB::SELECT("SELECT id FROM hrm_leave_years WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'");

                if(empty($hrm_leave_years_id[0]->id)) {
                    $message = 'Leave Applied date cross the Leave Year !';
                }

                $employeejobinfo = HrmEmployeeJobInfo::query()
                    ->where('hrm_employee_id', $request->employee_id)
                    ->where('employee_activity', 1)
                    ->first();

                if($employeejobinfo->overtime_status == 1) {
                    $find = DB::SELECT("SELECT * FROM hrm_employee_leave_type WHERE id=$request->leave_type AND leave_status=2 ");

                    if(!empty( $find )){
                            // dd("NOT sucess");
                        $reservehal = DB::SELECT("SELECT count(id) as id FROM hrm_holiday_against_leave WHERE hrm_employee_id=$request->employee_id AND valid=1");
                        $expensehal = DB::SELECT("SELECT count(b.id) as id
                                                        FROM hrm_leave_application a
                                                        JOIN hrm_employee b on b.id=a.hrm_employee_id and b.active_status=1 AND b.id=$request->employee_id
                                                        JOIN hrm_employee_leave_type c On a.hrm_employee_leave_type_id=c.id and c.leave_status=2
                                                        JOIN hrm_employee_job_info d On a.hrm_employee_id=d.hrm_employee_id and d.employee_activity=1
                                                        JOIN hrm_leave_approve f ON f.hrm_leave_application_id=a.id AND f.action_type=1 AND f.forward=2");

                        if(($reservehal[0]->id) < ($expensehal[0]->id)){
                            $message = 'Sorry you have no reserve holiday against leave!';
                        }
                    }
                }

                $leave_check_from = DB::SELECT("SELECT id FROM hrm_leave_application WHERE '$date_from' between date_from AND  date_to AND valid = 1 AND hrm_employee_id = $request->employee_id");

                $leave_check_to = DB::SELECT("SELECT id FROM hrm_leave_application WHERE '$date_to' between date_from AND date_to AND valid = 1 AND hrm_employee_id=$request->employee_id");

                if(!empty($leave_check_from) || !empty($leave_check_to)) {
                    $message = 'Sorry you have already entry this leave! Please check';
                    $error_code = 500;
                } else {
                    $insert     = new HrmEmployeeLeave;
                    $insert->applied                      = $applied;
                    $insert->hrm_employee_id              = $request->employee_id;
                    $insert->hrm_employee_leave_type_id   = $request->leave_type;
                    $insert->date_from                    = $date_from;
                    $insert->date_to                      = $date_to;
                    $insert->comment                      = $request->comment;
                    $insert->duration                     = $request->duration;
                    $insert->payment_mode                 = $request->payment_mode;
                    $insert->valid                        = 1;
                    $insert->hrm_leave_years_id           = $hrm_leave_years_id[0]->id;
                    $insert->save();

                    $insert_into_leave_Approve     = new HrmLeaveApprove;
                    $insert_into_leave_Approve->hrm_leave_application_id = $insert->id;
                    $insert_into_leave_Approve->users_id                 = $request->applytousername;
                    $insert_into_leave_Approve->save();


                    $success = true;
                    $message = 'Leave has been saved successfully..!';
                    $error_code = 200;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function delete($id)
    {
        $success = false;
        try {
            DB::table('hrm_leave_approve')
                ->where('hrm_leave_application_id', $id)
                ->delete();

            DB::table('hrm_leave_application')
                ->where('id', $id)
                ->delete();

            $success = true;
            $message = 'Data has been deleted successfully..!';
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data'    => $data ?? '',
            'error'   => $error ?? '',
            'error_code' => $error_code ?? '',
        ]);
    }



  public function leaveApproveReject(Request $request)
    {
        $success = false;
        try {


            DB::update("UPDATE hrm_leave_approve SET action_type = $request->status,forward  = 2,comment='$request->comments' WHERE id = $request->hrm_leave_approve_id ");

            if ($request->action == 1)
            {

                $query_data = HrmEmployeeLeave::find($request->hrm_leave_application_id);
                $fdate = $query_data->date_from;
                $tdate = $query_data->date_to;
                $datetime1 = new DateTime($fdate);
                $datetime2 = new DateTime($tdate);
                $interval = $datetime1->diff($datetime2);
                $days_total = $interval->format('%a');
                $duration = $days_total+1;

                // dd($duration);

                $insert                                 = new HrmLeaveLedger;
                $insert->hrm_leave_approve_id           = $request ->hrm_leave_approve_id;
                $insert->used_leave                     = $duration;
                $insert->hrm_employee_leave_type_id     = $query_data ->hrm_employee_leave_type_id;
                $insert->hrm_employee_id                = $query_data ->hrm_employee_id;
                $insert->hrm_leave_years_id             = $query_data ->hrm_leave_years_id;
                $insert->status                         = 2;
                $insert->users_id                       = Auth::user()->id;
                $insert->save();

                //-------Data Process
                // $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $query_data->hrm_employee_id)->where('employee_activity','=',1)->first();
                 
                // for ($x = 0; $x < $duration; $x++) {
                //     $processDate = date('Y-m-d', strtotime($query_data->date_from. ' + '.$x.'days'));
                //     if(date('Y-m-d')< $processDate){
                //         break;
                //     }
                //     $attendance = new AttendanceDataProcessController();
                //     $attendance->attandanceProcess($location->hrm_location_id,$processDate,$query_data->hrm_employee_id);
                // }
            }


            $success = true;
            $message = 'Successfully Approved!';
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data'    => $data ?? '',
            'error'   => $error ?? '',
            'error_code' => $error_code ?? '',
        ]);
    }









    public function leaveTypeDropData(Request $request)
    {
        $success = false;
        try {
            $leaveTypes = DB::table('hrm_employee_leave_type')
                ->select('id', 'leave_type as text')
                ->where('valid', 1)
                ->where('leave_type', 'like', "%{$request->term}%")
                ->get();

            $success = true;
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $leaveTypes ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }



    public function pendingLeaveList(Request $request)
    {
        $userId           = auth()->user()->id;

        $success = false;
        try {
            $leaveTypes = DB::SELECT("SELECT 
                                            g.id AS hrm_leave_approve_id,
                                            CONCAT(a.employee_name, ' | ', d.employee_code) AS employee_name,
                                            e.depertment_name,
                                            h.designation_name,
                                            c.leave_type,
                                            b.date_from,
                                            b.date_to,
                                            (CASE
                                                WHEN b.duration = 1 THEN '.50'
                                                WHEN b.duration = 3 THEN '.25'
                                                ELSE (DATEDIFF(b.date_to, b.date_from) + 1)
                                            END) AS days,
                                            b.comment,
                                            (CASE
                                                WHEN b.payment_mode = 1 THEN 'With Pay'
                                                ELSE 'Without Pay'
                                            END) AS payment_mode
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_leave_application b ON a.id = b.hrm_employee_id
                                                JOIN
                                            hrm_employee_leave_type c ON b.hrm_employee_leave_type_id = c.id
                                                AND a.active_status = 1
                                                JOIN
                                            hrm_employee_job_info d ON a.id = d.hrm_employee_id
                                                JOIN
                                            hrm_depertment e ON e.id = d.hrm_depertment_id
                                                JOIN
                                            user_location f ON d.hrm_location_id = f.hrm_location_id
                                                AND f.users_id = $userId
                                                JOIN
                                            hrm_leave_approve g ON b.id = g.hrm_leave_application_id
                                                JOIN
                                            hrm_designation h ON d.hrm_designation_id = h.id
                                        WHERE
                                            a.active_status = 1
                                                AND g.id IN (SELECT 
                                                    id
                                                FROM
                                                    hrm_leave_approve
                                                WHERE
                                                    action_type IS NULL AND forward IS NULL
                                                        AND users_id = $userId)
                                        GROUP BY b.id");


            $success = true;
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $leaveTypes ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }



    public function applyTo(Request $request)
    {

        $userId           = auth()->user()->id;
        $hrmEmployeeId    = auth()->user()->hrm_employee_id;
        $EmpInfoDtls      = HrmEmployeeJobInfo::where('hrm_employee_id',$hrmEmployeeId)->WHERE('employee_activity',1)->first();
        $hrm_manage_by_id = $EmpInfoDtls->hrm_manage_by_id;

        // dd($hrm_manage_by_id);
        $success = false;
        try {


            // if (!empty($request->term)) {
            //     $data = DB::select("SELECT id, concat(name, ' | ', email) as text FROM users
            //         WHERE name LIKE '%$request->term%'
            //         AND valid = 1
            //         OR email LIKE '%$request->term%'
            //     ");
            // } else {
            //     $data = DB::select("SELECT id, concat(name, ' | ', email) as text FROM users WHERE valid = 1");
            // }

             $data = DB::select("SELECT id, concat(name, ' | ', email) as text FROM users WHERE valid = 1 AND hrm_employee_id=$hrm_manage_by_id");


            $success = true;
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }
}
