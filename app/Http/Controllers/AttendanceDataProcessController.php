<?php

namespace App\Http\Controllers;


use Auth;
use Crypt;
use Config;
use Session;
use App\User;
use DateTime;
use Redirect;
use Response;
use Validator;
use Datatables;
use Carbon\Carbon;
use App\Models\Log;
use App\Models\OTLog;
use App\Models\HrmLocation;
use Illuminate\Http\Request;
use App\Models\HrmAttendanceData;
use Illuminate\Support\Facades\DB;
use App\Models\HrmLocationInterval;


use App\Models\TmpHrmAttendanceData;
use App\Models\HrmChangeEmployeeShift;
use Illuminate\Support\Facades\Schema;
use App\Models\HrmAttendanceDataProcessLog;



class AttendanceDataProcessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('attendance.attendance_data_process');

        $user_id = auth()->id();
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");


        return view('attendance.attendance_data_process')
            ->with('default_user_location',  $default_user_location);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'location'     => 'required',
            'process_date' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'messages'    => $validator->getMessageBag()->toArray()
            ));
        }
        $process_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->process_date)));
        $CMdate       = date('m', strtotime($process_date));
        $CYdate       = date('Y', strtotime($process_date));
        $condition    = '';


        // $check_data_salary = DB::SELECT("SELECT * FROM pay_register a
        //                             JOIN
        //                             hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
        //                             AND b.hrm_location_id=$request->location
        //                             AND a.hrm_month_id = $CMdate
        //                             AND a.year_id = $CYdate
        //                             AND a.salary_genarate_type<>0
        //                             AND a.apply_for=1 LIMIT 1");



        // if (!empty($check_data_salary)){
        //     $messages = 'Salary (General) Delete , Then Reprocess' ;
        // }else{
        //     $condition = ' AND c.status = 1 ';
        // }

        // $check_data_cw = DB::SELECT("SELECT * FROM pay_register a
        //                             JOIN
        //                             hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
        //                             AND b.hrm_location_id=$request->location
        //                             AND a.hrm_month_id = $CMdate
        //                             AND a.year_id = $CYdate
        //                             AND a.salary_genarate_type<>0
        //                             AND a.apply_for=3 LIMIT 1");



        // if (!empty($check_data_cw)){
        //     $messages = $messages.'Salary Already Process This Months (CW) , First Delete CW Salary then Reprocess' ;
        // }else{
        //     if(!empty($condition)){
        //         $condition = ' AND c.status in (1,2) ';
        //     }else{
        //         $condition = ' AND c.status =2 ';
        //     }

        // }


        // if(empty($condition)){

        //     return Response::json(array(
        //         'success'           => false,
        //         'error_messages'    => true,
        //         'messages'          => 'All Employees Salary has been Processed , First delete salary then Reprocess '
        //     ));

        // }

        $condition = ' AND c.status in (1,2) ';

        $totalEmployee = DB::SELECT("SELECT
                                            COUNT(hrm_employee_id) totalEmployee,
                                            GROUP_CONCAT(hrm_employee_id) hrm_employee_id
                                        FROM
                                            (SELECT
                                                b.hrm_employee_id
                                            FROM
                                                hrm_employee_activity a
                                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                            JOIN hrm_employment_status c ON b.hrm_employment_status_id=c.id
                                            WHERE
                                                b.hrm_location_id = $request->location
                                                $condition
                                                    AND ('$process_date' >= a.start_date
                                                    AND '$process_date' <= a.end_date)
                                                    AND a.end_date IS NOT NULL
                                                    AND a.hrm_employee_activity_status_id NOT IN (7)
                                        UNION ALL
                                            SELECT
                                                b.hrm_employee_id
                                            FROM
                                                hrm_employee_activity a
                                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                            JOIN hrm_employment_status c ON b.hrm_employment_status_id=c.id
                                            WHERE
                                                b.hrm_location_id = $request->location
                                                $condition
                                                    AND a.end_date IS NULL
                                                    AND '$process_date' >= a.start_date
                                                    AND a.hrm_employee_activity_status_id NOT IN (7)) m")[0];

        if (!empty($totalEmployee->hrm_employee_id)) {


            // $punchDateEmployee = '2024-03-08';

            // for($r = 0; $r <5; $r++) {
            //  $info = $this->attandanceProcess($request->location,$punchDateEmployee,$totalEmployee->hrm_employee_id);
            //  $punchDateEmployee = date('Y-m-d', strtotime($punchDateEmployee . " +1 days"));
            // }

            // dd("end");
            $info = $this->attandanceProcess($request->location, $request->process_date, $totalEmployee->hrm_employee_id);
            // $info = $this->attandanceProcess($request->location, $request->process_date, 1813);

            return $info;
        } else {

            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => 'All Employees Salary has been Processed , First delete salary then Reprocess '
            ));
        }





        /*
            $totalEmployee = DB::SELECT("select count(hrm_employee_id)totalEmployee,group_concat(hrm_employee_id)hrm_employee_id
                                        from (SELECT b.hrm_employee_id FROM hrm_employee_activity a JOIN hrm_employee_job_info b
                                        ON a.hrm_employee_job_info_id = b.id
                                        WHERE  b.hrm_location_id = $request->location AND ('$value->punch_date' >= a.start_date AND '$value->punch_date' <= a.end_date) AND a.end_date IS NOT NULL
                                        AND a.hrm_employee_activity_status_id NOT IN (7)
                                        UNION ALL
                                        SELECT b.hrm_employee_id FROM hrm_employee_activity a JOIN hrm_employee_job_info b
                                        ON a.hrm_employee_job_info_id = b.id
                                        WHERE b.hrm_location_id = $request->location AND a.end_date IS NULL
                                        AND '$value->punch_date' >= a.start_date AND a.hrm_employee_activity_status_id NOT IN (7)
                                        )m")[0];

            $totalAttend = DB::SELECT("select count(id)totalAttend from hrm_attendance where hrm_location_id = $request->location and punche_date = '$value->punch_date'")[0];

            $employee_id = $totalEmployee->totalEmployee == $totalAttend->totalAttend ? $value->hrm_employee_id : $totalEmployee->hrm_employee_id;

            $x = $this->attandanceProcess($request->location,$value->punch_date,$employee_id);
        */

        // $punchDateEmployee = DB::SELECT("SELECT hrm_location_id,
        //                                         punch_date,
        //                                         GROUP_CONCAT(hrm_employee_id) hrm_employee_id,
        //                                         COUNT(1)number_of_employee
        //                                     FROM
        //                                         hrm_attendance_raw_data
        //                                     WHERE
        //                                         punch_date >= DATE(DATE_ADD(NOW(), INTERVAL - 31 DAY))
        //                                             AND valid = 1
        //                                             AND is_new = 1
        //                                             AND hrm_location_id = $request->location
        //                                             AND punch_date = '$process_date'
        //                                     GROUP BY punch_date");



        // $punchDateEmployee = DB::SELECT("SELECT aa.hrm_location_id,aa.punch_date,
        //                                 GROUP_CONCAT(aa.hrm_employee_id) hrm_employee_id,
        //                                 sum(number_of_employee) as number_of_employee
        //                                 FROM(SELECT hrm_location_id,
        //                                     punch_date,
        //                                     GROUP_CONCAT(hrm_employee_id) hrm_employee_id,
        //                                     COUNT(1)number_of_employee
        //                                 FROM
        //                                     hrm_attendance_raw_data
        //                                 WHERE
        //                                     punch_date >= DATE(DATE_ADD(NOW(), INTERVAL - 31 DAY))
        //                                         AND valid = 1
        //                                         AND is_new = 1
        //                                         AND hrm_location_id =$request->location
        //                                         AND punch_date = '$process_date'
        //                                 UNION
        //                                 SELECT hrm_location_id,
        //                                     '$process_date' as punch_date,
        //                                     GROUP_CONCAT(hrm_employee_id) hrm_employee_id,
        //                                     COUNT(1)number_of_employee
        //                                 FROM
        //                                     hrm_attendance_raw_data
        //                                 WHERE
        //                                     punch_date >= DATE(DATE_ADD(NOW(), INTERVAL - 31 DAY))
        //                                         AND valid = 1
        //                                         AND is_new = 1
        //                                         AND hrm_location_id = $request->location
        //                                         AND punch_date = DATE(DATE_ADD('$process_date', INTERVAL 1 DAY))
        //                                         AND hrm_employee_id in (SELECT
        //                                             a.hrm_employee_id
        //                                         FROM
        //                                             hrm_employee_job_info a
        //                                                 JOIN
        //                                             hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
        //                                                 AND a.hrm_location_id = $request->location
        //                                                 AND a.employee_activity = 1
        //                                                 AND '$process_date' BETWEEN b.start_date AND IFNULL(b.end_date, '$process_date')
        //                                                 AND b.end_date IS NULL
        //                                                 JOIN
        //                                             hrm_shift c ON b.hrm_shift_id = c.id
        //                                                 AND c.date_status = 2 UNION SELECT
        //                                             a.hrm_employee_id
        //                                         FROM
        //                                             hrm_employee_job_info a
        //                                                 JOIN
        //                                             hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
        //                                                 AND a.hrm_location_id = $request->location
        //                                                 AND a.employee_activity = 1
        //                                                 AND '$process_date' BETWEEN b.start_date AND IFNULL(b.end_date, '$process_date')
        //                                                 AND b.end_date IS NOT NULL
        //                                                 JOIN
        //                                             hrm_shift c ON b.hrm_shift_id = c.id
        //                                                 AND c.date_status = 2
        //                                         )) aa
        //                                 WHERE aa.hrm_location_id is not null
        //                                 GROUP BY aa.punch_date");



        // $totalEmployee = DB::SELECT("SELECT count(hrm_employee_id)totalEmployee,group_concat(hrm_employee_id)hrm_employee_id
        //                             from (SELECT b.hrm_employee_id FROM hrm_employee_activity a JOIN hrm_employee_job_info b
        //                             ON a.hrm_employee_job_info_id = b.id
        //                             WHERE  b.hrm_location_id = $request->location AND ('$process_date' >= a.start_date AND '$process_date' <= a.end_date) AND a.end_date IS NOT NULL
        //                             AND a.hrm_employee_activity_status_id NOT IN (7)
        //                             UNION ALL
        //                             SELECT b.hrm_employee_id FROM hrm_employee_activity a JOIN hrm_employee_job_info b
        //                             ON a.hrm_employee_job_info_id = b.id
        //                             WHERE b.hrm_location_id = $request->location AND a.end_date IS NULL
        //                             AND '$process_date' >= a.start_date AND a.hrm_employee_activity_status_id NOT IN (7)
        //                             )m")[0];

        // $totalAttend = DB::SELECT("SELECT count(id)totalAttend from hrm_attendance where hrm_location_id = $request->location and punche_date = '$process_date'")[0];

        // if(!empty($request->all_pro)){
        //     return $info = $this->attandanceProcess($request->location,$request->process_date,$totalEmployee->hrm_employee_id);
        // }


        // if(empty($punchDateEmployee) && ($totalEmployee->totalEmployee == $totalAttend->totalAttend)){
        //     return response::json(array(
        //         'success'   => false,
        //         'messages'  => 'This date attendance has already been processed!!'
        //     ));
        // }else{
        //     if($punchDateEmployee){
        //         $employee_id = $totalEmployee->totalEmployee == $totalAttend->totalAttend ? $punchDateEmployee[0]->hrm_employee_id : $totalEmployee->hrm_employee_id;
        //     }else{
        //         $employee_id = $totalEmployee->hrm_employee_id;
        //     }
        //     //dd($employee_id);
        //     $info = $this->attandanceProcess($request->location,$request->process_date,$employee_id);
        // }

        //        foreach ($punchDateEmployee as $key => $value) {
        //           $x = $this->attandanceProcess($request->location,$value->punch_date,$value->hrm_employee_id);
        //           //break;
        //        }





    }

    public function attandanceProcess($hrm_location_id, $date, $employees)
    {


        $user_id  = Auth::id();
        $date     = date('Y-m-d', strtotime($date));
        $day_name = Carbon::parse($date)->format('l');
        $location_info = HrmLocation::where('id', $hrm_location_id)->first();
        $current_date = date('Y-m-d');


        if ($location_info->is_wh_location_wise == 1) {

            $find_w_holiday = DB::SELECT("SELECT
                                    b.days_name
                                    FROM
                                        hrm_holiday_configure a
                                            JOIN
                                        hrm_days_name b ON a.hrm_days_name_id = b.id
                                    WHERE
                                        a.hrm_location_id = $hrm_location_id
                                            AND a.is_active = 1
                                            AND '$date' BETWEEN a.start_date AND ifnull(a.end_date,now())");

            if (empty($find_w_holiday)) {
                $wh = 'Friday';
            } else {
                $wh = DB::SELECT("SELECT
                                    b.days_name
                                    FROM
                                        hrm_holiday_configure a
                                            JOIN
                                        hrm_days_name b ON a.hrm_days_name_id = b.id
                                    WHERE
                                        a.hrm_location_id = $hrm_location_id
                                            AND a.is_active = 1
                                            AND '$date' BETWEEN a.start_date AND ifnull(a.end_date,now())
                                            AND b.days_name = '$day_name'");

                if (!empty($wh)) {
                    $wh = $wh[0]->days_name;
                } else {
                    $wh = '';
                }
            }
        }




        // Dynamic Table Create=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

        $dynamic_table = 'tmp_hrm_attendance_' . $user_id;

        // dd($dynamic_table);

        if (!Schema::hasTable($dynamic_table)) {

            Schema::create($dynamic_table, function ($table) {
                $table->increments('id');
                $table->integer('hrm_employee_id')->index();
                $table->date('punche_date')->nullable();
                $table->time('in_time')->nullable();
                $table->time('out_time')->nullable();
                $table->time('early_out_time')->nullable();
                $table->time('late_time')->nullable();
                $table->time('overtime_time')->nullable();
                $table->integer('attendance_status')->index();
                $table->string('comment')->nullable();
                $table->integer('hrm_shift_id')->index();
                $table->time('actual_overtime')->nullable();
                $table->integer('hrm_location_id')->index();
                $table->datetime('out_time_date')->nullable();
            });
        } else {
            DB::table($dynamic_table)->truncate();
        }

        $dynamic_datetime = date('Ymd_His').$user_id;
        $temp_vw_employee_late_summary = "temp_user_wise_view_" . intval($dynamic_datetime);
        $first_day = date('Y-m-01', strtotime($date));
      


        
      
        // Dynamic Table Create=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


        DB::beginTransaction();
        try {

         

            DB::DELETE("DELETE FROM tmp_hrm_attendance_data WHERE users_id=$user_id AND hrm_employee_id in ($employees)");

            $query_data    = HrmLocationInterval::where('hrm_location_id', $hrm_location_id)->first();
            $location_data = HrmLocation::where('id', $hrm_location_id)->where('overtime_eligibility', '>', 0)->first();

            if (empty($query_data)) {
                $intervaltime = 30;
                $gracetime = '00:10:59';
            } else {
                $intervaltime = $query_data->interval_time;
                $gracetime    = $query_data->grace_minute;
                $gracetime    = '00:' . $gracetime . ':59';
            }

            if (empty($location_data)) {
                $overtime_eligibility = '00:30:00';
                $ot_eligibility_include_exclude = 1;
            } else {
                $overtime_eligibility           = $location_data->overtime_eligibility;

                if ($overtime_eligibility > 59) {
                    $overtime_eligibility = intdiv($overtime_eligibility, 60) . ':' . ($overtime_eligibility  % 60) . ':' . '00';
                } else {
                    $overtime_eligibility           = '00:' . $overtime_eligibility . ':00';
                }

                $ot_eligibility_include_exclude = $location_data->ot_eligibility_include_exclude;
            }




            // need to change
            $this->GetEmployeeShift($date, $hrm_location_id, $employees, $intervaltime);

            // Resign employee er ekhane current date neya hoache because of protect wrong date process protect
            $this->GetResignEmployee($current_date, $hrm_location_id, $employees);



            //employee inactive

            DB::UPDATE("UPDATE hrm_employee_activity
                        JOIN(SELECT b.id,b.hrm_location_id,a.status,a.date_from FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to AND b.hrm_employee_id in ($employees)
                        )aa
                        ON hrm_employee_activity.hrm_employee_job_info_id = aa.id AND  hrm_employee_activity.end_date is null
                        SET hrm_employee_activity.end_date = DATE_SUB(aa.date_from, INTERVAL 1 DAY) ");


            DB::insert("INSERT INTO hrm_employee_activity (hrm_employee_job_info_id,
                        activity_date,activity,hrm_employee_activity_status_id,comment,users_id,start_date,end_date)
                        SELECT b.id,a.date_from,1,7,'Inactive',$user_id,a.date_from,a.date_from FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to AND b.hrm_employee_id in ($employees)");



            DB::UPDATE("UPDATE hrm_employee_job_info a INNER JOIN hrm_employee_inactive b ON b.hrm_employee_job_info_id=a.id
                        JOIN(SELECT b.id,b.hrm_location_id,a.status FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to AND b.hrm_employee_id in ($employees)
                        )aa
                        ON aa.id= a.id
                        SET a.employee_activity = 2,b.status=1");

            //end employee inactive



            $this->GetDataFromRaw($date, $hrm_location_id, $intervaltime, $employees);




            $this->ProcessLogData($date, $hrm_location_id, $employees);




            DB::DELETE("DELETE FROM  hrm_attendance
                        WHERE punche_date   = '$date'
                        AND   hrm_employee_id IN (SELECT hrm_employee_id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id) AND hrm_employee_id in ($employees)");


            DB::insert("INSERT INTO $dynamic_table (hrm_employee_id,hrm_shift_id,punche_date,in_time,out_time,early_out_time,late_time,overtime_time,attendance_status,comment,actual_overtime,hrm_location_id)
                        SELECT
                            a.hrm_employee_id,
                            b.hrm_shift_id,
                            '$date' AS punche_date,
                            '00:00:00' AS in_time,
                            '00:00:00' AS out_time,
                            '00:00:00' AS early_out_time,
                            '00:00:00' AS late,
                            '00:00:00' AS overtime,
                            0 AS attendance_status,
                            '' AS comment,
                            '00:00:00' AS actual_overtime,
                            '$hrm_location_id' as hrm_location_id

                        FROM
                            hrm_employee_job_info a
                                JOIN
                            tmp_employee_shift b ON a.hrm_employee_id = b.hrm_employee_id
                                AND a.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date AND hrm_employee_activity_status_id NOT IN (7))
                                AND b.process_date = '$date'
                                AND a.hrm_location_id = $hrm_location_id
                                AND b.hrm_location_id = $hrm_location_id
                                AND a.hrm_employee_id in ($employees)
                                AND a.hrm_employee_id NOT IN (SELECT
                                    hrm_employee_id
                                FROM
                                    hrm_attendance
                                WHERE
                                    punche_date = '$date') ");



            // end insert attendance

            //  set hrm_employee_shift
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT hrm_employee_id,hrm_shift_id,process_date
                            FROM
                            tmp_employee_shift
                            where process_date = '$date'
                            AND hrm_location_id   = $hrm_location_id
                            AND hrm_employee_id in ($employees)
                            GROUP BY hrm_employee_id,hrm_shift_id,process_date) aa ON
                        $dynamic_table.hrm_employee_id = aa.hrm_employee_id
                        AND $dynamic_table.punche_date = aa.process_date
                        AND $dynamic_table.punche_date = '$date'
                        SET $dynamic_table.hrm_shift_id = aa.hrm_shift_id");

            // Set in time & out time  333333333333333333333333333333
            // DB::update("UPDATE $dynamic_table
            //             JOIN(SELECT
            //                     a.hrm_employee_id,
            //                     (SELECT DATE_FORMAT(MIN(punch_date), '%H:%i:%s') FROM tmp_hrm_attendance_data WHERE attandance_date = a.attandance_date AND hrm_employee_id = a.hrm_employee_id AND punch_status = 0 GROUP BY hrm_employee_id,attandance_date) AS in_time,
            //                     (SELECT DATE_FORMAT(MAX(punch_date), '%H:%i:%s') FROM tmp_hrm_attendance_data WHERE attandance_date = a.attandance_date AND hrm_employee_id = a.hrm_employee_id AND punch_status = 1 GROUP BY hrm_employee_id,attandance_date) AS out_time
            //                 FROM
            //                     tmp_hrm_attendance_data a
            //                         JOIN
            //                     hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
            //                         AND b.hrm_employee_id in ($employees)
            //                         AND b.id in
            //                         (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
            //                         WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
            //                         UNION ALL
            //                         SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
            //                         AND '$date' >= start_date)
            //                         AND a.attandance_date = '$date'
            //                         AND b.hrm_location_id   = $hrm_location_id
            //                 GROUP BY a.hrm_employee_id , a.attandance_date) punchtime
            //             ON  $dynamic_table.hrm_employee_id = punchtime.hrm_employee_id
            //             AND $dynamic_table.punche_date     = '$date'
            //             SET $dynamic_table.in_time         = punchtime.in_time,
            //                 $dynamic_table.out_time        = punchtime.out_time");


            $location_info = HrmLocation::find($hrm_location_id);
            if ($location_info->shifting_rules == 2 || $location_info->shifting_rules == 3) {
                DB::update("UPDATE $dynamic_table
                    JOIN (
                        SELECT
                            a.hrm_employee_id,
                            a.in_time,
                            a.out_time,
                            IF(c.date_status = 2, DATE_ADD('$date', INTERVAL 1 DAY), '$date') AS out_time_date
                        FROM
                            hrm_employee_shift a
                        JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                            AND b.hrm_location_id = $hrm_location_id
                        JOIN hrm_shift c ON c.id = a.hrm_shift_id
                        AND a.start_date = '$date'

                    ) AS punchtime
                    ON $dynamic_table.hrm_employee_id = punchtime.hrm_employee_id
                    AND $dynamic_table.punche_date = '$date'
                    SET
                        $dynamic_table.in_time = punchtime.in_time,
                        $dynamic_table.out_time = punchtime.out_time,
                        $dynamic_table.out_time_date = punchtime.out_time_date
                ");
            } else {



                            // dd($datas);
                DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id,
                                DATE_FORMAT(MIN(a.punch_date), '%H:%i:%s') AS in_time,
                                CASE WHEN DATE_FORMAT(MIN(a.punch_date), '%H:%i:%s') = DATE_FORMAT(MAX(a.punch_date), '%H:%i:%s') THEN
                                    '00:00:00'
                                ELSE
                                    DATE_FORMAT(MAX(a.punch_date), '%H:%i:%s')
                                END out_time,

                                CASE WHEN DATE_FORMAT(MIN(a.punch_date), '%H:%i:%s') = DATE_FORMAT(MAX(a.punch_date), '%H:%i:%s') THEN
                                  null
                                ELSE
                                    MAX(a.punch_date)
                                END out_time_date

                            FROM
                                tmp_hrm_attendance_data a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.hrm_employee_id in ($employees)
                                    AND b.id in
                                    (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                    AND '$date' >= start_date)
                                    AND a.attandance_date = '$date'
                                    AND b.hrm_location_id   = $hrm_location_id
                            GROUP BY a.hrm_employee_id ) punchtime
                        ON  $dynamic_table.hrm_employee_id = punchtime.hrm_employee_id
                        AND $dynamic_table.punche_date     = '$date'
                        SET $dynamic_table.in_time         = punchtime.in_time,
                            $dynamic_table.out_time        = punchtime.out_time,
                            $dynamic_table.out_time_date   = punchtime.out_time_date");
            }


            // 1 for absent
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.id
                            FROM
                                $dynamic_table a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.hrm_employee_id in ($employees)
                                    AND b.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND b.hrm_location_id = $hrm_location_id
                                    AND a.in_time = '00:00:00'
                                    AND a.punche_date = '$date')
                                    absent ON $dynamic_table.id = absent.id
                        SET $dynamic_table.attendance_status = 1");

            // 2 for present
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.id
                            FROM
                                $dynamic_table a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.hrm_employee_id in ($employees)
                                    AND b.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND b.hrm_location_id = $hrm_location_id
                                    AND a.in_time <> '00:00:00'
                                    AND a.punche_date = '$date')
                                    absent ON $dynamic_table.id = absent.id
                        SET $dynamic_table.attendance_status = 2");

            // 6 for late
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.id, TIMEDIFF(a.in_time, c.start_time) AS late_time
                            FROM
                                $dynamic_table a
                                    JOIN
                                hrm_shift c ON a.hrm_shift_id = c.id
                                    AND a.hrm_shift_id = c.id
                                    AND a.punche_date = '$date'
                                    AND TIMEDIFF(a.in_time, c.start_time) > '$gracetime'
                                    JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                    AND d.hrm_employee_id in ($employees)
                                    AND d.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND d.hrm_location_id = $hrm_location_id)
                                    late ON $dynamic_table.id = late.id
                        SET $dynamic_table.attendance_status = 6,
                            $dynamic_table.late_time = late.late_time");





            $this->ProcessOT($date, $hrm_location_id, $employees);


            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                             FROM log_ot
                             WHERE attandance_date = '$date'
                             AND hrm_employee_id in ($employees)
                             AND hrm_location_id = $hrm_location_id) ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.overtime_time = ot.punch_time");



            // $ot_eligibility_include_exclude==1 For Including & $ot_eligibility_include_exclude==2 For Excluding
            if ($ot_eligibility_include_exclude == 1) {



                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND hrm_employee_id in ($employees)
                                    AND punch_time>'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = ot.punch_time");

                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND hrm_employee_id in ($employees)
                                    AND punch_time<'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = '00:00:00'");
            }


            if ($ot_eligibility_include_exclude == 2) {

                // dd($overtime_eligibility);
                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,SUBTIME(punch_time, '$overtime_eligibility') as punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND hrm_employee_id in ($employees)
                                    AND punch_time>'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = ot.punch_time");


                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND hrm_employee_id in ($employees)
                                    AND punch_time<'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = '00:00:00'");

                // actual_overtime
            }




            //EO is working is here .......... 16-05-2023
            //14 for EO
            $getEarlyOut = HrmLocation::findOrFail($hrm_location_id)->is_early_out;

            if ($getEarlyOut == 1) {
                DB::update("UPDATE $dynamic_table
                                    JOIN(SELECT
                                            a.id, TIMEDIFF(c.end_time,a.out_time) AS eo_time
                                        FROM
                                            $dynamic_table a
                                                JOIN
                                            hrm_shift c ON a.hrm_shift_id = c.id
                                                AND a.hrm_shift_id = c.id
                                                AND a.punche_date = '$date'
                                                AND a.in_time <> '00:00:00'
                                                AND TIMESTAMPDIFF(second,if(c.date_status=2, concat(DATE_ADD('$date', INTERVAL 1 DAY),' ',c.end_time) , concat('$date',' ',c.end_time) ) , ifnull(a.out_time_date, concat('$date',' ',a.in_time))) < 0
                                                JOIN
                                            hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                                AND d.hrm_employee_id in ($employees)
                                                AND d.id in
                                            (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                            AND '$date' >= start_date)
                                                AND d.hrm_location_id = $hrm_location_id)
                                                eoTime ON $dynamic_table.id = eoTime.id
                                    SET $dynamic_table.attendance_status = 14");
            }

            // EO is working is here .......... 16-05-2023 Working with this




            $holiday_from_workingday = DB::select("SELECT id FROM hrm_holidayconvert_to_working WHERE  action_date='$date' AND  valid = 1 AND status=1 AND hrm_location_id = $hrm_location_id");


            // 7 for Friday wh
            if (empty($holiday_from_workingday)) {

                if ($location_info->is_wh_location_wise == 1) {

                    if (!empty($wh)) {


                        DB::update("UPDATE $dynamic_table
                                    JOIN(SELECT
                                            a.id
                                        FROM
                                            $dynamic_table a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                AND b.hrm_employee_id in ($employees)
                                                AND b.id in
                                        (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND '$date' >= start_date)
                                                AND b.hrm_location_id = $hrm_location_id
                                                AND DAYNAME(a.punche_date) = '$wh'
                                                AND a.punche_date = '$date')
                                                friday ON $dynamic_table.id = friday.id
                                    SET $dynamic_table.attendance_status = 7");
                    }
                } else {

                    DB::update("UPDATE $dynamic_table
                                    JOIN(SELECT
                                    b.days_name,a.hrm_employee_id
                                    FROM
                                        hrm_holiday_configure a
                                            JOIN
                                        hrm_days_name b ON a.hrm_days_name_id = b.id
                                    WHERE
                                        a.hrm_location_id = $hrm_location_id
                                            AND a.is_active = 1
                                            AND '$date' BETWEEN a.start_date AND ifnull(a.end_date,now())
                                            AND b.days_name = '$day_name'
                                            AND a.hrm_employee_id in ($employees))
                                                whday ON $dynamic_table.hrm_employee_id = whday.hrm_employee_id
                                    SET $dynamic_table.attendance_status = 7");
                }
            }



            // 5 for osd if time is 00:00  4444444444444444444444444444444   1.5

            $gethrm_employee_id = DB::SELECT("SELECT
                group_concat(a.hrm_employee_id) as hrm_employee_id
            FROM
                hrm_attendance_raw_data a
                    JOIN
                hrm_attendance_comment b ON a.id = b.hrm_attendance_raw_data_id
                    AND a.hrm_employee_id in ($employees)
                    AND a.valid=1
                    AND a.hrm_location_id = $hrm_location_id
                    AND b.entry_status = 1
                    AND b.osd_time_status=0
                    AND a.punch_date='$date'");


            if (!empty($gethrm_employee_id[0]->hrm_employee_id)) {

                $gethrm_employee_id = $gethrm_employee_id[0]->hrm_employee_id;

                DB::UPDATE("Update $dynamic_table
                               SET attendance_status = 5,
                                   in_time='00:00:00',
                                   out_time='00:00:00',
                                   late_time='00:00:00',
                                   overtime_time='00:00:00'
                                WHERE punche_date='$date'
                                   AND hrm_employee_id IN ($gethrm_employee_id)");
            };







            // 5 for osd if time is come from attendance machine
            // 5 for osd   if time is manually input
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id, a.punch_date
                            FROM
                                hrm_attendance_raw_data a
                                    JOIN
                                hrm_attendance_comment b ON a.id = b.hrm_attendance_raw_data_id
                                    AND a.valid = 0
                                    AND b.entry_status = 1
                                    AND a.hrm_location_id = $hrm_location_id
                                    AND a.hrm_employee_id in ($employees)
                                    AND b.osd_time_status = 1
                                    AND a.punch_date='$date'
                            UNION ALL
                            SELECT
                                a.hrm_employee_id, a.punch_date
                            FROM
                                hrm_attendance_raw_data a
                                    JOIN
                                hrm_attendance_comment b ON a.id = b.hrm_attendance_raw_data_id
                                AND a.valid = 0
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.hrm_employee_id in ($employees)
                                AND b.entry_status = 1
                                AND b.osd_time_status = 2
                                AND a.punch_date='$date'
                                ) osd
                        ON $dynamic_table.hrm_employee_id = osd.hrm_employee_id
                        AND $dynamic_table.punche_date    = osd.punch_date
                        SET attendance_status = 5");



            // 8 for Holiday
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                b.hrm_employee_id
                            FROM
                                hrm_employee_holiday a
                                    JOIN
                                hrm_employee_holiday_details b ON a.id = b.hrm_employee_holiday_id
                                    AND a.valid=1
                                    AND a.apply_type = 2
                                    AND a.process_status = 2
                                    AND  '$date' >= a.date_from  AND '$date' <= a.date_to
                                    JOIN
                                hrm_employee_job_info c ON b.hrm_employee_id = c.hrm_employee_id
                                    AND c.hrm_location_id = $hrm_location_id
                                    AND c.hrm_employee_id in ($employees)
                                    AND c.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL

                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)) holiday
                        ON $dynamic_table.hrm_employee_id = holiday.hrm_employee_id
                        AND $dynamic_table.punche_date = '$date'
                        SET $dynamic_table.attendance_status = 8");


            $holiday = DB::select("SELECT id FROM hrm_employee_holiday
                                   WHERE apply_type = 1
                                   AND process_status = 2
                                   AND valid=1
                                   AND '$date' BETWEEN date_from AND date_to AND hrm_location_id = $hrm_location_id ");
            //AND hrm_employee_id in ($employees)

            // 8 for Holiday
            if (empty($holiday_from_workingday)) {
                if (!empty($holiday)) {

                    DB::update("UPDATE $dynamic_table
                                        JOIN(SELECT
                                                a.id
                                            FROM
                                                $dynamic_table a
                                                    JOIN
                                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                AND b.hrm_employee_id in ($employees)
                                                    AND b.id in
                                                    (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                    WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                                                    UNION ALL
                                                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                                                    AND '$date' >= start_date)
                                                    AND b.hrm_location_id = $hrm_location_id
                                                    AND a.punche_date = '$date')
                                                    holiday ON $dynamic_table.id = holiday.id
                                        SET $dynamic_table.attendance_status = 8");
                }
            }


            $convertWorkingday_from_holiday = DB::select("SELECT id FROM hrm_holidayconvert_to_working WHERE  action_date='$date' AND  valid = 1 AND status=2 AND hrm_location_id = $hrm_location_id "); //AND hrm_employee_id in ($employees)

            // 8 for Holiday
            if (empty($holiday_from_workingday)) {
                if (!empty($convertWorkingday_from_holiday)) {

                    DB::update("UPDATE $dynamic_table
                                        JOIN(SELECT
                                                a.id
                                            FROM
                                                $dynamic_table a
                                                    JOIN
                                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                    AND b.hrm_employee_id in ($employees)
                                                    AND b.id in
                                                    (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                    WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                                            UNION ALL
                                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                                            AND '$date' >= start_date)
                                                    AND b.hrm_location_id = $hrm_location_id
                                                    AND a.punche_date = '$date')
                                                    holiday ON $dynamic_table.id = holiday.id
                                        SET $dynamic_table.attendance_status = 8");

                    // DB::update("UPDATE hrm_attendance SET attendance_status = 8 WHERE  punche_date = '$date'  ");
                }
            }


            // 3 for leave with pay
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                JOIN
                                hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                                AND '$date' BETWEEN a.date_from AND a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 2
                                AND a.payment_mode = 1
                                AND c.leave_status = 1
                                JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                AND d.hrm_employee_id in ($employees)
                                AND d.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND d.hrm_location_id = $hrm_location_id) leaves
                        ON  $dynamic_table.hrm_employee_id  = leaves.hrm_employee_id
                        AND $dynamic_table.punche_date      = '$date'
                        SET attendance_status = 3 ");

            // 9 for without pay leave
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                AND '$date' BETWEEN a.date_from AND a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 2
                                AND a.payment_mode = 2
                                JOIN
                                hrm_employee_job_info c ON a.hrm_employee_id = c.hrm_employee_id
                                AND c.hrm_employee_id in ($employees)
                                AND c.id in
                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND c.hrm_location_id = $hrm_location_id) leaves
                        ON  $dynamic_table.hrm_employee_id  = leaves.hrm_employee_id
                        AND $dynamic_table.punche_date      = '$date'
                        SET attendance_status = 9,
                        in_time = '00:00:00',
                        out_time = '00:00:00' ");


            // 4 for h_leave with pay
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                JOIN
                                hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                                AND '$date' BETWEEN a.date_from AND a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 1
                                AND a.payment_mode = 1
                                AND c.leave_status = 1
                                JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                AND d.hrm_employee_id in ($employees)
                                AND d.id in
                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND d.hrm_location_id = $hrm_location_id) leavee
                        ON $dynamic_table.hrm_employee_id  = leavee.hrm_employee_id
                        AND $dynamic_table.punche_date = '$date'
                        SET attendance_status = 4 ");

            // 10 for h_leave without pay
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                AND '$date' BETWEEN a.date_from AND a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 1
                                AND a.payment_mode = 2
                                JOIN
                                hrm_employee_job_info c ON a.hrm_employee_id = c.hrm_employee_id
                                AND c.hrm_employee_id in ($employees)
                                AND c.id in
                               (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                    WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND c.hrm_location_id = $hrm_location_id) leavee
                        ON $dynamic_table.hrm_employee_id  = leavee.hrm_employee_id
                        AND $dynamic_table.punche_date = '$date'
                        SET attendance_status = 10 ");

            // 12 for Quater Day Leave With Pay
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                And  '$date' >= a.date_from AND '$date' <= a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 3
                                AND a.payment_mode = 1
                                JOIN
                                hrm_employee_job_info c ON a.hrm_employee_id = c.hrm_employee_id
                                AND c.hrm_employee_id in ($employees)
                                AND c.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND c.hrm_location_id = $hrm_location_id) leavee
                        ON $dynamic_table.hrm_employee_id  = leavee.hrm_employee_id
                        AND $dynamic_table.punche_date = '$date'
                        SET attendance_status = 12 ");

            // 13 for Quater Day Leave With Pay
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                AND  ('$date' >= a.date_from AND '$date' <=  a.date_to)
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 3
                                AND a.payment_mode = 2
                                JOIN
                                hrm_employee_job_info c ON a.hrm_employee_id = c.hrm_employee_id
                                AND c.hrm_employee_id in ($employees)
                                AND c.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND c.hrm_location_id = $hrm_location_id) leavee
                        ON $dynamic_table.hrm_employee_id  = leavee.hrm_employee_id
                        AND $dynamic_table.punche_date = '$date'
                        SET attendance_status = 13 ");




            // 11 for Holiday Against Leave
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id
                            FROM
                                hrm_leave_application a
                                JOIN
                                hrm_leave_approve b ON a.id = b.hrm_leave_application_id
                                JOIN
                                hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                                AND  '$date' >= a.date_from AND '$date' <= a.date_to
                                AND b.action_type = 1
                                AND b.forward = 2
                                AND a.duration = 2
                                AND a.payment_mode = 1
                                AND c.leave_status = 2
                                JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                AND d.hrm_employee_id in ($employees)
                                AND d.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND d.hrm_location_id = $hrm_location_id) leaves
                        ON  $dynamic_table.hrm_employee_id  = leaves.hrm_employee_id
                        AND $dynamic_table.punche_date      = '$date'
                        SET attendance_status = 11 ");


            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(a.working_hour))),'%T') AS ot_time,
                                a.hrm_employee_id
                            FROM
                                log a
                                    JOIN
                                $dynamic_table b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND a.punch_date    = b.punche_date
                                    AND b.punche_date   = '$date'
                                    AND b.attendance_status IN (7,8)
                                    JOIN
                                hrm_employee_job_info c ON b.hrm_employee_id = c.hrm_employee_id
                                    AND c.hrm_employee_id in ($employees)
                                    AND c.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND c.overtime_status = 1
                            GROUP BY a.hrm_employee_id) ot ON
                            $dynamic_table.hrm_employee_id   = ot.hrm_employee_id
                            AND $dynamic_table.punche_date   = '$date'
                            SET $dynamic_table.overtime_time = ot.ot_time");


            DB::update("UPDATE hrm_attendance_raw_data
                        JOIN(SELECT
                                row_data_id
                            FROM tmp_hrm_attendance_data
                            WHERE attandance_date = '$date' ) row_update
                            ON hrm_attendance_raw_data.id = row_update.row_data_id
                            AND hrm_attendance_raw_data.hrm_location_id = $hrm_location_id
                            AND hrm_employee_id in ($employees)
                        SET is_new = 0");

            // ommit overtime of employee who are taken holiday against leave

            DB::update("UPDATE $dynamic_table
                            JOIN(SELECT
                                    a.hrm_employee_id,a.holiday_date
                                FROM hrm_holiday_against_leave a
                                JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.hrm_employee_id in ($employees)
                                AND b.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                AND b.hrm_location_id = $hrm_location_id
                                AND holiday_date = '$date' AND valid=1 ) employee_holiday_take
                                ON $dynamic_table.hrm_employee_id = employee_holiday_take.hrm_employee_id
                                AND $dynamic_table.punche_date = employee_holiday_take.holiday_date
                            SET overtime_time = '00:00:00'");


            DB::update("UPDATE $dynamic_table
                                JOIN(SELECT
                                        a.hrm_employee_id,a.punch_date,a.overtime
                                    FROM hrm_manual_ot a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.hrm_employee_id in ($employees)
                                    AND b.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND b.hrm_location_id = $hrm_location_id
                                    AND punch_date = '$date' AND valid=1 ) hrm_manual_ot_list
                                    ON $dynamic_table.hrm_employee_id = hrm_manual_ot_list.hrm_employee_id
                                    AND $dynamic_table.punche_date = hrm_manual_ot_list.punch_date
                                SET overtime_time = hrm_manual_ot_list.overtime,actual_overtime=hrm_manual_ot_list.overtime");






            $getData = DB::SELECT("SELECT
                                    group_concat(aa.id) AS id
                                FROM
                                    hrm_attendance_data aa
                                    JOIN
                                    hrm_employee_job_info bb ON aa.hrm_employee_id = bb.hrm_employee_id
                                    AND bb.hrm_employee_id in ($employees)
                                    AND bb.hrm_location_id = $hrm_location_id
                                    AND aa.attandance_date = '$date'");


            if (!empty($getData[0]->id)) {
                $hrm_attendance_data_id = $getData[0]->id;
                DB::delete("DELETE FROM hrm_attendance_data WHERE id IN ($hrm_attendance_data_id) AND hrm_employee_id in ($employees)");
            }

            DB::INSERT("INSERT INTO hrm_attendance_data (attandance_date,punch_date,punch_time,data_from,row_data_id,hrm_employee_id,hrm_shift_id,punch_status)
                                    SELECT attandance_date,punch_date,punch_time,data_from,row_data_id,hrm_employee_id,hrm_shift_id,punch_status FROM tmp_hrm_attendance_data
                                    WHERE users_id = $user_id AND attandance_date='$date' AND hrm_employee_id in ($employees)");



            DB::INSERT("INSERT INTO hrm_attendance (hrm_employee_id,punche_date,in_time,out_time,early_out_time,late_time,overtime_time,attendance_status,comment,hrm_shift_id,actual_overtime,hrm_location_id)
                                    SELECT hrm_employee_id,punche_date,in_time,out_time,early_out_time,late_time,overtime_time,attendance_status,comment,hrm_shift_id,actual_overtime,hrm_location_id
                                     FROM $dynamic_table
                                    WHERE punche_date='$date' AND hrm_employee_id in ($employees)");






            DB::commit();

            // NEW OT RULES IMPLEMENT If getOTConfiq =2 Then only selectable employee will be allowed to get OT.

            $getOTConfiq = HrmLocation::findOrFail($hrm_location_id)->ot_rules;
            if (!empty($getOTConfiq)) {
                if ($getOTConfiq == 2) {

                    $ldate = date('Y-m-d H:i:s');

                    // Update not selectable ot employee '00:00' Which is manually confiqured =================================
                    DB::update("UPDATE hrm_attendance SET overtime_time='00:00:00' WHERE hrm_location_id = $hrm_location_id AND punche_date='$date' AND hrm_employee_id
                                     NOT IN (SELECT
                                                a.hrm_employee_id
                                            FROM
                                                hrm_eligible_ot_employee a
                                                    JOIN
                                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                    AND a.isActive=1
                                                    AND b.hrm_location_id = $hrm_location_id
                                                    AND a.apply_date = '$date'
                                                    AND b.overtime_status =1
                                                    Group by a.hrm_employee_id)");



                    DB::DELETE("DELETE FROM hrm_eligible_ot_pending WHERE apply_date='$date' AND hrm_employee_id in ($employees) AND isActive=1");
                    // dd("Working.Well");

                    // INSERT INTO hrm_eligible_ot_pending

                    DB::INSERT("INSERT INTO hrm_eligible_ot_pending (apply_date,hrm_employee_id,eligible_ot_hour,exceed_ot_hour,users_id,isActive,created_at,updated_at) SELECT
                                            '$date' as apply_date,
                                            a.hrm_employee_id,
                                            a.eligible_ot_hour,
                                            c.overtime_time as exceed_ot_hour,
                                            $user_id,
                                            1 as isActive,
                                            '$ldate',
                                            '$ldate'
                                        FROM
                                            hrm_eligible_ot_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                AND a.isActive = 1
                                                AND b.hrm_location_id = $hrm_location_id
                                                AND a.apply_date = '$date'
                                                AND b.overtime_status = 1
                                                AND b.hrm_employee_id in ($employees)
                                                JOIN
                                            hrm_attendance c ON b.hrm_employee_id = c.hrm_employee_id
                                                AND c.punche_date = a.apply_date
                                                AND b.hrm_location_id = c.hrm_location_id
                                                AND DATE_ADD(a.eligible_ot_hour, INTERVAL 30 MINUTE)<c.overtime_time
                                        GROUP BY a.hrm_employee_id ");


                    //Update selectable ot employees to allow ot  =================================
                    DB::UPDATE("UPDATE hrm_attendance JOIN (
                            SELECT
                                c.id,
                                a.hrm_employee_id,
                                a.eligible_ot_hour,
                                c.overtime_time,
                                a.apply_date
                            FROM
                                hrm_eligible_ot_employee a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND a.isActive = 1
                                    AND b.hrm_location_id = $hrm_location_id
                                    AND a.apply_date = '$date'
                                    AND b.overtime_status = 1
                                    AND b.hrm_employee_id in ($employees)
                                    JOIN
                                hrm_attendance c ON b.hrm_employee_id = c.hrm_employee_id
                                    AND c.punche_date = a.apply_date
                                    AND b.hrm_location_id = c.hrm_location_id
                                    AND a.eligible_ot_hour<c.overtime_time
                            GROUP BY a.hrm_employee_id ) aa ON hrm_attendance.id = aa.id
                            SET hrm_attendance.overtime_time = aa.eligible_ot_hour");
                };
            };
            // END NEW OT RULES IMPLEMENT If getOTConfiq =2 Then only selectable employee will be allowed to get OT.




            if ($location_info->allow_late_minutes !== null && $location_info->allow_late_minutes > 0) {



                         // 2. Data insert
                        $create_table_sql = "
                        CREATE TEMPORARY TABLE IF NOT EXISTS `$temp_vw_employee_late_summary` (
                            `hrm_employee_id` INT NOT NULL,
                            `latedays` INT NOT NULL,
                            `late_time` DECIMAL(10, 0) DEFAULT 0,
                            `hrm_month_id` TINYINT NOT NULL,
                            `year_id` SMALLINT NOT NULL
                        );";
                
                        // Create Table Statement Execute করা হচ্ছে
                        DB::statement($create_table_sql);
                        $latemin= $location_info->allow_late_minutes;
                       
                        $insert_sql = "
                            INSERT INTO `$temp_vw_employee_late_summary` (
                                `hrm_employee_id`,
                                `latedays`,
                                `late_time`,
                                `hrm_month_id`,
                                `year_id`
                            )
                            SELECT 
                                `hrm_attendance`.`hrm_employee_id`,
                                COUNT(`hrm_attendance`.`id`),
                                ROUND(SUM(TIME_TO_SEC(`hrm_attendance`.`late_time`)) / $latemin, 0),
                                MONTH(`hrm_attendance`.`punche_date`),
                                YEAR(`hrm_attendance`.`punche_date`)
                            FROM
                                `hrm_attendance`
                            WHERE
                                `hrm_attendance`.`punche_date` BETWEEN '$first_day' AND '$date'
                                AND `hrm_attendance`.`hrm_location_id` = 6
                                AND `hrm_attendance`.`attendance_status` = 6
                                AND `hrm_attendance`.`hrm_employee_id` IN ($employees) 
                            GROUP BY 
                                `hrm_attendance`.`hrm_employee_id`, 
                                YEAR(`hrm_attendance`.`punche_date`), 
                                MONTH(`hrm_attendance`.`punche_date`);";

                        
                        DB::statement($insert_sql);





                


                       $allow_late_minutes = $location_info->allow_late_minutes;
                       DB::update("UPDATE hrm_attendance
                        JOIN(SELECT
                                a.id, TIMEDIFF(a.in_time, c.start_time) AS late_time
                            FROM
                                hrm_attendance a
                                    JOIN
                                hrm_shift c ON a.hrm_shift_id = c.id
                                    AND a.hrm_shift_id = c.id
                                    AND a.punche_date = '$date'
                                    AND TIMEDIFF(a.in_time, c.start_time) > '$gracetime'
                                    JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                    AND d.hrm_employee_id in ($employees)
                                    AND d.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND d.hrm_location_id = $hrm_location_id
                                JOIN $temp_vw_employee_late_summary e
                                ON e.hrm_employee_id = d.hrm_employee_id
                                AND e.hrm_month_id = MONTH('$date')
                                AND e.year_id = YEAR('$date')
                                AND e.late_time >= $allow_late_minutes


                            )
                            late ON hrm_attendance.id = late.id

                            SET hrm_attendance.attendance_status = 1

                            WHERE hrm_attendance.attendance_status = 6;

                        ");
            }

            if ($location_info->allow_late_days !== null && $location_info->allow_late_days > 0) {


                       $allow_late_days = $location_info->allow_late_days;
                       DB::update("UPDATE hrm_attendance
                        JOIN(SELECT
                                a.id, TIMEDIFF(a.in_time, c.start_time) AS late_time
                            FROM
                                hrm_attendance a
                                    JOIN
                                hrm_shift c ON a.hrm_shift_id = c.id
                                    AND a.hrm_shift_id = c.id
                                    AND a.punche_date = '$date'
                                    AND TIMEDIFF(a.in_time, c.start_time) > '$gracetime'
                                    JOIN
                                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                    AND d.hrm_employee_id in ($employees)
                                    AND d.id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                    AND d.hrm_location_id = $hrm_location_id
                                JOIN $temp_vw_employee_late_summary e
                                ON e.hrm_employee_id = d.hrm_employee_id
                                AND e.hrm_month_id = MONTH('$date')
                                AND e.year_id = YEAR('$date')
                                AND e.latedays > $allow_late_days


                            )
                            late ON hrm_attendance.id = late.id

                            SET hrm_attendance.attendance_status = 1

                            WHERE hrm_attendance.attendance_status = 6;

                        ");
            }


            if ($location_info->is_eo_punishment !== null && $location_info->is_eo_punishment > 0) {

                
                        DB::update("UPDATE hrm_attendance
                                    JOIN(SELECT
                                            a.id
                                        FROM
                                            hrm_attendance a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                AND b.hrm_employee_id in ($employees)
                                                AND b.id in
                                        (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND '$date' >= start_date)
                                        AND b.hrm_location_id = $hrm_location_id
                                        AND a.punche_date = '$date'
                                        AND a.attendance_status = 14)
                                        eoData ON hrm_attendance.id = eoData.id
                                    SET hrm_attendance.attendance_status = 1");

                
            }



        } catch (\Exception $e) {
            DB::rollback();

            // DB::DELETE("DELETE  FROM hrm_attendance_data_process_log WHERE users_id = $user_id");

            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        // DB::DELETE("DELETE  FROM hrm_attendance_data_process_log WHERE users_id = $user_id");

        return response::json(array(
            'success'   => true,
            'messages'  => 'successfully attendance data process!'
        ));
    }

    public function GetResignEmployee($date, $hrm_location_id, $employees)
    {



        DB::update("UPDATE hrm_employee_job_info
                                JOIN
                            (SELECT
                                a.hrm_employee_job_info_id
                            FROM
                                hrm_employee_resignation a
                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                -- AND b.hrm_employee_id IN ($employees)
                                AND b.hrm_location_id = $hrm_location_id
                                AND a.effective_date < '$date'
                                AND a.resingnation_status = 4) aa ON hrm_employee_job_info.id = aa.hrm_employee_job_info_id
                        SET
                            hrm_employee_job_info.employee_activity = 0");


        DB::update("UPDATE hrm_employee_resignation
                                JOIN
                            (SELECT
                                a.hrm_employee_job_info_id
                            FROM
                                hrm_employee_resignation a
                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                -- AND b.hrm_employee_id IN ($employees)
                                AND b.hrm_location_id = $hrm_location_id
                                AND a.effective_date < '$date'
                                AND a.resingnation_status = 4) aa ON hrm_employee_resignation.hrm_employee_job_info_id = aa.hrm_employee_job_info_id
                        SET
                            hrm_employee_resignation.resingnation_status = 2");
    }


    public function GetEmployeeShift($date, $hrm_location_id, $employees, $intervaltime)
    {

        $location_info = HrmLocation::where('id', $hrm_location_id)->first();
        $user_id = auth()->user()->id;


        if ($location_info->shifting_rules == 2) {


            //====================== For Auto shift Start ==========================================


                DB::DELETE("DELETE FROM  hrm_employee_shift
                        WHERE start_date   = '$date'
                        AND   hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");


                // DB::DELETE("DELETE FROM  hrm_employee_shift
                //         WHERE start_date   = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                //         AND   hrm_employee_job_info_id IN (SELECT id
                //         FROM  hrm_employee_job_info
                //         WHERE id in
                //                 (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                //                 WHERE  (DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) <= end_date) AND end_date IS NOT NULL
                //                 UNION ALL
                //                 SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                //                 AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date )
                //         AND   hrm_location_id   = $hrm_location_id AND hrm_employee_id in ($employees))");


                DB::update("UPDATE hrm_employee_shift SET end_date = start_date
                        WHERE end_date IS NULL AND hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");




                $employee_ids = explode(',', $employees);

                $arr = [];
                $arr_data = [];

                foreach ($employee_ids as $employee) {
                    $prev_date = date('Y-m-d', strtotime($date . ' -1 day'));

                    $pre = DB::table('hrm_employee_shift as a')
                        ->join('hrm_employee_job_info as b', function ($join) use ($prev_date, $employee) {
                            $join->on('a.hrm_employee_job_info_id', '=', 'b.id')
                                ->where('b.hrm_employee_id', $employee)
                                ->where('a.start_date', $prev_date);
                        })
                        ->join('hrm_shift as c', 'a.hrm_shift_id', '=', 'c.id')
                        ->where('c.date_status', 2)
                        ->first();

                    if (isset($pre)) {
                        $shift_start_time = DB::table('hrm_attendance_raw_data')
                            ->select(DB::raw('MAX(punch_time) as in_time'))
                            ->where('valid',1)
                            ->where('hrm_employee_id', $employee)
                            ->where('punch_date', $date)
                            ->whereTime('punch_time', '>', '12:00:00')
                            ->first();



                        $in_time = $shift_start_time->in_time;

                        $shift = DB::table('hrm_shift')
                            ->select('id', 'shift_name', 'start_time', 'end_time', 'date_status')
                            ->where('date_status', '!=', 3)
                            ->where('valid',1)
                            ->where(function ($query) use ($in_time) {
                                $query->where(function ($q) use ($in_time) {

                                    $q->whereRaw("? BETWEEN SUBTIME(start_time, '02:00:00') AND ADDTIME(start_time, '02:00:00')", [$in_time]);
                                })->orWhere(function ($q) use ($in_time) {

                                    $q->whereRaw("start_time > end_time")
                                        ->where(function ($qq) use ($in_time) {
                                            $qq->whereRaw("? >= SUBTIME(start_time, '02:00:00')", [$in_time])
                                                ->orWhereRaw("? <= ADDTIME(start_time, '02:00:00')", [$in_time]);
                                        });
                                });
                            })
                            ->first();

                        if (isset($shift)) {
                            $data = DB::select("SELECT
                                        :shift_id AS hrm_shift_id,
                                        :date AS start_date,
                                        :date AS end_date,
                                        1 AS valid,
                                        a.hrm_employee_job_info_id,
                                        a.users_id,
                                        a.comment,
                                        CURDATE() AS created_at,
                                        CURDATE() AS updated_at,
                                        :in_time AS in_time,
                                        CASE
                                            WHEN :date_status = 1 THEN
                                                CASE
                                                    WHEN :in_time = (
                                                        SELECT MAX(punch_time)
                                                        FROM hrm_attendance_raw_data
                                                        WHERE hrm_employee_id = b.hrm_employee_id
                                                        AND valid = 1
                                                        AND punch_date = :date
                                                    ) THEN '00:00:00'
                                                    ELSE (
                                                        SELECT DATE_FORMAT(MAX(punch_time), '%H:%i:%s')
                                                        FROM hrm_attendance_raw_data
                                                        WHERE hrm_employee_id = b.hrm_employee_id
                                                        AND punch_date = :date
                                                    )
                                                END
                                            WHEN :date_status = 2 THEN (
                                                SELECT DATE_FORMAT(MIN(punch_time), '%H:%i:%s')
                                                FROM hrm_attendance_raw_data
                                                WHERE hrm_employee_id = b.hrm_employee_id
                                                AND valid = 1
                                                AND punch_date = DATE_ADD(:date, INTERVAL 1 DAY)
                                                AND TIME(punch_time) BETWEEN :end_time AND ADDTIME(:end_time, '06:00:00')
                                            )
                                            ELSE '00:00:00'
                                        END AS out_time,
                                        b.hrm_employee_id
                                    FROM hrm_employee_shift_auto a
                                    JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND b.employee_activity = 1
                                        AND b.hrm_location_id = :hrm_location_id
                                        AND b.hrm_employee_id = :employee_id
                                        AND :date BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                                    LIMIT 1
                                ", [
                                'shift_id' => $shift->id,
                                'date' => $date,
                                'in_time' => $in_time,
                                'date_status' => $shift->date_status,
                                'end_time' => $shift->end_time,
                                'hrm_location_id' => $hrm_location_id,
                                'employee_id' => $employee,
                            ]);


                            if (!empty($data)) {
                                if ($data[0]->in_time == $data[0]->out_time) {

                                    $data[0]->out_time = '00:00:00';
                                }
                                foreach ($data as $row) {
                                    $arr_data[] = (array) $row;
                                }
                                $arr[] = $employee;
                            }
                        }
                    } else {


                        $data =  DB::SELECT("SELECT
                            a.hrm_shift_id,
                            '$date' AS start_date,
                            '$date' AS end_date,
                            1 AS valid,
                            a.hrm_employee_job_info_id,
                            a.users_id,
                            a.comment,
                            CURDATE() AS created_at,
                            CURDATE() AS updated_at,
                            DATE_FORMAT(d.in_time, '%H:%i:%s') AS in_time,
                            CASE
                                WHEN c.date_status != 2 THEN
                                    CASE
                                        WHEN c.start_time = (
                                            SELECT MAX(punch_time)
                                            FROM hrm_attendance_raw_data
                                            WHERE hrm_employee_id = b.hrm_employee_id
                                            AND valid = 1
                                            AND punch_date = '$date'
                                        ) THEN '00:00:00'
                                        ELSE (
                                            SELECT DATE_FORMAT(MAX(punch_time), '%H:%i:%s')
                                            FROM hrm_attendance_raw_data
                                            WHERE hrm_employee_id = b.hrm_employee_id
                                            AND valid = 1
                                            AND punch_date = '$date'
                                        )
                                    END
                                WHEN c.date_status = 2 THEN (
                                    SELECT DATE_FORMAT(MIN(punch_time), '%H:%i:%s')
                                    FROM hrm_attendance_raw_data
                                    WHERE hrm_employee_id = b.hrm_employee_id
                                    AND valid = 1
                                    AND punch_date = DATE_ADD('$date', INTERVAL 1 DAY)
                                    AND TIME(punch_time) BETWEEN c.end_time AND ADDTIME(c.end_time, '06:00:00')
                                )
                                ELSE '00:00:00'
                            END AS out_time,
                            b.hrm_employee_id
                        FROM hrm_employee_shift_auto a
                        JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                            AND b.employee_activity = 1
                            AND b.hrm_location_id = $hrm_location_id
                            AND b.hrm_employee_id = $employee
                            AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                        JOIN hrm_shift c ON a.hrm_shift_id = c.id
                            AND c.valid = 1
                        JOIN (
                            SELECT
                                MIN(punch_time) AS in_time,
                                CASE
                                    WHEN MIN(punch_time) = MAX(punch_time) THEN '00:00:00'
                                    ELSE DATE_FORMAT(MAX(punch_time), '%H:%i:%s')
                                END AS out_time
                            FROM hrm_attendance_raw_data
                            WHERE hrm_employee_id = $employee
                            AND valid = 1
                            AND punch_date = '$date'
                        ) d ON (
                            (c.start_time < c.end_time AND TIME(d.in_time) BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '01:00:00'))
                            OR
                            (c.start_time > c.end_time AND (
                                TIME(d.in_time) >= SUBTIME(c.start_time, '01:00:00')
                                OR TIME(d.in_time) <= ADDTIME(c.start_time, '01:00:00')
                            ))
                        )
                        LIMIT 1
                    ");

                        if (!empty($data)) {
                            if ($data[0]->in_time == $data[0]->out_time) {
                                $data[0]->out_time = '00:00:00';
                            }
                            foreach ($data as $row) {
                                $arr_data[] = (array) $row;
                            }
                            $arr[] = $employee;
                        }

                    }

                }
                // dd($arr_data);

                DB::table('hrm_employee_shift')->insert($arr_data);



                $diff_ids = collect($employee_ids)->diff($arr);

                $default_shift = DB::table('hrm_shift')->where('date_status', 1)->first();


                if ($default_shift && $diff_ids->count() > 0) {

                    $employee_ids = $diff_ids->toArray();

                    $jobInfos = DB::table('hrm_employee_job_info')
                        ->select(
                            DB::raw($default_shift->id . ' AS hrm_shift_id'),
                            DB::raw("'$date' AS start_date"),
                            DB::raw("'$date' AS end_date"),
                            DB::raw('1 AS valid'),
                            'id as hrm_employee_job_info_id',
                            DB::raw(auth()->id() . ' AS users_id'),
                            DB::raw("'Auto Shifting' AS comment"),
                            DB::raw('NOW() AS created_at'),
                            DB::raw('NOW() AS updated_at'),
                            DB::raw("'00:00:00' AS in_time"),
                            DB::raw("'00:00:00' AS out_time"),
                            'hrm_employee_id'
                        )
                        ->where('employee_activity', 1)
                        ->whereIn('hrm_employee_id', $employee_ids)
                        ->groupBy('hrm_employee_id')
                        ->get()
                        ->map(fn($row) => (array) $row)
                        ->toArray();

                    DB::table('hrm_employee_shift')->insert($jobInfos);
                }

        }elseif($location_info->shifting_rules == 3){
             DB::DELETE("DELETE FROM  hrm_employee_shift
                        WHERE start_date   = '$date'
                        AND   hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");


                // DB::DELETE("DELETE FROM  hrm_employee_shift
                //         WHERE start_date   = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                //         AND   hrm_employee_job_info_id IN (SELECT id
                //         FROM  hrm_employee_job_info
                //         WHERE id in
                //                 (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                //                 WHERE  (DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) <= end_date) AND end_date IS NOT NULL
                //                 UNION ALL
                //                 SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                //                 AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date )
                //         AND   hrm_location_id   = $hrm_location_id AND hrm_employee_id in ($employees))");


                DB::update("UPDATE hrm_employee_shift SET end_date = start_date
                        WHERE end_date IS NULL AND hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");




                $employee_ids = explode(',', $employees);
                $prev_date = date('Y-m-d', strtotime($date . ' -1 day'));

               $users_id = auth()->id();

                $pre_data_status_2_employee_ids =  DB::table('hrm_employee_shift as a')
                ->whereIn('a.hrm_employee_id',$employee_ids)
                ->where('a.start_date',$prev_date)
                ->join('hrm_shift as b','b.id','=','a.hrm_shift_id')
                ->where('b.valid',1)
                ->where('b.date_status',2)
                ->get()->pluck('hrm_employee_id')->toArray();

                $pre_data_status_2_employee_ids = '(' . implode(',', $pre_data_status_2_employee_ids) . ')';



                        if( $pre_data_status_2_employee_ids !="()" ){


                            DB::insert("INSERT INTO hrm_employee_shift (
                                hrm_shift_id,
                                start_date,
                                end_date,
                                valid,
                                hrm_employee_job_info_id,
                                users_id,
                                comment,
                                created_at,
                                updated_at,
                                in_time,
                                out_time,
                                hrm_employee_id
                            )
                            SELECT * FROM (
                                SELECT
                                    (
                                        (
                                            SELECT id
                                            FROM hrm_shift
                                            WHERE d.in_time <= start_time
                                            AND start_time <= DATE_ADD(d.in_time, INTERVAL 3 HOUR)
                                            ORDER BY start_time
                                            LIMIT 1
                                        )
                                        UNION
                                        (
                                           SELECT id
                                            FROM hrm_shift
                                            WHERE d.in_time <= ADDTIME(start_time, '07:00:00')
                                            ORDER BY start_time
                                            LIMIT 1
                                        )
                                        UNION
                                        (
                                            SELECT id
                                            FROM hrm_shift
                                            WHERE SUBTIME(d.in_time, '00:59:59') BETWEEN start_time AND end_time
                                            ORDER BY start_time
                                            LIMIT 1
                                        )
                                        LIMIT 1
                                    ) AS hrm_shift_id,

                                    '$date' AS start_date,
                                    '$date' AS end_date,
                                    1 AS valid,
                                    b.id,
                                    '$users_id',
                                    'Auto Shifting',
                                    CURDATE() AS created_at,
                                    CURDATE() AS updated_at,
                                    d.in_time,
                                    d.out_time_final AS out_time,
                                    b.hrm_employee_id

                                FROM hrm_employee_job_info b

                                JOIN (
                                    SELECT
                                        hrm_employee_id,
                                        in_time,
                                        CASE
                                            WHEN out_time = '00:00:00' THEN (
                                                SELECT MAX(punch_time)
                                                FROM hrm_attendance_raw_data
                                                WHERE hrm_employee_id = t.hrm_employee_id
                                                    AND punch_date = DATE_ADD('$date', INTERVAL 1 DAY)
                                                    AND punch_time <= '12:00:00'
                                                    AND valid = 1
                                            )
                                            ELSE out_time
                                        END AS out_time_final
                                    FROM (
                                        SELECT
                                            hrm_employee_id,
                                            MIN(CASE WHEN punch_time >= '12:00:00' THEN punch_time END) AS in_time,
                                            CASE
                                                WHEN MIN(CASE WHEN punch_time >= '12:00:00' THEN punch_time END) = MAX(punch_time)
                                                THEN '00:00:00'
                                                ELSE MAX(punch_time)
                                            END AS out_time
                                        FROM hrm_attendance_raw_data
                                        WHERE hrm_employee_id IN $pre_data_status_2_employee_ids
                                            AND valid = 1
                                            AND punch_date = '$date'
                                        GROUP BY hrm_employee_id
                                    ) t
                                ) d ON b.hrm_employee_id = d.hrm_employee_id
                                 AND b.employee_activity = 1
                                    AND b.hrm_location_id = $hrm_location_id
                                    AND b.hrm_employee_id IN $pre_data_status_2_employee_ids

                            ) AS final_data
                            WHERE final_data.hrm_shift_id IS NOT NULL
                            GROUP BY final_data.hrm_employee_id
                        ");

                        }


                            DB::insert("INSERT INTO hrm_employee_shift (
                                hrm_shift_id,
                                start_date,
                                end_date,
                                valid,
                                hrm_employee_job_info_id,
                                users_id,
                                comment,
                                created_at,
                                updated_at,
                                in_time,
                                out_time,
                                hrm_employee_id
                            )
                            SELECT final_data.* FROM (
                                SELECT
                                    (
                                         SELECT hs.id
                                        FROM hrm_shift hs
                                        WHERE hs.valid = 1
                                        AND hs.start_time BETWEEN
                                                DATE_SUB(d.in_time, INTERVAL 5 HOUR)
                                                AND DATE_ADD(d.in_time, INTERVAL 5 HOUR)
                                        ORDER BY
                                            CASE
                                                WHEN hs.start_time >= d.in_time THEN 0  -- future shift first
                                                ELSE 1
                                            END,
                                            ABS(TIMESTAMPDIFF(SECOND, d.in_time, hs.start_time)) ASC
                                        LIMIT 1
                                    ) AS hrm_shift_id,

                                    '$date' AS start_date,
                                    '$date' AS end_date,
                                    1 AS valid,
                                    b.id,
                                    '$users_id',
                                    'auto shifting' as comment,
                                    CURDATE() AS created_at,
                                    CURDATE() AS updated_at,
                                    d.in_time,
                                    d.out_time,
                                    b.hrm_employee_id

                                FROM hrm_employee_job_info b


                                JOIN (
                                    SELECT
                                        hrm_employee_id,
                                        MIN(punch_time) AS in_time,
                                        CASE
                                            WHEN MIN(punch_time) = MAX(punch_time) THEN '00:00:00'
                                            ELSE MAX(punch_time)
                                        END AS out_time
                                    FROM hrm_attendance_raw_data
                                    WHERE hrm_employee_id IN ($employees)
                                        AND valid = 1
                                        AND punch_date = '$date'
                                    GROUP BY hrm_employee_id
                                ) d ON b.hrm_employee_id = d.hrm_employee_id
                                AND b.employee_activity = 1
                                AND b.hrm_location_id = $hrm_location_id
                                AND b.hrm_employee_id IN ($employees)


                                WHERE NOT EXISTS (
                                    SELECT 1 FROM hrm_employee_shift s
                                    WHERE s.hrm_employee_job_info_id = b.id
                                        AND s.start_date = '$date'
                                )
                            ) AS final_data
                            WHERE final_data.hrm_shift_id IS NOT NULL
                            GROUP BY final_data.id
                        ");



                    $default_shift = DB::table('hrm_shift')->where('date_status', 1)->where('valid',1)->first();
                     $jobInfos = DB::table('hrm_employee_job_info as a')
                        ->select(
                            DB::raw($default_shift->id . ' AS hrm_shift_id'),
                            DB::raw("'$date' AS start_date"),
                            DB::raw("'$date' AS end_date"),
                            DB::raw('1 AS valid'),
                            DB::raw('a.id AS hrm_employee_job_info_id'),
                            DB::raw(auth()->id() . ' AS users_id'),
                            DB::raw("'Auto Shifting' AS comment"),
                            DB::raw('NOW() AS created_at'),
                            DB::raw('NOW() AS updated_at'),
                            DB::raw("'00:00:00' AS in_time"),
                            DB::raw("'00:00:00' AS out_time"),
                            'a.hrm_employee_id'
                        )
                        ->where('a.employee_activity', 1)
                        ->whereIn('a.hrm_employee_id', $employee_ids)
                        ->whereNotExists(function ($query) use ($date) {
                            $query->select(DB::raw(1))
                                ->from('hrm_employee_shift as s')
                                ->whereRaw('s.hrm_employee_job_info_id = a.id')
                                ->where('s.start_date', $date);
                        })
                        ->groupBy('a.hrm_employee_id')
                        ->get()
                        ->map(fn($row) => (array) $row)
                        ->toArray();

                    DB::table('hrm_employee_shift')->insert($jobInfos);

        }
        //====================== Auto shift End ==========================================

        //DB::table('tmp_employee_shift')->where('hrm_location_id','=',$hrm_location_id)->delete();
        DB::DELETE("DELETE FROM tmp_employee_shift WHERE  hrm_location_id = $hrm_location_id AND hrm_employee_id in ($employees)");




        DB::insert("INSERT INTO tmp_employee_shift(process_date,hrm_shift_id,hrm_location_id,hrm_employee_id)
                        SELECT * FROM (
                        SELECT '$date' as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND '$date' BETWEEN b.start_date AND b.end_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NOT NULL
                        UNION
                        SELECT '$date' as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id

                                AND '$date' >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NULL
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) BETWEEN b.start_date AND b.end_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NOT NULL
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id

                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                b.id
                                            FROM
                                                hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                            WHERE
                                                b.hrm_employee_id IN ($employees) AND
                                                a.hrm_employee_activity_status_id not in (7) AND
                                                '$date' BETWEEN a.start_date AND a.end_date
                                                    AND end_date IS NOT NULL
                                            UNION ALL SELECT
                                               b.id
                                            FROM
                                                hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                            WHERE
                                                b.hrm_employee_id IN ($employees) AND
                                                a.hrm_employee_activity_status_id not in (7) AND
                                                a.end_date IS NULL
                                                    AND '$date' >= a.start_date)
                                AND b.end_date IS NULL) aa
                        GROUP BY aa.process_date,aa.hrm_location_id,aa.hrm_employee_id ");




        DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = '$date') aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = '$date'
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        AND tmp_employee_shift.hrm_employee_id in ($employees)
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");

        DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = DATE(DATE_ADD('$date', INTERVAL 1 DAY))) aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                        AND tmp_employee_shift.hrm_employee_id in ($employees)
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");
    }
    public function GetEmployeeShiftOld($date, $hrm_location_id, $employees, $intervaltime)
    {

        $location_info = HrmLocation::where('id', $hrm_location_id)->first();
        $user_id = auth()->user()->id;


        if ($location_info->shifting_rules == 2) {


            //====================== For Auto shift Start ==========================================

            DB::DELETE("DELETE FROM  hrm_employee_shift
                        WHERE start_date   = '$date'
                        AND   hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");


            DB::DELETE("DELETE FROM  hrm_employee_shift
                        WHERE start_date   = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                        AND   hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  (DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= start_date )
                        AND   hrm_location_id   = $hrm_location_id AND hrm_employee_id in ($employees))");


            DB::update("UPDATE hrm_employee_shift SET end_date = start_date
                        WHERE end_date IS NULL AND hrm_employee_job_info_id IN (SELECT id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date )
                        AND   hrm_location_id   = $hrm_location_id
                        AND hrm_employee_id in ($employees)
                        )");

            DB::INSERT("INSERT INTO hrm_employee_shift (hrm_shift_id, start_date, end_date, valid,  hrm_employee_job_info_id, users_id, comment, created_at, updated_at)
                                SELECT
                                    a.hrm_shift_id, '$date' as start_date, '$date'as end_date, 1, a.hrm_employee_job_info_id, a.users_id, a.comment, CURDATE() as created_at, CURDATE() as updated_at
                                FROM
                                    hrm_employee_shift_auto a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND b.employee_activity = 1
                                        AND b.hrm_location_id   = $hrm_location_id
                                        AND b.hrm_employee_id in ($employees)
                                        AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                                        JOIN
                                    hrm_shift c ON a.hrm_shift_id = c.id
                                        JOIN
                                            (SELECT
                                             MIN(punch_time) AS punch_time, hrm_employee_id
                                            FROM
                                                hrm_attendance_raw_data
                                            WHERE
                                                hrm_employee_id in ($employees)
                                                    AND punch_date = '$date'
                                                    group by hrm_employee_id
                                        ) aa ON b.hrm_employee_id = aa.hrm_employee_id
                                        AND aa.punch_time BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '04:00:00')
                                        AND aa.punch_time < c.start_time
                                UNION
                                SELECT
                                    a.hrm_shift_id, DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as start_date, DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as end_date, 1, a.hrm_employee_job_info_id, a.users_id, a.comment, CURDATE() as created_at, CURDATE() as updated_at
                                FROM
                                    hrm_employee_shift_auto a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND b.employee_activity = 1
                                        AND b.hrm_location_id   = $hrm_location_id

                                        AND b.hrm_employee_id in ($employees)
                                        AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                                        JOIN
                                    hrm_shift c ON a.hrm_shift_id = c.id
                                        JOIN
                                        (SELECT
                                        MIN(punch_time) AS punch_time, hrm_employee_id
                                        FROM
                                            hrm_attendance_raw_data
                                        WHERE
                                            hrm_employee_id in ($employees)
                                                AND punch_date = '$date'
                                                group by hrm_employee_id
                                    ) aa ON b.hrm_employee_id = aa.hrm_employee_id

                                    AND aa.punch_time BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '04:00:00')
                                    AND aa.punch_time < c.start_time ");



            // DB::INSERT("INSERT INTO hrm_employee_shift (hrm_shift_id, start_date, end_date, valid,  hrm_employee_job_info_id, users_id, comment, created_at, updated_at)
            //                     SELECT
            //                         a.hrm_shift_id, '$date' as start_date, '$date'as end_date, 1, a.hrm_employee_job_info_id, a.users_id, a.comment, CURDATE() as created_at, CURDATE() as updated_at
            //                     FROM
            //                         hrm_employee_shift_auto a
            //                             JOIN
            //                         hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
            //                             AND b.employee_activity = 1
            //                             AND b.hrm_location_id   = $hrm_location_id
            //                             AND b.hrm_employee_id in ($employees)
            //                             AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
            //                             JOIN
            //                         hrm_shift c ON a.hrm_shift_id = c.id
            //                             JOIN
            //                                 (SELECT
            //                                  MIN(punch_time) AS punch_time, hrm_employee_id
            //                                 FROM
            //                                     hrm_attendance_raw_data
            //                                 WHERE
            //                                     hrm_employee_id in ($employees)
            //                                         AND punch_date = '$date'
            //                                         group by hrm_employee_id
            //                             ) aa ON b.hrm_employee_id = aa.hrm_employee_id
            //                            AND (
            //                             (

            //                                 c.start_time < c.end_time
            //                                 AND TIME(aa.punch_time) BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '01:00:00')
            //                             )
            //                             OR
            //                             (

            //                                 c.start_time > c.end_time
            //                                 AND (
            //                                     TIME(aa.punch_time) >= SUBTIME(c.start_time, '01:00:00')
            //                                     OR TIME(aa.punch_time) <= ADDTIME(c.start_time, '01:00:00')
            //                                 )
            //                             )
            //                         )
            //                     UNION
            //                     SELECT
            //                         a.hrm_shift_id, DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as start_date, DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as end_date, 1, a.hrm_employee_job_info_id, a.users_id, a.comment, CURDATE() as created_at, CURDATE() as updated_at
            //                     FROM
            //                         hrm_employee_shift_auto a
            //                             JOIN
            //                         hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
            //                             AND b.employee_activity = 1
            //                             AND b.hrm_location_id   = $hrm_location_id

            //                             AND b.hrm_employee_id in ($employees)
            //                             AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
            //                             JOIN
            //                         hrm_shift c ON a.hrm_shift_id = c.id
            //                             JOIN
            //                             (SELECT
            //                                 MIN(punch_time) AS punch_time, hrm_employee_id
            //                                 FROM
            //                                     hrm_attendance_raw_data
            //                                 WHERE
            //                                     hrm_employee_id in ($employees)
            //                                         AND punch_date = '$date'
            //                                         group by hrm_employee_id
            //                             ) aa ON b.hrm_employee_id = aa.hrm_employee_id

            //                         AND (
            //                             (

            //                                 c.start_time < c.end_time
            //                                 AND TIME(aa.punch_time) BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '01:00:00')
            //                             )
            //                             OR
            //                             (

            //                                 c.start_time > c.end_time
            //                                 AND (
            //                                     TIME(aa.punch_time) >= SUBTIME(c.start_time, '01:00:00')
            //                                     OR TIME(aa.punch_time) <= ADDTIME(c.start_time, '01:00:00')
            //                                 )
            //                             )
            //                         ) ");
            DB::insert("INSERT INTO hrm_employee_shift (
                    hrm_shift_id,
                    start_date,
                    end_date,
                    valid,
                    hrm_employee_job_info_id,
                    users_id,
                    comment,
                    created_at,
                    updated_at
                )

                SELECT
                    a.hrm_shift_id,
                    '$date' AS start_date,
                    '$date' AS end_date,
                    1,
                    a.hrm_employee_job_info_id,
                    a.users_id,
                    a.comment,
                    CURDATE() AS created_at,
                    CURDATE() AS updated_at
                FROM hrm_employee_shift_auto a
                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                    AND b.employee_activity = 1
                    AND b.hrm_location_id = $hrm_location_id
                    AND b.hrm_employee_id IN ($employees)
                    AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                JOIN hrm_shift c ON a.hrm_shift_id = c.id
                JOIN (
                    SELECT
                        MIN(punch_time) AS punch_time,
                        hrm_employee_id
                    FROM hrm_attendance_raw_data
                    WHERE hrm_employee_id IN ($employees)
                        AND punch_date = '$date'
                    GROUP BY hrm_employee_id
                ) aa ON b.hrm_employee_id = aa.hrm_employee_id


                AND (
                    (c.start_time < c.end_time AND TIME(aa.punch_time) BETWEEN SUBTIME(c.start_time, '01:00:00') AND ADDTIME(c.start_time, '01:00:00'))
                    OR
                    (c.start_time > c.end_time AND (
                        TIME(aa.punch_time) >= SUBTIME(c.start_time, '01:00:00')
                        OR TIME(aa.punch_time) <= ADDTIME(c.end_time, '01:00:00')
                    ))
                )


                AND NOT (
                    c.date_status = 1 AND EXISTS (
                        SELECT 1
                        FROM hrm_employee_shift es
                        JOIN hrm_shift cs ON es.hrm_shift_id = cs.id
                        WHERE es.hrm_employee_job_info_id = a.hrm_employee_job_info_id
                            AND es.start_date = DATE_SUB('$date', INTERVAL 1 DAY)
                            AND cs.date_status = 2
                    )
                )

                UNION


                SELECT
                    b_shift.id AS hrm_shift_id,
                    '$date' AS start_date,
                    '$date' AS end_date,
                    1,
                    a.hrm_employee_job_info_id,
                    a.users_id,
                    a.comment,
                    CURDATE() AS created_at,
                    CURDATE() AS updated_at
                FROM hrm_employee_shift_auto a
                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                    AND b.employee_activity = 1
                    AND b.hrm_location_id = $hrm_location_id
                    AND b.hrm_employee_id IN ($employees)
                    AND '$date' BETWEEN a.start_date AND IFNULL(a.end_date, CURDATE())
                LEFT JOIN (
                    SELECT
                        MIN(punch_time) AS punch_time,
                        hrm_employee_id
                    FROM hrm_attendance_raw_data
                    WHERE hrm_employee_id IN ($employees)
                        AND punch_date = '$date'
                    GROUP BY hrm_employee_id
                ) aa ON b.hrm_employee_id = aa.hrm_employee_id
                JOIN hrm_shift b_shift ON b_shift.date_status = 3

                    WHERE aa.punch_time IS NULL
                ");











            DB::INSERT("INSERT INTO hrm_employee_shift (hrm_shift_id, start_date, end_date, valid,  hrm_employee_job_info_id, users_id, comment, created_at, updated_at)
                                SELECT
                                    (SELECT hrm_shift_id FROM hrm_employee_shift_auto WHERE hrm_employee_shift_auto.hrm_employee_job_info_id = hrm_employee_job_info.id AND hrm_employee_shift_auto.end_date is null limit 1) as hrm_shift_id,
                                    '$date' AS start_date,
                                    '$date' AS end_date,
                                    1,
                                    id as hrm_employee_job_info_id,
                                    $user_id as users_id,
                                    'AutoShift'  as comment,
                                    CURDATE() AS created_at,
                                    CURDATE() AS updated_at
                                FROM
                                    hrm_employee_job_info
                                WHERE
                                    employee_activity = 1
                                        AND hrm_location_id = $hrm_location_id
                                        AND id NOT IN (SELECT
                                            hrm_employee_job_info_id
                                        FROM
                                            hrm_employee_shift
                                        WHERE
                                            '$date' BETWEEN start_date AND end_date
                                                AND hrm_employee_job_info_id IN (SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE
                                                    ('$date' >= start_date
                                                        AND '$date' <= end_date)
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE
                                                    end_date IS NULL
                                                        AND '$date' >= start_date))

                                UNION
                                SELECT
                                    (SELECT hrm_shift_id FROM hrm_employee_shift_auto WHERE hrm_employee_shift_auto.hrm_employee_job_info_id = hrm_employee_job_info.id AND hrm_employee_shift_auto.end_date is null limit 1) as hrm_shift_id,
                                    DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as start_date,
                                    DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as end_date,
                                    1,
                                    id as hrm_employee_job_info_id,
                                    $user_id as users_id,
                                    'AutoShift'  as comment,
                                    CURDATE() AS created_at,
                                    CURDATE() AS updated_at
                                FROM
                                    hrm_employee_job_info
                                WHERE
                                    employee_activity = 1
                                        AND hrm_location_id = $hrm_location_id
                                        AND id NOT IN (SELECT
                                            hrm_employee_job_info_id
                                        FROM
                                            hrm_employee_shift
                                        WHERE
                                            '$date' BETWEEN start_date AND end_date
                                                AND hrm_employee_job_info_id IN (SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE
                                                    ('$date' >= start_date
                                                        AND '$date' <= end_date)
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE
                                                    end_date IS NULL
                                                        AND '$date' >= start_date)) ;");
        }
        //====================== Auto shift End ==========================================

        //DB::table('tmp_employee_shift')->where('hrm_location_id','=',$hrm_location_id)->delete();
        DB::DELETE("DELETE FROM tmp_employee_shift WHERE  hrm_location_id = $hrm_location_id AND hrm_employee_id in ($employees)");




        DB::insert("INSERT INTO tmp_employee_shift(process_date,hrm_shift_id,hrm_location_id,hrm_employee_id)
                        SELECT * FROM (
                        SELECT '$date' as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND '$date' BETWEEN b.start_date AND b.end_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NOT NULL
                        UNION
                        SELECT '$date' as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id

                                AND '$date' >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NULL
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) BETWEEN b.start_date AND b.end_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                            b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL
                                                        UNION ALL SELECT
                                                           b.id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            b.hrm_employee_id IN ($employees) AND
                                                            a.hrm_employee_activity_status_id not in (7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                                AND b.end_date IS NOT NULL
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)) as process_date,b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id

                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.id IN (SELECT
                                                b.id
                                            FROM
                                                hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                            WHERE
                                                b.hrm_employee_id IN ($employees) AND
                                                a.hrm_employee_activity_status_id not in (7) AND
                                                '$date' BETWEEN a.start_date AND a.end_date
                                                    AND end_date IS NOT NULL
                                            UNION ALL SELECT
                                               b.id
                                            FROM
                                                hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                            WHERE
                                                b.hrm_employee_id IN ($employees) AND
                                                a.hrm_employee_activity_status_id not in (7) AND
                                                a.end_date IS NULL
                                                    AND '$date' >= a.start_date)
                                AND b.end_date IS NULL) aa
                        GROUP BY aa.process_date,aa.hrm_location_id,aa.hrm_employee_id ");




        DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = '$date') aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = '$date'
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        AND tmp_employee_shift.hrm_employee_id in ($employees)
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");

        DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = DATE(DATE_ADD('$date', INTERVAL 1 DAY))) aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                        AND tmp_employee_shift.hrm_employee_id in ($employees)
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");
    }

    public function GetDataFromRawOld($date, $hrm_location_id, $intervaltime, $employees)
    {
        // data insert from raw data
        $punch_status       = 0;
        $hrm_employee_id    = 0;
        $intime             = 0;
        $outtime            = 0;
        // $employees = 8743;


        $emp_punch  =   DB::SELECT("SELECT /*+ MAX_EXECUTION_TIME(30000)*/ * FROM(SELECT
                                    a.hrm_employee_id,
                                    '$date',
                                    (CONCAT(a.punch_date, ' ', a.punch_time)) AS punch_date,
                                    a.punch_time,
                                    c.hrm_shift_id,
                                    a.id,
                                    1 AS data_from
                                FROM
                                    hrm_attendance_raw_data a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND a.valid = 1
                                    AND a.hrm_location_id = b.hrm_location_id
                                    AND a.punch_date >= '$date' and a.punch_date <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                                    AND a.hrm_location_id = $hrm_location_id
                                    AND b.hrm_employee_id in ($employees)
                                    AND b.id IN (SELECT
                                                  distinct  hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE '$date' >= start_date AND '$date' <= end_date AND end_date IS NOT NULL
                                                UNION ALL
                                                SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE end_date IS NULL AND '$date' >= start_date )
                                        JOIN
                                    tmp_employee_shift c ON a.hrm_employee_id = c.hrm_employee_id
                                        AND b.hrm_employee_id = c.hrm_employee_id
                                        AND c.process_date = '$date'
                                        AND c.hrm_location_id = $hrm_location_id
                                        JOIN
                                    hrm_shift d ON c.hrm_shift_id = d.id
                                        AND (CONCAT(a.punch_date, ' ', a.punch_time)) BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                                        AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                                        FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE a.hrm_employee_id = aa.hrm_employee_id
                                        AND aa.hrm_location_id = $hrm_location_id
                                        AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY)))))

                                UNION ALL
                                SELECT
                                    b.hrm_employee_id,
                                    '$date',
                                    a.created_at AS punch_date,
                                    DATE_FORMAT(a.created_at,'%H:%i:%s') punch_time,
                                    c.hrm_shift_id,
                                    a.id,
                                    1 AS data_from
                                FROM
                                    mobile_log a
                                        JOIN
                                    users bb ON a.user_id=bb.id AND bb.hrm_employee_id IS NOT NULL
                                        JOIN
                                    hrm_employee_job_info b ON bb.hrm_employee_id = b.hrm_employee_id
                                        AND a.valid = 1
                                        AND b.hrm_employee_id in ($employees)
                                        AND DATE_FORMAT(a.created_at, '%Y-%m-%d') >= '$date' and DATE_FORMAT(a.created_at, '%Y-%m-%d') <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                                        AND b.hrm_location_id = $hrm_location_id
                                        AND b.id IN (SELECT
                                                      distinct  hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                                    UNION ALL
                                                    SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE end_date IS NULL AND '$date' >= start_date)

                                        JOIN
                                    tmp_employee_shift c ON b.hrm_employee_id = c.hrm_employee_id
                                        AND bb.hrm_employee_id = c.hrm_employee_id
                                        AND c.process_date = '$date'
                                        AND c.hrm_location_id = $hrm_location_id
                                        JOIN
                                    hrm_shift d ON c.hrm_shift_id = d.id
                                        AND a.created_at BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                                        AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                                        FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE b.hrm_employee_id = aa.hrm_employee_id
                                        AND aa.hrm_location_id = $hrm_location_id
                                        AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))))) ) aa
                                        ORDER BY aa.hrm_employee_id , aa.punch_date ");


        foreach ($emp_punch as $keys) {

            if ($hrm_employee_id != $keys->hrm_employee_id) {
                $punch_status    = 0;
            }

            $insert[] = [
                'hrm_employee_id'           => $keys->hrm_employee_id,
                'attandance_date'           => $date,
                'punch_date'                => $keys->punch_date,
                'punch_time'                => $keys->punch_time,
                'hrm_shift_id'              => $keys->hrm_shift_id,
                'row_data_id'               => $keys->id,
                'data_from'                 => 1,
                'punch_status'              => $punch_status,
                'users_id'                  => Auth::user()->id
            ];

            $hrm_employee_id = $keys->hrm_employee_id;

            if ($punch_status == 0) {
                $punch_status    = 1;
            } else {
                $punch_status    = 0;
            }
        }



        if (!empty($insert)) {
            // HrmAttendanceData::insert($insert);
            TmpHrmAttendanceData::insert($insert);
            return true;
        } else {
            return false;
        }
    }

    public function GetDataFromRaw($date, $hrm_location_id, $intervaltime, $employees)
    {
        $insert = [];


        $location_info = HrmLocation::find($hrm_location_id);
        if ($location_info->shifting_rules == 2 || $location_info->shifting_rules == 3) {

            $employee_ids = explode(',', $employees);


                   $ids = DB::table('hrm_employee_shift')
                    ->where('start_date', $date)
                    ->whereIn('hrm_employee_id', $employee_ids)
                    ->where('comment', '=', 'Not Assign')
                    ->pluck('hrm_employee_id')
                    ->toArray();
                    // dd($ids);

                    DB::table("hrm_attendance_raw_data")
                        ->where('punch_date', $date)
                        ->whereNotIn('hrm_employee_id', $ids)
                        ->update(['is_new' => 0]);

                $data = DB::table('hrm_employee_shift')

                ->where('start_date', $date)
                ->whereIn('hrm_employee_id', $employee_ids)
                ->get();

            $insertData = [];




            foreach ($data as $row) {
                $insertData[] = [
                    'hrm_employee_id' => $row->hrm_employee_id,
                    'attandance_date' => $row->start_date,
                    'punch_date' => $row->start_date . ' ' . $row->in_time,
                    'punch_time' => $row->in_time,
                    'hrm_shift_id' => $row->hrm_shift_id,
                    'data_from' => 1,
                    'punch_status' => 0,
                    'users_id' => $row->users_id,
                ];

                if ($row->out_time !== '00:00:00') {
                    $next_day = DB::table('hrm_shift')->where('id',$row->hrm_shift_id)->first();
                    if($next_day->date_status == 2){
                        $insertData[] = [
                            'hrm_employee_id' => $row->hrm_employee_id,
                            'attandance_date' => $row->start_date,
                            'punch_date' => \Carbon\Carbon::parse($row->start_date)->addDay()->format('Y-m-d') . ' ' . $row->out_time,
                            'punch_time' => $row->out_time,
                            'hrm_shift_id' => $row->hrm_shift_id,
                            'data_from' => 1,
                            'punch_status' => 1,
                            'users_id' => $row->users_id,
                        ];
                    }else{
                        $insertData[] = [
                            'hrm_employee_id' => $row->hrm_employee_id,
                            'attandance_date' => $row->start_date,
                            'punch_date' => $row->start_date . ' ' . $row->out_time,
                            'punch_time' => $row->out_time,
                            'hrm_shift_id' => $row->hrm_shift_id,
                            'data_from' => 1,
                            'punch_status' => 1,
                            'users_id' => $row->users_id,
                        ];
                    }

                }
            }

            if (!empty($insertData)) {
                TmpHrmAttendanceData::insert($insertData);
            }



        }
         else {
            // data insert from raw data
            $punch_status       = 0;
            $hrm_employee_id    = 0;
            $intime             = 0;
            $outtime            = 0;
            // $employees = 8743;

            // $datas = DB::select("SELECT
            //             a.hrm_employee_id,
            //             '$date',
            //             (CONCAT(a.punch_date, ' ', a.punch_time)) AS punch_date,
            //             a.punch_time,
            //             c.hrm_shift_id,
            //             a.id,
            //             1 AS data_from
            //         FROM
            //             hrm_attendance_raw_data a
            //                 JOIN
            //             hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
            //             AND a.valid = 1
            //             AND a.hrm_location_id = b.hrm_location_id
            //             AND a.punch_date >= '$date' and a.punch_date <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
            //             AND a.hrm_location_id = $hrm_location_id
            //             AND b.hrm_employee_id in ($employees)
            //             AND b.id IN (SELECT
            //                             distinct  hrm_employee_job_info_id
            //                         FROM
            //                             hrm_employee_activity
            //                         WHERE '$date' >= start_date AND '$date' <= end_date AND end_date IS NOT NULL
            //                         UNION ALL
            //                         SELECT
            //                             hrm_employee_job_info_id
            //                         FROM
            //                             hrm_employee_activity
            //                         WHERE end_date IS NULL AND '$date' >= start_date )
            //                 JOIN
            //             tmp_employee_shift c ON a.hrm_employee_id = c.hrm_employee_id
            //                 AND b.hrm_employee_id = c.hrm_employee_id
            //                 AND c.process_date = '$date'
            //                 AND c.hrm_location_id = $hrm_location_id
            //                 JOIN
            //             hrm_shift d ON c.hrm_shift_id = d.id
            //                 AND (CONCAT(a.punch_date, ' ', a.punch_time)) BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
            //                 AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
            //                 FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE a.hrm_employee_id = aa.hrm_employee_id
            //                 AND aa.hrm_location_id = $hrm_location_id
            //                 -- AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
            //                 )))
            //                 ");
                            // dd($datas);




                // $emp_punch  =   DB::SELECT("SELECT /*+ MAX_EXECUTION_TIME(30000)*/ * FROM(

                //     SELECT
                //         a.hrm_employee_id,
                //         '$date',
                //         (CONCAT(a.punch_date, ' ', a.punch_time)) AS punch_date,
                //         a.punch_time,
                //         c.hrm_shift_id,
                //         a.id,
                //         1 AS data_from
                //     FROM
                //         hrm_attendance_raw_data a
                //             JOIN
                //         hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                //         AND a.valid = 1
                //         AND a.hrm_location_id = b.hrm_location_id
                //         AND a.punch_date >= '$date' and a.punch_date <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                //         AND a.hrm_location_id = $hrm_location_id
                //         AND b.hrm_employee_id in ($employees)
                //         AND b.id IN (SELECT
                //                         distinct  hrm_employee_job_info_id
                //                     FROM
                //                         hrm_employee_activity
                //                     WHERE '$date' >= start_date AND '$date' <= end_date AND end_date IS NOT NULL
                //                     UNION ALL
                //                     SELECT
                //                         hrm_employee_job_info_id
                //                     FROM
                //                         hrm_employee_activity
                //                     WHERE end_date IS NULL AND '$date' >= start_date )
                //             JOIN
                //         tmp_employee_shift c ON a.hrm_employee_id = c.hrm_employee_id
                //             AND b.hrm_employee_id = c.hrm_employee_id
                //             AND c.process_date = '$date'
                //             AND c.hrm_location_id = $hrm_location_id
                //             JOIN
                //         hrm_shift d ON c.hrm_shift_id = d.id
                //             AND (CONCAT(a.punch_date, ' ', a.punch_time)) BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                //             AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                //             FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE a.hrm_employee_id = aa.hrm_employee_id
                //             AND aa.hrm_location_id = $hrm_location_id
                //             -- AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                //             )))

                //     UNION ALL
                //     SELECT
                //         b.hrm_employee_id,
                //         '$date',
                //         a.created_at AS punch_date,
                //         DATE_FORMAT(a.created_at,'%H:%i:%s') punch_time,
                //         c.hrm_shift_id,
                //         a.id,
                //         1 AS data_from
                //     FROM
                //         mobile_log a
                //             JOIN
                //         users bb ON a.user_id=bb.id AND bb.hrm_employee_id IS NOT NULL
                //             JOIN
                //         hrm_employee_job_info b ON bb.hrm_employee_id = b.hrm_employee_id
                //             AND a.valid = 1
                //             AND b.hrm_employee_id in ($employees)
                //             AND DATE_FORMAT(a.created_at, '%Y-%m-%d') >= '$date' and DATE_FORMAT(a.created_at, '%Y-%m-%d') <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                //             AND b.hrm_location_id = $hrm_location_id
                //             AND b.id IN (SELECT
                //                             distinct  hrm_employee_job_info_id
                //                         FROM
                //                             hrm_employee_activity
                //                         WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                //                         UNION ALL
                //                         SELECT
                //                             hrm_employee_job_info_id
                //                         FROM
                //                             hrm_employee_activity
                //                         WHERE end_date IS NULL AND '$date' >= start_date
                //                         )

                //             JOIN
                //         tmp_employee_shift c ON b.hrm_employee_id = c.hrm_employee_id
                //             AND bb.hrm_employee_id = c.hrm_employee_id
                //             AND c.process_date = '$date'
                //             AND c.hrm_location_id = $hrm_location_id
                //             JOIN
                //         hrm_shift d ON c.hrm_shift_id = d.id
                //             AND a.created_at BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                //             AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                //             FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE b.hrm_employee_id = aa.hrm_employee_id
                //             AND aa.hrm_location_id = $hrm_location_id
                //             AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY)))))
                //             ) aa
                //             ORDER BY aa.hrm_employee_id , aa.punch_date
                //             ");
                // dd( $emp_punch);

                $emp_punch = DB::select("SELECT /*+ MAX_EXECUTION_TIME(30000)*/ * FROM(

                    SELECT
                        a.hrm_employee_id,
                        '$date' AS process_date,
                        (CONCAT(a.punch_date, ' ', a.punch_time)) AS punch_date,
                        a.punch_time,
                        c.hrm_shift_id,
                        a.id,
                        1 AS data_from
                    FROM
                        hrm_attendance_raw_data a
                            JOIN
                        hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        AND a.valid = 1
                        AND a.hrm_location_id = b.hrm_location_id
                        AND a.punch_date >= '$date' and a.punch_date <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                        AND a.hrm_location_id = $hrm_location_id
                        AND b.hrm_employee_id in ($employees)
                        AND b.id IN (SELECT
                                        distinct hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_activity
                                    WHERE '$date' >= start_date AND '$date' <= end_date AND end_date IS NOT NULL
                                    UNION ALL
                                    SELECT
                                        hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_activity
                                    WHERE end_date IS NULL AND '$date' >= start_date )
                            JOIN
                        tmp_employee_shift c ON a.hrm_employee_id = c.hrm_employee_id
                            AND b.hrm_employee_id = c.hrm_employee_id
                            AND c.process_date = '$date'
                            AND c.hrm_location_id = $hrm_location_id
                            JOIN
                        hrm_shift d ON c.hrm_shift_id = d.id
                            AND (CONCAT(a.punch_date, ' ', a.punch_time)) BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                            AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                            FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE a.hrm_employee_id = aa.hrm_employee_id
                            AND aa.hrm_location_id = $hrm_location_id
                            AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                            LIMIT 1)))

                    UNION ALL
                    SELECT
                        b.hrm_employee_id,
                        '$date' AS process_date,
                        a.created_at AS punch_date,
                        DATE_FORMAT(a.created_at,'%H:%i:%s') punch_time,
                        c.hrm_shift_id,
                        a.id,
                        1 AS data_from
                    FROM
                        mobile_log a
                            JOIN
                        users bb ON a.user_id=bb.id AND bb.hrm_employee_id IS NOT NULL
                            JOIN
                        hrm_employee_job_info b ON bb.hrm_employee_id = b.hrm_employee_id
                            AND a.valid = 1
                            AND b.hrm_employee_id in ($employees)
                            AND DATE_FORMAT(a.created_at, '%Y-%m-%d') >= '$date' and DATE_FORMAT(a.created_at, '%Y-%m-%d') <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                            AND b.hrm_location_id = $hrm_location_id
                            AND b.id IN (SELECT
                                            distinct hrm_employee_job_info_id
                                        FROM
                                            hrm_employee_activity
                                        WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                        UNION ALL
                                        SELECT
                                            hrm_employee_job_info_id
                                        FROM
                                            hrm_employee_activity
                                        WHERE end_date IS NULL AND '$date' >= start_date
                                        )

                            JOIN
                        tmp_employee_shift c ON b.hrm_employee_id = c.hrm_employee_id
                            AND bb.hrm_employee_id = c.hrm_employee_id
                            AND c.process_date = '$date'
                            AND c.hrm_location_id = $hrm_location_id
                            JOIN
                        hrm_shift d ON c.hrm_shift_id = d.id
                            AND a.created_at BETWEEN (CONCAT('$date',' ',DATE_SUB(d.start_time,INTERVAL $intervaltime MINUTE)))
                            AND (CONCAT(DATE(DATE_ADD('$date', INTERVAL 1 DAY)),' ',(SELECT DATE_SUB(start_time, INTERVAL $intervaltime MINUTE)
                            FROM tmp_employee_shift aa JOIN hrm_shift bb ON aa.hrm_shift_id = bb.id WHERE b.hrm_employee_id = aa.hrm_employee_id
                            AND aa.hrm_location_id = $hrm_location_id
                            AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                            LIMIT 1)))
                            ) aa
                            ORDER BY aa.hrm_employee_id , aa.punch_date
                            ");
            // dd($emp_punch);

            foreach ($emp_punch as $keys) {

                if ($hrm_employee_id != $keys->hrm_employee_id) {
                    $punch_status    = 0;
                }

                $insert[] = [
                    'hrm_employee_id'           => $keys->hrm_employee_id,
                    'attandance_date'           => $date,
                    'punch_date'                => $keys->punch_date,
                    'punch_time'                => $keys->punch_time,
                    'hrm_shift_id'              => $keys->hrm_shift_id,
                    'row_data_id'               => $keys->id,
                    'data_from'                 => 1,
                    'punch_status'              => $punch_status,
                    'users_id'                  => Auth::user()->id
                ];

                $hrm_employee_id = $keys->hrm_employee_id;

                if ($punch_status == 0) {
                    $punch_status    = 1;
                } else {
                    $punch_status    = 0;
                }
            }

            // dd($insert);

            if (!empty($insert)) {
                // dd($insert);
                // HrmAttendanceData::insert($insert);
                $isWork =  TmpHrmAttendanceData::insert($insert);
                return true;
            } else {
                return false;
            }
        }
    }

    public function ProcessLogData($date, $hrm_location_id, $employees)
    {

        DB::delete("DELETE FROM log WHERE punch_date = '$date' AND hrm_location_id = $hrm_location_id AND hrm_employee_id in ($employees)");

        $user_id = Auth::user()->id;

        $log_data   = DB::SELECT("SELECT
                                    a.hrm_employee_id,
                                    a.attandance_date,
                                    a.punch_date,
                                    a.punch_status
                                FROM
                                tmp_hrm_attendance_data a
                                    JOIN
                                hrm_employee_job_info c ON a.hrm_employee_id = c.hrm_employee_id
                                    AND a.users_id = $user_id
                                    AND a.attandance_date = '$date'
                                    AND c.hrm_location_id = $hrm_location_id
                                    AND c.hrm_employee_id in ($employees)
                                    AND c.id IN (SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                                    UNION ALL
                                                    SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE end_date IS NULL AND '$date' >= start_date )
                                ORDER BY a.hrm_employee_id , a.punch_date");

        $hrm_employee_id   = 0;
        $i                 = 0;
        $default_time      = $date . " 00:00:00";
        $dteEnd            = "00:00:00";
        $dteStart          = "00:00:00";
        // $insert            = array();
        foreach ($log_data as $keys) {

            if ($hrm_employee_id != $keys->hrm_employee_id) {
                $punch_status    = 0;
                $in_time         = 0;
                $out_time        = 0;
                $last_out_time   = '00:00:00';
                $outside_time    = '00:00:00';
                $hrm_employee_id = $keys->hrm_employee_id;
            }
            $i++;

            if ($keys->punch_status == 0) {
                $in_time         = $keys->punch_date;
            } else {
                $out_time        = $keys->punch_date;
            }
            $working_time        = '00:00:00';

            if (isset($log_data[$i])) {
                if ($hrm_employee_id != $log_data[$i]->hrm_employee_id) {
                    if ($out_time != 0 && $in_time != 0) {
                        $dteStart         = new DateTime($in_time);
                        $dteEnd           = new DateTime($out_time);
                        $dteDiff          = $dteStart->diff($dteEnd);
                        $working_time     = $dteDiff->format("%h:%i:%s");

                        $outside_time     = '00:00:00';
                        if ($last_out_time != '00:00:00') {
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }
                        $log_insert[] = [
                            'hrm_employee_id'   => $hrm_employee_id,
                            'punch_date'        => $date,
                            'in_datetime'       => $in_time,
                            'out_datetime'      => $out_time,
                            'working_hour'      => $working_time,
                            'hrm_location_id'   => $hrm_location_id,
                            'outside_time'      => $outside_time
                        ];

                        $last_out_time   = $dteEnd;
                        $in_time         = 0;
                        $out_time        = 0;
                    } else {
                        if ($keys->punch_status == 0) {
                            $in_time         = $keys->punch_date;
                        } else {
                            $in_time         = $default_time;
                        }
                        if ($keys->punch_status == 1) {
                            $out_time        = $keys->punch_date;
                        } else {
                            $out_time        = $default_time;
                        }

                        $outside_time     = '00:00:00';
                        if ($last_out_time != '00:00:00') {
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }
                        $log_insert[] = [
                            'hrm_employee_id'   => $hrm_employee_id,
                            'punch_date'        => $date,
                            'in_datetime'       => $in_time,
                            'out_datetime'      => $out_time,
                            'working_hour'      => $working_time,
                            'hrm_location_id'   => $hrm_location_id,
                            'outside_time'      => $outside_time
                        ];


                        $last_out_time   = $dteEnd;
                        $in_time         = 0;
                        $out_time        = 0;
                    }
                } else {
                    // its down
                    if ($out_time != 0 && $in_time != 0) {
                        $dteStart         = new DateTime($in_time);
                        $dteEnd           = new DateTime($out_time);
                        $dteDiff          = $dteStart->diff($dteEnd);
                        $working_time     = $dteDiff->format("%h:%i:%s");

                        $outside_time     = '00:00:00';
                        if ($last_out_time != '00:00:00') {
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }

                        $log_insert[] = [
                            'hrm_employee_id'   => $hrm_employee_id,
                            'punch_date'        => $date,
                            'in_datetime'       => $in_time,
                            'out_datetime'      => $out_time,
                            'working_hour'      => $working_time,
                            'hrm_location_id'   => $hrm_location_id,
                            'outside_time'      => $outside_time
                        ];

                        $last_out_time   = $dteEnd;
                        $in_time         = 0;
                        $out_time        = 0;
                    }
                }
            } else {

                if ($out_time != 0 && $in_time != 0) {
                    $dteStart         = new DateTime($in_time);
                    $dteEnd           = new DateTime($out_time);
                    $dteDiff          = $dteStart->diff($dteEnd);
                    $working_time     = $dteDiff->format("%h:%i:%s");
                } else {
                    if ($keys->punch_status == 0) {
                        $in_time         = $keys->punch_date;
                    } else {
                        $in_time         = $default_time;
                    }
                    if ($keys->punch_status == 1) {
                        $out_time        = $keys->punch_date;
                    } else {
                        $out_time        = $default_time;
                    }
                    $working_time     = '00:00:00';
                }

                $outside_time     = '00:00:00';
                if ($last_out_time != '00:00:00') {
                    $dteStart     = new DateTime($in_time);
                    $dteDiff      = $last_out_time->diff($dteStart);
                    $outside_time = $dteDiff->format("%h:%i:%s");
                }

                $log_insert[] = [
                    'hrm_employee_id'   => $hrm_employee_id,
                    'punch_date'        => $date,
                    'in_datetime'       => $in_time,
                    'out_datetime'      => $out_time,
                    'working_hour'      => $working_time,
                    'hrm_location_id'   => $hrm_location_id,
                    'outside_time'      => $outside_time
                ];
            }
        }
        // dd($log_insert);
        if (!empty($log_insert)) {
            Log::insert($log_insert);
            return true;
        } else {
            return false;
        }
    }


    public function ProcessOT($date, $hrm_location_id, $employees)
    {

        DB::delete("DELETE FROM hrm_ot_time WHERE attandance_date = '$date' AND hrm_location_id = $hrm_location_id AND hrm_employee_id in ($employees)");
        DB::delete("DELETE FROM log_ot      WHERE attandance_date = '$date' AND hrm_location_id = $hrm_location_id AND hrm_employee_id in ($employees)");


        $otCondition = '';
        $users_id = auth()->user()->id ?? DB::table('users')->first()->id;

        $ot_data  =  DB::SELECT("SELECT
                                    a.hrm_employee_id,
                                    b.hrm_location_id,
                                    (CONCAT(DATE(a.attandance_date), ' ', c.end_time)) AS shift_end_time,
                                    a.punch_date,
                                    a.punch_status
                                FROM
                                    tmp_hrm_attendance_data a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.overtime_status = 1
                                        AND a.attandance_date = '$date'
                                        AND b.hrm_location_id = $hrm_location_id
                                        AND b.hrm_employee_id in ($employees)
                                        AND a.users_id = $users_id
                                        AND b.id IN (SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                                    UNION ALL
                                                    SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE end_date IS NULL AND '$date' >= start_date )
                                        JOIN
                                    hrm_shift c ON a.hrm_shift_id = c.id
                                        AND c.date_status IN (1,3)
                                        AND a.punch_date >= CONCAT(a.attandance_date, ' ', c.end_time)
                                UNION ALL
                                SELECT
                                    a.hrm_employee_id,
                                    b.hrm_location_id,
                                    (CONCAT(DATE(DATE_ADD(a.attandance_date, INTERVAL 1 DAY)),' ',c.end_time)) AS shift_end_time,
                                    a.punch_date,
                                    a.punch_status
                                FROM
                                    tmp_hrm_attendance_data a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.overtime_status = 1
                                        AND a.attandance_date = '$date'
                                        AND b.hrm_location_id = $hrm_location_id
                                        AND b.hrm_employee_id in ($employees)
                                        AND a.users_id = $users_id
                                        AND b.id IN (SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                                    UNION ALL
                                                    SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE end_date IS NULL AND '$date' >= start_date )
                                        JOIN
                                    hrm_shift c ON a.hrm_shift_id = c.id
                                        AND c.date_status = 2
                                        AND a.punch_date >= (CONCAT(DATE(DATE_ADD(a.attandance_date, INTERVAL 1 DAY)),' ',c.end_time))
                                ORDER BY hrm_employee_id , punch_date ASC");

        $shift_end_time     = 0;
        $calculate          = 0;
        $ot_time            = 0;
        $lust_punch         = 0;
        $i                  = 0;
        $hrm_employee_id    = 0;


        foreach ($ot_data as $keys) {


            if ($hrm_employee_id != $keys->hrm_employee_id) {
                $calculate      = 0;
                $shift_end_time = 0;
                $ot_time        = 0;
                $lust_punch     = 0;
            }

            if ($calculate == 0) {
                $hrm_employee_id    = $keys->hrm_employee_id;
                $lust_punch         = $keys->punch_date;
                if ($keys->punch_status == 1) {
                    $ot_time += strtotime($keys->punch_date) - strtotime($keys->shift_end_time);
                }
                $calculate = 1;
            } else {
                if ($keys->punch_status == 0) {
                    $lust_punch         = $keys->punch_date;
                } else {

                    $ot_time    += strtotime($keys->punch_date) - strtotime($lust_punch);
                    $lust_punch  = $keys->punch_date;
                }
            }
            $i++;
            if (isset($ot_data[$i])) {
                if ($hrm_employee_id != $ot_data[$i]->hrm_employee_id) {
                    if ($ot_time > 0) {
                        $ottime   = $this->secToHR($ot_time);
                        $insert[] = [
                            'hrm_employee_id'           => $keys->hrm_employee_id,
                            'attandance_date'           => $date,
                            'punch_time'                => $ottime,
                            'hrm_location_id'           => $hrm_location_id
                        ];
                    }
                    $calculate          = 0;
                    $shift_end_time     = 0;
                    $ot_time            = 0;
                    $lust_punch         = 0;
                }
            } else {
                if ($ot_time > 0) {
                    $ottime   = $this->secToHR($ot_time);
                    $insert[] = [
                        'hrm_employee_id'           => $keys->hrm_employee_id,
                        'attandance_date'           => $date,
                        'punch_time'                => $ottime,
                        'hrm_location_id'           => $hrm_location_id
                    ];
                }
            }
        }

        // need to insert
        if (!empty($insert)) {

            OTLog::insert($insert);
            return true;
        } else {
            return false;
        }
    }

    public function secToHR($seconds)
    {
        $hours    = floor($seconds / 3600);
        $minutes  = floor(($seconds / 60) % 60);
        $seconds  = $seconds % 60;
        return "$hours:$minutes:$seconds";
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function interface_multiple()
    {
        $user_id = auth()->id();
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('attendance.attendance_data_process_multiple')
            ->with('default_user_location',  $default_user_location);
    }

    public function data_process_mutiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location'     => 'required',
            'date_from'    => 'required',
            'date_to'      => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'messages'    => 'Please fill all the required fields (Location & Date Range).'
            ));
        }

        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to   = date('Y-m-d', strtotime($request->date_to));

        if ($date_to < $date_from) {
            return Response::json(array(
                'success'   => false,
                'messages'  => 'To Date must be greater than or equal to From Date.'
            ));
        }

        $employees = (array) $request->employees;
        $employees = array_filter($employees, function ($value) {
            return is_numeric($value) && $value > 0;
        });

        if (empty($employees)) {
            $totalEmployee = DB::SELECT("SELECT
                                                GROUP_CONCAT(hrm_employee_id) hrm_employee_id
                                            FROM
                                                (SELECT
                                                    b.hrm_employee_id
                                                FROM
                                                    hrm_employee_activity a
                                                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                                JOIN hrm_employment_status c ON b.hrm_employment_status_id=c.id
                                                WHERE
                                                    b.hrm_location_id = $request->location
                                                    AND c.status in (1,2)
                                                        AND ('$date_from' >= a.start_date
                                                        AND '$date_from' <= a.end_date)
                                                        AND a.end_date IS NOT NULL
                                                        AND a.hrm_employee_activity_status_id NOT IN (7)
                                                UNION ALL
                                                    SELECT
                                                        b.hrm_employee_id
                                                    FROM
                                                        hrm_employee_activity a
                                                    JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                                    JOIN hrm_employment_status c ON b.hrm_employment_status_id=c.id
                                                    WHERE
                                                        b.hrm_location_id = $request->location
                                                        AND c.status in (1,2)
                                                            AND a.end_date IS NULL
                                                            AND '$date_from' >= a.start_date
                                                            AND a.hrm_employee_activity_status_id NOT IN (7)) m")[0];

            $allEmployees = $totalEmployee->hrm_employee_id ?? null;

            if (empty($allEmployees)) {
                return Response::json(array(
                    'success'   => false,
                    'messages'  => 'No active employee found for the selected location.'
                ));
            }

            $employee_ids = $allEmployees;
        } else {
            $employee_ids = implode(',', array_map('intval', $employees));
        }

        try {
            $from = new DateTime($date_from);
            $to   = new DateTime($date_to);

            for ($i = $from; $i <= $to; $i->modify('+1 day')) {
                $processDate = $i->format('Y-m-d');

                if (date('Y-m-d') < $processDate) {
                    break;
                }

                $this->attandanceProcess($request->location, $processDate, $employee_ids);
            }

            return Response::json(array(
                'success'   => true,
                'messages'  => 'successfully attendance data process for the selected employees and date range!'
            ));
        } catch (\Exception $e) {
            return Response::json(array(
                'success'   => false,
                'messages'  => 'insert problem !! ' . $e->getMessage()
            ));
        }
    }

    
}
