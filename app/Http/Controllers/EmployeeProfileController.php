<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HrmLeaveApprove;
use App\Models\HrmEmployeeLeave;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeProfileController extends Controller
{
    public function my_leaves()
    {
        if (request()->ajax()) {
            $user_id = auth()->id();

            $hrm_employee_id = auth()->user()->hrm_employee_id;

            $employee = DB::select("SELECT
                    b.id,
                    a.id as leave_app_id,
                    f.id as leave_approve_id,
                    concat(b.employee_name,' | ',d.employee_code) as employee_name,
                    c.leave_type,
                    (CASE
                        WHEN a.payment_mode = 1 THEN 'With Pay'
                        WHEN a.payment_mode = 2 THEN 'Without Pay'
                        ELSE ''
                    END) as payment_mode,
                    (CASE
                    WHEN a.duration=1 THEN '.50'
                    WHEN a.duration=3 THEN '.25'
                    ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                    END) as days,
                    a.comment,
                    date_format(a.date_from, '%d %b, %Y') date_from,
                    date_format(a.date_to, '%d %b, %Y') date_to,
                    b.Images,
                    g.name as apply_to
                FROM
                    hrm_leave_application a
                        JOIN
                    hrm_employee b on b.id = a.hrm_employee_id and b.active_status = 1 and b.id = $hrm_employee_id
                        JOIN
                    hrm_employee_leave_type c On a.hrm_employee_leave_type_id = c.id
                        JOIN
                    hrm_employee_job_info d On a.hrm_employee_id = d.hrm_employee_id and d.employee_activity = 1
                        JOIN
                    user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                        JOIN
                    hrm_leave_approve f ON f.hrm_leave_application_id = a.id
                    And f.action_type is null
                        join
                    users g ON  f.users_id = g.id
            ");

            return json_encode(array('data' => $employee));
        }

        return view('employee_profile.leave');
    }

    public function my_leaves_create()
    {

        $hrm_employee_id = auth()->user()->hrm_employee_id;
        $employee = DB::select("SELECT
                a.id,
                CONCAT_WS(' | ', a.employee_name, IFNULL(b.employee_code, ''), c.alis, a.contact_number) AS employee_name
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    AND b.employee_activity = 1
                    AND a.active_status = 1
                    AND a.id = $hrm_employee_id
                    JOIN
                hrm_designation c ON b.hrm_designation_id = c.id
                    JOIN
                hrm_depertment d ON b.hrm_depertment_id = d.id
            LIMIT 1
        ")[0];

        return view('employee_profile.leave_create', compact('employee'));
    }

    public function my_leaves_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name'              => 'required',
            'employee_leave_type'        => 'required',
            'duration'                   => 'required',
            'payment_mode'               => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('my_leaves/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $applied            = date('Y-m-d', strtotime(str_replace('/', '-', $request->applied)));
        $date_from          = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to            = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        $hrm_leave_years_id = DB::SELECT("SELECT id FROM hrm_leave_years WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'  ");

        if(empty($hrm_leave_years_id[0]->id)){
            $request->session()->flash('alert-danger', 'Leave Applied date cross the Leave Year !');
            return redirect('leaveyear');
        }


        $employeejobinfo = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_name)
                        ->where('employee_activity', 1)
                        ->first();

        // dd($employeejobinfo->overtime_status);

        if($employeejobinfo->overtime_status==1){
            $find = DB::SELECT("SELECT * FROM hrm_employee_leave_type WHERE id=$request->employee_leave_type AND leave_status=2 ");
            if(!empty( $find )){
                // dd("NOT sucess");
                $reservehal = DB::SELECT("SELECT count(id) as id FROM hrm_holiday_against_leave WHERE hrm_employee_id=$request->employee_name AND valid=1");
                $expensehal = DB::SELECT("SELECT count(b.id) as id
                                                FROM hrm_leave_application a
                                                JOIN hrm_employee b on b.id=a.hrm_employee_id and b.active_status=1 AND b.id=$request->employee_name
                                                JOIN hrm_employee_leave_type c On a.hrm_employee_leave_type_id=c.id and c.leave_status=2
                                                JOIN hrm_employee_job_info d On a.hrm_employee_id=d.hrm_employee_id and d.employee_activity=1
                                                JOIN hrm_leave_approve f ON f.hrm_leave_application_id=a.id AND f.action_type=1 AND f.forward=2");

                if (($reservehal[0]->id) > ($expensehal[0]->id)) {
                    // dd("YES");
                } else {
                    $request->session()->flash('alert-danger', 'Sorry you have no reserve holiday against leave!');
                    return redirect('my_leaves');
                }
            }
        }

        $leave_check = DB::SELECT("SELECT id FROM hrm_leave_application WHERE '$date_from'  between date_from AND  date_to  AND valid=1 AND hrm_employee_id=$request->employee_name");

        // dd($leave_check);
        if(!empty($leave_check)){
             $request->session()->flash('alert-danger', 'Sorry you have already entry this leave! Please check');
             return redirect('my_leaves');
        }

        $leave_check = DB::SELECT("SELECT id FROM hrm_leave_application WHERE '$date_to'  between date_from AND  date_to  AND valid=1  AND hrm_employee_id=$request->employee_name");

        if(!empty($leave_check)){
             $request->session()->flash('alert-danger', 'Sorry you have already entry this leave! Please check');
             return redirect('my_leaves');
        }


        $insert     = new HrmEmployeeLeave;
        $insert->applied                      = $applied;
        $insert->hrm_employee_id              = $request->employee_name;
        $insert->hrm_employee_leave_type_id   = $request->employee_leave_type;
        $insert->date_from                    = $date_from;
        $insert->date_to                      = $date_to;
        $insert->comment                      = $request->comment;
        $insert->duration                     = $request->duration;
        $insert->payment_mode                 = $request->payment_mode;
        $insert->valid                        = 1;
        $insert->hrm_leave_years_id           = $hrm_leave_years_id[0]->id;
        $insert->save();


        $approve                           = new HrmLeaveApprove;
        $approve->hrm_leave_application_id = $insert->id;
        $approve->users_id                 = $request->applytousername;
        $approve->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');

        return redirect('my_leaves');
    }

    public function my_leaveapprovedlist()
    {
        if (request()->ajax()) {
            $date_from  = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_from)));
            $date_to  = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_to)));
            $condition = " and a.date_to between '$date_from'  and '$date_to'";

            $user_id = auth()->id();

            $hrm_employee_id = auth()->user()->hrm_employee_id;

            $employee = DB::select("SELECT
                    b.id,
                    a.id as leave_app_id,
                    a.applied,
                    concat(b.employee_name,' | ',d.employee_code) as employee_name,
                    a.duration,
                    c.leave_type,
                    (CASE
                    WHEN a.duration=1 THEN '.50'
                    WHEN a.duration=3 THEN '.25'
                    ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                    END)  as days,
                    a.comment,
                    CONCAT(a.date_from,'  to  ',a.date_to) AS date_range,
                    b.Images,
                    'Approved' as status,
                    g.name as apply_to
                FROM
                    hrm_leave_application a
                        JOIN
                    hrm_employee b ON b.id = a.hrm_employee_id AND b.active_status = 1 AND b.id = $hrm_employee_id
                        JOIN
                    hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                        JOIN
                    hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id AND d.employee_activity = 1
                        $condition
                        JOIN
                    user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                        JOIN
                    hrm_leave_approve f ON f.hrm_leave_application_id = a.id AND f.action_type = 1 AND f.forward = 2
                        JOIN
                    users g ON f.users_id = g.id 
            ");

            return json_encode(array('data' => $employee));
        }

        return view('employee_profile.leave_approved');
    }
 
    public function my_leaverejectedlist()
    {
        if (request()->ajax()) {
            $user_id = auth()->id();

            $hrm_employee_id = auth()->user()->hrm_employee_id;

            $employee = DB::select("SELECT
                    b.id,
                    a.id as leave_app_id,
                    concat(b.employee_name,' | ',d.employee_code) as employee_name,
                    c.leave_type,
                    (CASE
                    WHEN a.duration=1 THEN '.50'
                    WHEN a.duration=3 THEN '.25'
                    ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                    END)  as days,
                    a.comment,
                    a.date_from,
                    a.date_to,
                    b.Images,
                    'Reject' as status,
                    f.id as hrm_leave_approve_id,
                    g.name as apply_to
                FROM
                    hrm_leave_application a
                        JOIN
                    hrm_employee b on b.id = a.hrm_employee_id and b.active_status = 1 AND b.id = $hrm_employee_id
                        JOIN
                    hrm_employee_leave_type c On a.hrm_employee_leave_type_id = c.id
                        JOIN
                    hrm_employee_job_info d On a.hrm_employee_id = d.hrm_employee_id and d.employee_activity = 1
                        JOIN
                    user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                        JOIN
                    hrm_leave_approve f ON f.hrm_leave_application_id = a.id AND f.action_type = 2 AND f.forward = 2
                        JOIN
                    users g On f.users_id = g.id 
            ");

            return json_encode(array('data' => $employee));
        }

        return view('employee_profile.leave_rejected');
    }

    public function myAttendance()
    {
        if (request()->ajax()) {
            $user_id = auth()->id();

            $hrm_location_id = DB::select("SELECT b.id FROM user_location a JOIN hrm_location b ON a.hrm_location_id =b.id and a.users_id = $user_id AND a.default_location = 1")[0]->id;
            $hrm_employee_id = auth()->user()->hrm_employee_id;

            $date_from = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_from)));
            $date_to = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_to)));

            $data = DB::select("SELECT
                    bb.id,
                    bb.employee_name,
                    b.employee_code,
                    d.location_name,
                    e.depertment_name,
                    f.alis AS designation_name,
                    i.category_name,
                    j.section_name,
                    f.priority,
                    ROUND(SUM(a.Absent + a.Present + a.Leavess + a.halfDayleave + a.OSD + a.Late + a.Friday + a.Holiday + a.withoutpayleave + a.withoutpayhalfdayleavewithoutpay + a.HolidayAgainstLeave + a.QuarterDayLeave + a.withoutpayQuarterDayLeave),
                            2) AS total_days,
                    ROUND(SUM(a.Present + a.Leavess + a.OSD + a.Late + a.Friday + a.Holiday + a.HolidayAgainstLeave + a.halfDayleave + a.QuarterDayLeave + (a.withoutpayhalfdayleavewithoutpay / 2) + (a.withoutpayQuarterDayLeave * .75)),
                            2) AS payable_days,
                    ROUND(SUM(a.Absent), 2) AS Absent,
                    ROUND((SUM(a.Present) + SUM(a.halfDayleave * .50) + SUM(a.QuarterDayLeave * .75) + SUM(a.withoutpayhalfdayleavewithoutpay * .50) + SUM(a.withoutpayQuarterDayLeave * .75)),
                            2) AS Present,
                    ROUND(SUM(a.Late), 2) AS Late,
                    ROUND(SUM(a.OSD), 2) AS OSD,
                    ROUND(SUM(a.Friday), 2) AS WeeklyHoliday,
                    ROUND(SUM(a.Holiday), 2) AS Holiday,
                    ROUND(SUM(a.HolidayAgainstLeave), 2) AS HolidayAgainstLeave,
                    ROUND(SUM((a.Leavess) + (a.halfDayleave / 2) + (a.QuarterDayLeave / 4)),
                            2) AS withPayLeaves,
                    ROUND(SUM((a.withoutpayleave) + (a.withoutpayhalfdayleavewithoutpay / 2) + (a.withoutpayQuarterDayLeave / 4)),
                            2) AS withoutPayLeaves
                FROM
                    (SELECT
                        a.hrm_employee_id,
                            CASE
                                WHEN a.attendance_status = 1 THEN COUNT(a.id)
                                ELSE 0
                            END Absent,
                            CASE
                                WHEN a.attendance_status = 2 THEN COUNT(a.id)
                                ELSE 0
                            END Present,
                            CASE
                                WHEN a.attendance_status = 3 THEN COUNT(a.id)
                                ELSE 0
                            END Leavess,
                            CASE
                                WHEN a.attendance_status = 4 THEN COUNT(a.id)
                                ELSE 0
                            END halfDayleave,
                            CASE
                                WHEN a.attendance_status = 5 THEN COUNT(a.id)
                                ELSE 0
                            END OSD,
                            CASE
                                WHEN a.attendance_status = 6 THEN COUNT(a.id)
                                ELSE 0
                            END Late,
                            CASE
                                WHEN a.attendance_status = 7 THEN COUNT(a.id)
                                ELSE 0
                            END Friday,
                            CASE
                                WHEN a.attendance_status = 8 THEN COUNT(a.id)
                                ELSE 0
                            END Holiday,
                            CASE
                                WHEN a.attendance_status = 9 THEN COUNT(a.id)
                                ELSE 0
                            END withoutpayleave,
                            CASE
                                WHEN a.attendance_status = 10 THEN COUNT(a.id)
                                ELSE 0
                            END withoutpayhalfdayleavewithoutpay,
                            CASE
                                WHEN a.attendance_status = 11 THEN COUNT(a.id)
                                ELSE 0
                            END HolidayAgainstLeave,
                            CASE
                                WHEN a.attendance_status = 12 THEN COUNT(a.id)
                                ELSE 0
                            END QuarterDayLeave,
                            CASE
                                WHEN a.attendance_status = 13 THEN COUNT(a.id)
                                ELSE 0
                            END withoutpayQuarterDayLeave
                    FROM
                        hrm_attendance a
                    JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        AND a.punche_date BETWEEN '$date_from' AND '$date_to'
                        AND b.hrm_employee_id = $hrm_employee_id
                        AND b.id IN (SELECT
                            MAX(bbb.id) AS id
                        FROM
                            (SELECT
                            hrm_employee_job_info_id
                        FROM
                            hrm_employee_activity
                        WHERE
                            '$date_to' BETWEEN start_date AND end_date
                                AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                            hrm_employee_job_info_id
                        FROM
                            hrm_employee_activity
                        WHERE
                            end_date IS NULL
                                AND start_date <= '$date_to'
                                AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                            hrm_employee_job_info_id
                        FROM
                            hrm_employee_activity
                        WHERE
                            end_date BETWEEN '$date_from' AND '$date_to'
                                AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                            hrm_employee_job_info_id
                        FROM
                            hrm_employee_activity
                        WHERE
                            end_date BETWEEN '$date_from' AND '$date_to'
                                AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7 , 5)) aaa
                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id = bbb.id
                            AND bbb.hrm_location_id = $hrm_location_id
                        GROUP BY bbb.hrm_employee_id)
                    GROUP BY a.hrm_employee_id , a.attendance_status) a
                        JOIN
                    hrm_employee bb ON a.hrm_employee_id = bb.id
                        JOIN
                    hrm_employee_job_info b ON bb.id = b.hrm_employee_id
                        JOIN
                    hrm_location d ON b.hrm_location_id = d.id
                        JOIN
                    hrm_depertment e ON b.hrm_depertment_id = e.id
                        JOIN
                    hrm_designation f ON b.hrm_designation_id = f.id
                        JOIN
                    hrm_employee_joining g ON b.hrm_employee_id = g.hrm_employee_id
                        JOIN
                    hrm_employment_status h ON b.hrm_employment_status_id = h.id
                        JOIN
                    hrm_category i ON b.hrm_category_id = i.id
                        JOIN
                    hrm_section j ON b.hrm_section_id = j.id
                        AND b.id IN (SELECT
                            MAX(bbb.id) AS id
                        FROM
                            (SELECT
                                hrm_employee_job_info_id
                            FROM
                                hrm_employee_activity
                            WHERE
                                '$date_to' BETWEEN start_date AND end_date
                                    AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                                hrm_employee_job_info_id
                            FROM
                                hrm_employee_activity
                            WHERE
                                end_date IS NULL
                                    AND start_date <= '$date_to'
                                    AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                                hrm_employee_job_info_id
                            FROM
                                hrm_employee_activity
                            WHERE
                                end_date BETWEEN '$date_from' AND '$date_to'
                                    AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7 , 5) UNION ALL SELECT
                                hrm_employee_job_info_id
                            FROM
                                hrm_employee_activity
                            WHERE
                                end_date BETWEEN '$date_from' AND '$date_to'
                                    AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7 , 5)) aaa
                                JOIN
                            hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id = bbb.id
                                AND bbb.hrm_location_id = $hrm_location_id
                        GROUP BY bbb.hrm_employee_id)
                GROUP BY bb.employee_name , b.employee_code , d.location_name , e.depertment_name , f.alis , i.category_name , j.section_name , f.priority

                ORDER BY e.depertment_name , f.priority
            ");

            return datatables()->of($data)
                ->addColumn('HolydayCount', function ($data) {
                    return $data->Holiday + $data->HolidayAgainstLeave;
                })
                ->addColumn('EmployeeName', function ($data) {
                    return "
                    <div style=padding:5px 0>
                        <div style=font-size:13px;color:#000>
                            $data->employee_name | $data->employee_code
                        </div>
                        <div style=color:#5f5d5dd5;font-style:italic;>
                            $data->designation_name | $data->depertment_name
                        </div>
                    </div>
                    ";
                })
                ->addColumn('Link', function ($data) {
                    return '
                    <a href="'.url('my_attendance_details/'. encrypt($data->id)).'" style="display:block;padding:9px;" data-bs-toggle="tooltip" title="Details">
                        <svg style="height:16px;width:16px;" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>';
                })
                ->rawColumns(['Link', 'EmployeeName'])
                ->make(true);
        }

        return view('employee_profile.my_attendance');
    }

    public function my_attendance_details($id)
    {
        if (request()->ajax()) {
            $hrm_employee_id = decrypt($id);

            $from_date = date('Y-m-d', strtotime(request()->from_date));
            $to_date = date('Y-m-d', strtotime(request()->to_date));

            $data['employee'] = DB::select("SELECT
                    a.id,
                    a.employee_name,
                    concat('+880',a.contact_number) as contact_number,
                    a.email,
                    d.depertment_name,
                    e.designation_name,
                    f.joining_date,
                    h.category_name,
                    a.Images
                FROM
                    hrm_employee a
                        JOIN
                    hrm_employee_job_info b ON a.id = b.hrm_employee_id
                        AND a.active_status = 1
                        AND b.employee_activity = 1
                        AND a.id = $hrm_employee_id
                        JOIN
                    hrm_depertment d ON b.hrm_depertment_id = d.id
                        JOIN
                    hrm_designation e ON b.hrm_designation_id = e.id
                        JOIN
                    hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                        JOIN
                    hrm_employment_status g ON b.hrm_employment_status_id = g.id
                        JOIN
                    hrm_category h ON b.hrm_category_id = h.id
            ")[0];

            $data['attendance_details'] = DB::select("SELECT
                    a.id,
                    a.punche_date,
                    a.in_time,
                    a.out_time,
                    a.early_out_time,
                    a.late_time,
                    a.overtime_time,
                    b.alies attendance_status,
                    b.id as hrm_attendance_status_id
                    -- b.color_code
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_attendance_status b ON b.id = a.attendance_status
                WHERE
                    a.hrm_employee_id = $hrm_employee_id
                    AND a.punche_date BETWEEN '$from_date' AND '$to_date'
                ORDER BY a.punche_date
            ");

            $data['monthly_summary'] = DB::SELECT("SELECT
                    b.attendance_status,
                    COUNT(a.id) days
                    -- b.color_code
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_attendance_status b ON a.attendance_status = b.id
                WHERE
                    a.hrm_employee_id = $hrm_employee_id
                    AND a.punche_date BETWEEN '$from_date' AND '$to_date'
                GROUP BY b.id
            ");

            $data['early_out_time'] = DB::SELECT("SELECT
                        SEC_TO_TIME( SUM( TIME_TO_SEC( a.early_out_time ) ) ) as early_out_time
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_attendance_status b ON a.attendance_status = b.id
                WHERE
                    a.hrm_employee_id = $hrm_employee_id
                    AND a.punche_date BETWEEN '$from_date' AND '$to_date'
            ")[0]->early_out_time;

            $data['late_time'] = DB::SELECT("SELECT
                        SEC_TO_TIME( SUM( TIME_TO_SEC( a.late_time ) ) ) as late
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_attendance_status b ON a.attendance_status = b.id
                WHERE
                    a.hrm_employee_id = $hrm_employee_id
                    AND a.punche_date BETWEEN '$from_date' AND '$to_date'
            ")[0]->late;

            $data['overtime_time'] = DB::SELECT("SELECT
                    SEC_TO_TIME( SUM( TIME_TO_SEC( a.overtime_time ) ) ) as overtime_time
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_attendance_status b ON a.attendance_status = b.id
                WHERE
                    a.hrm_employee_id = $hrm_employee_id
                    AND a.punche_date BETWEEN '$from_date' AND '$to_date'
            ")[0]->overtime_time;

            return response()->json(
                view('employee_profile.attendance_detail_data', $data)->render()
            );
        }

        return view('employee_profile.attendance_detail');
    }
}
