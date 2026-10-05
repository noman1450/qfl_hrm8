<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\HrmAttendanceData;
use App\Models\Log;
use App\Models\OTLog;
use App\User;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;
use Response;
use DateTime;
use Carbon\Carbon;


use App\Models\HrmLocationInterval;
use App\Models\HrmAttendanceDataProcessLog;
use App\Models\HrmLocation;
use App\Models\TmpHrmAttendanceData;



class AttendanceDataProcessController_1 extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    // NOMAN
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('attendance.attendance_data_process');

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");


        return view('attendance.attendance_data_process')
            -> with('default_user_location',  $default_user_location) ;
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

        // $user_id  = Auth::user()->id;

        // if($user_id==1){

        // }else{

        //     return Response::json(array(
        //         'success'   => false,
        //         'messages'    => 'System is Updating , Please try after 3 hours ...'
        //     ));

        // }





        $validator = Validator::make($request->all(), [
            'location'     =>'required',
            'process_date' =>'required',
        ]);

        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'messages'    => $validator->getMessageBag()->toArray()
            ));
        }


        $user_id  = Auth::user()->id;
        $date     = date('Y-m-d', strtotime(str_replace('/', '-', $request->process_date)));
        $day_name = Carbon::parse($date)->format('l');
        $hrm_location_id  = $request->location;
        $current_date = date('Y-m-d');


        // $this->ProcessLogData($date,$hrm_location_id);
        // if($date >= '2018-10-26'){
        //     dd("please contact with i-infotech business Solution. 01673201560");
        // }


        $checkDateYM = date('Y-m-d', strtotime("+1 months", strtotime($request->process_date)));
        $checkDateYM = new Carbon( $checkDateYM );




        $check_data = DB::SELECT("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$hrm_location_id
                                    AND a.hrm_month_id = $checkDateYM->month
                                    AND a.year_id = $checkDateYM->year
                                    AND a.apply_for=1
                                    AND a.salary_genarate_type<>0 LIMIT 1");

        if (!empty($check_data)){
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => 'you can not give back date data process!'
            ));
        }










        $find_w_holiday=DB::SELECT("SELECT
                                    b.days_name
                                    FROM
                                        hrm_holiday_configure a
                                            JOIN
                                        hrm_days_name b ON a.hrm_days_name_id = b.id
                                    WHERE
                                        a.hrm_location_id = $hrm_location_id
                                            AND a.is_active = 1
                                            AND '$date' BETWEEN a.start_date AND ifnull(a.end_date,now())");



        if(empty($find_w_holiday)){
            $wh = 'Friday';
        }else{
            $wh =DB::SELECT("SELECT
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

            if(!empty($wh)){$wh = $wh[0]->days_name;}else{$wh = '';}

        }


        // Dynamic Table Create=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            $dynamic_table = 'tmp_hrm_attendance_'.$user_id;

            // dd($dynamic_table);

            if (!Schema::hasTable($dynamic_table)) {

                Schema::create($dynamic_table, function($table)
                {
                    $table->increments('id');
                    $table->integer('hrm_employee_id');
                    $table->date('punche_date')->nullable();
                    $table->time('in_time')->nullable();
                    $table->time('out_time')->nullable();
                    $table->time('early_out_time')->nullable();
                    $table->time('late_time')->nullable();
                    $table->time('overtime_time')->nullable();
                    $table->integer('attendance_status')->nullable();
                    $table->string('comment')->nullable();
                    $table->integer('hrm_shift_id')->nullable();
                    $table->time('actual_overtime')->nullable();
                    $table->integer('hrm_location_id')->nullable();

                });

            }else{

                DB::table($dynamic_table)->truncate();
            }

            // dd('Hello');

        // Dynamic Table Create=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>





        DB::beginTransaction();
        try {


            // $find_user_data = DB::SELECT("SELECT id FROM hrm_attendance_data_process_log WHERE users_id = $user_id AND status=1");

            // if(!empty($find_user_data)){

            //      DB::commit();

            //     return Response::json(array(
            //         'success'           => false,
            //         'error_messages'    => true,
            //         'messages'          => 'Same user not accepted....!!',
            //     ));

            // }else{

            //     $find_user_location = DB::SELECT("SELECT id FROM hrm_attendance_data_process_log WHERE hrm_location_id = $hrm_location_id  AND status=1");

            //     if (!empty($find_user_location)) {

            //          DB::commit();

            //         return Response::json(array(
            //             'success'           => false,
            //             'error_messages'    => true,
            //             'messages'          => 'Some One Processing Data ,Please Wait.... !',
            //         ));

            //     }else{

            //         $AttDataProcessLog = new HrmAttendanceDataProcessLog;
            //         $AttDataProcessLog->hrm_location_id = $request->location;
            //         $AttDataProcessLog->status          = 1;
            //         $AttDataProcessLog->users_id        = auth()->user()->id;
            //         $AttDataProcessLog->save();

            //     };
            // };



            DB::DELETE("DELETE FROM tmp_hrm_attendance_data WHERE users_id=$user_id");




            $query_data    = HrmLocationInterval::where('hrm_location_id', $hrm_location_id)->first();
            $location_data = HrmLocation::where('id', $hrm_location_id)->where('overtime_eligibility', '>', 0)->first();

            if (empty($query_data)){
                $intervaltime = 30;
                $gracetime = '00:10:59';
            }else{
                $intervaltime = $query_data->interval_time;
                $gracetime    = $query_data->grace_minute;
                $gracetime    ='00:'.$gracetime.':59' ;
            }

            if (empty($location_data)){
                    $overtime_eligibility = '00:30:00';
                    $ot_eligibility_include_exclude = 1;
            }else{
                    $overtime_eligibility           = $location_data->overtime_eligibility;

                    if($overtime_eligibility>59){
                       $overtime_eligibility = intdiv($overtime_eligibility , 60).':'. ($overtime_eligibility  % 60).':'.'00';

                    }else{
                      $overtime_eligibility           = '00:'.$overtime_eligibility.':00' ;
                    }

                    $ot_eligibility_include_exclude = $location_data->ot_eligibility_include_exclude;
            }




            // need to change
            $this->GetEmployeeShift($date,$hrm_location_id);
            $this->GetResignEmployee($current_date,$hrm_location_id);



            //employee inactive

            DB::UPDATE("UPDATE hrm_employee_activity
                        JOIN(SELECT b.id,b.hrm_location_id,a.status,a.date_from FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to
                        )aa
                        ON hrm_employee_activity.hrm_employee_job_info_id = aa.id AND  hrm_employee_activity.end_date is null
                        SET hrm_employee_activity.end_date = DATE_SUB(aa.date_from, INTERVAL 1 DAY) ");


            DB::insert("INSERT INTO hrm_employee_activity (hrm_employee_job_info_id,
                        activity_date,activity,hrm_employee_activity_status_id,comment,users_id,start_date,end_date)
                        SELECT b.id,a.date_from,1,7,'Inactive',$user_id,a.date_from,a.date_from FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to ");



            DB::UPDATE("UPDATE hrm_employee_job_info a INNER JOIN hrm_employee_inactive b ON b.hrm_employee_job_info_id=a.id
                        JOIN(SELECT b.id,b.hrm_location_id,a.status FROM `hrm_employee_inactive` a JOIN hrm_employee_job_info b
                        ON a.hrm_employee_job_info_id=b.id WHERE a.status=0 and b.hrm_location_id=$hrm_location_id  and
                        '$date' between a.date_from and a.date_to
                        )aa
                        ON aa.id= a.id
                        SET a.employee_activity = 2,b.status=1");

            //end employee inactive



            $this->GetDataFromRaw($date,$hrm_location_id,$intervaltime);




            $this->ProcessLogData($date,$hrm_location_id);




            DB::DELETE("DELETE FROM  hrm_attendance
                        WHERE punche_date   = '$date'
                        AND   hrm_employee_id IN (SELECT hrm_employee_id
                        FROM  hrm_employee_job_info
                        WHERE id in
                                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                        AND   hrm_location_id   = $hrm_location_id)");


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
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date AND hrm_employee_activity_status_id NOT IN (7,5))
                                AND b.process_date = '$date'
                                AND a.hrm_location_id = $hrm_location_id
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
                            GROUP BY hrm_employee_id,hrm_shift_id,process_date) aa ON
                        $dynamic_table.hrm_employee_id = aa.hrm_employee_id
                        AND $dynamic_table.punche_date = aa.process_date
                        AND $dynamic_table.punche_date = '$date'
                        SET $dynamic_table.hrm_shift_id = aa.hrm_shift_id");


            // Set in time & out time  333333333333333333333333333333
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.hrm_employee_id,
                                (SELECT DATE_FORMAT(MIN(punch_date), '%H:%i:%s') FROM tmp_hrm_attendance_data WHERE attandance_date = a.attandance_date AND hrm_employee_id = a.hrm_employee_id AND punch_status = 0 GROUP BY hrm_employee_id,attandance_date) AS in_time,
                                (SELECT DATE_FORMAT(MAX(punch_date), '%H:%i:%s') FROM tmp_hrm_attendance_data WHERE attandance_date = a.attandance_date AND hrm_employee_id = a.hrm_employee_id AND punch_status = 1 GROUP BY hrm_employee_id,attandance_date) AS out_time
                            FROM
                                tmp_hrm_attendance_data a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.id in
                                    (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE  ('$date' >= start_date AND '$date' <= end_date) AND end_date IS NOT NULL
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                    AND '$date' >= start_date)
                                    AND a.attandance_date = '$date'
                                    AND b.hrm_location_id   = $hrm_location_id
                            GROUP BY a.hrm_employee_id , a.attandance_date) punchtime
                        ON  $dynamic_table.hrm_employee_id = punchtime.hrm_employee_id
                        AND $dynamic_table.punche_date     = '$date'
                        SET $dynamic_table.in_time         = punchtime.in_time,
                            $dynamic_table.out_time        = punchtime.out_time");


            // 1 for absent
            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT
                                a.id
                            FROM
                                $dynamic_table a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
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



            $this->ProcessOT($date,$hrm_location_id);


            DB::update("UPDATE $dynamic_table
                        JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                             FROM log_ot
                             WHERE attandance_date = '$date'
                             AND hrm_location_id = $hrm_location_id) ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.overtime_time = ot.punch_time");



            // $ot_eligibility_include_exclude==1 For Including & $ot_eligibility_include_exclude==2 For Excluding
            if($ot_eligibility_include_exclude==1){



                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND punch_time>'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = ot.punch_time");

                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND punch_time<'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = '00:00:00'");

            }


            if($ot_eligibility_include_exclude==2){

                // dd($overtime_eligibility);
                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,SUBTIME(punch_time, '$overtime_eligibility') as punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND punch_time>'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = ot.punch_time");


                DB::update("UPDATE $dynamic_table
                            JOIN(SELECT hrm_employee_id,attandance_date,punch_time
                                    FROM log_ot
                                    WHERE attandance_date = '$date'
                                    AND hrm_location_id = $hrm_location_id
                                    AND punch_time<'$overtime_eligibility') ot ON
                            $dynamic_table.hrm_employee_id = ot.hrm_employee_id
                            AND $dynamic_table.punche_date = ot.attandance_date
                            SET $dynamic_table.actual_overtime = '00:00:00'");

             // actual_overtime
            }





            $holiday_from_workingday = DB::select("SELECT id FROM hrm_holidayconvert_to_working WHERE  action_date='$date' AND  valid = 1 AND status=1 AND hrm_location_id = $hrm_location_id");

            // 7 for Friday wh
            if (empty($holiday_from_workingday)) {


                  if(!empty($wh)){



                        DB::update("UPDATE $dynamic_table
                                    JOIN(SELECT
                                            a.id
                                        FROM
                                            $dynamic_table a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
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



            }



            // 5 for osd if time is 00:00  4444444444444444444444444444444   1.5

          $gethrm_employee_id = DB::SELECT("SELECT
                group_concat(a.hrm_employee_id) as hrm_employee_id
            FROM
                hrm_attendance_raw_data a
                    JOIN
                hrm_attendance_comment b ON a.id = b.hrm_attendance_raw_data_id
                    AND a.valid=1
                    AND a.hrm_location_id = $hrm_location_id
                    AND b.entry_status = 1
                    AND b.osd_time_status=0
                    AND a.punch_date='$date'");


            if(!empty($gethrm_employee_id[0]->hrm_employee_id)){

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


            $convertWorkingday_from_holiday = DB::select("SELECT id FROM hrm_holidayconvert_to_working WHERE  action_date='$date' AND  valid = 1 AND status=2 AND hrm_location_id = $hrm_location_id ");

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
                        SET is_new = 0");

            // ommit overtime of employee who are taken holiday against leave

            DB::update("UPDATE $dynamic_table
                            JOIN(SELECT
                                    a.hrm_employee_id,a.holiday_date
                                FROM hrm_holiday_against_leave a
                                JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
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
                                    AND bb.hrm_location_id = $hrm_location_id
                                    AND aa.attandance_date = '$date'");


                            if(!empty($getData[0]->id)){
                                $hrm_attendance_data_id = $getData[0]->id;
                                DB::delete("DELETE FROM hrm_attendance_data WHERE id IN ($hrm_attendance_data_id)");
                            }

                            DB::INSERT("INSERT INTO hrm_attendance_data (attandance_date,punch_date,punch_time,data_from,row_data_id,hrm_employee_id,hrm_shift_id,punch_status)
                                    SELECT attandance_date,punch_date,punch_time,data_from,row_data_id,hrm_employee_id,hrm_shift_id,punch_status FROM tmp_hrm_attendance_data
                                    WHERE users_id = $user_id AND attandance_date='$date' ");



                            DB::INSERT("INSERT INTO hrm_attendance (hrm_employee_id,punche_date,in_time,out_time,early_out_time,late_time,overtime_time,attendance_status,comment,hrm_shift_id,actual_overtime,hrm_location_id)
                                    SELECT hrm_employee_id,punche_date,in_time,out_time,early_out_time,late_time,overtime_time,attendance_status,comment,hrm_shift_id,actual_overtime,hrm_location_id
                                     FROM $dynamic_table
                                    WHERE punche_date='$date' ");


        DB::commit();

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


    public function GetResignEmployee($date,$hrm_location_id){



            DB::update("UPDATE hrm_employee_job_info
                        JOIN(SELECT a.hrm_employee_job_info_id FROM hrm_employee_resignation a JOIN
                        hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                        AND b.hrm_location_id=$hrm_location_id AND a.effective_date<'$date'
                        AND a.resingnation_status=4) aa ON
                        hrm_employee_job_info.id = aa.hrm_employee_job_info_id
                        SET hrm_employee_job_info.employee_activity = 0");


            DB::update("UPDATE hrm_employee_resignation
                        JOIN(SELECT a.hrm_employee_job_info_id FROM hrm_employee_resignation a JOIN
                        hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                        AND b.hrm_location_id=$hrm_location_id AND a.effective_date<'$date'
                        AND a.resingnation_status=4) aa ON
                        hrm_employee_resignation.hrm_employee_job_info_id = aa.hrm_employee_job_info_id
                        SET hrm_employee_resignation.resingnation_status = 2");


    }


    public function GetEmployeeShift($date,$hrm_location_id){

            DB::table('tmp_employee_shift')->where('hrm_location_id','=',$hrm_location_id)->delete();

            DB::insert("INSERT INTO tmp_employee_shift(process_date,hrm_shift_id,hrm_location_id,hrm_employee_id)
                        SELECT '$date',b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND '$date' BETWEEN b.start_date AND b.end_date AND b.end_date IS NOT NULL
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                        UNION
                        SELECT '$date',b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND b.end_date IS NULL
                                AND '$date' >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            '$date' BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            a.end_date IS NULL
                                                                AND '$date' >= a.start_date)
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)),b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) BETWEEN b.start_date AND b.end_date AND b.end_date IS NOT NULL
                                AND a.hrm_location_id = $hrm_location_id
                                    AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            DATE(DATE_ADD('$date', INTERVAL 1 DAY)) BETWEEN a.start_date AND a.end_date
                                                                AND end_date IS NOT NULL UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.hrm_employee_activity_status_id not in (5,7) AND
                                                            a.end_date IS NULL
                                                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= a.start_date)
                        UNION
                        SELECT DATE(DATE_ADD('$date', INTERVAL 1 DAY)),b.hrm_shift_id,a.hrm_location_id,a.hrm_employee_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_shift b ON a.id = b.hrm_employee_job_info_id
                                AND b.end_date IS NULL
                                AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= b.start_date
                                AND a.hrm_location_id = $hrm_location_id
                                 AND a.hrm_employee_id IN (SELECT
                                                    b.hrm_employee_id
                                                FROM
                                                    hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                WHERE
                                                    a.hrm_employee_activity_status_id not in (5,7) AND
                                                    DATE(DATE_ADD('$date', INTERVAL 1 DAY)) BETWEEN a.start_date AND a.end_date
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                   b.hrm_employee_id
                                                FROM
                                                    hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                WHERE
                                                    a.hrm_employee_activity_status_id not in (5,7) AND
                                                    a.end_date IS NULL
                                                        AND DATE(DATE_ADD('$date', INTERVAL 1 DAY)) >= a.start_date)");


            DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = '$date') aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = '$date'
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");

            DB::update("UPDATE tmp_employee_shift
                        JOIN(SELECT hrm_employee_id,hrm_shift_id
                            FROM  hrm_change_employee_shift
                            where punch_date  = DATE(DATE_ADD('$date', INTERVAL 1 DAY))) aa ON
                        tmp_employee_shift.hrm_employee_id = aa.hrm_employee_id
                        AND tmp_employee_shift.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                        AND tmp_employee_shift.hrm_location_id = $hrm_location_id
                        SET tmp_employee_shift.hrm_shift_id = aa.hrm_shift_id");

    }

    public function GetDataFromRaw($date,$hrm_location_id,$intervaltime){
        // data insert from raw data
        $punch_status       = 0;
        $hrm_employee_id    = 0;
        $intime             = 0;
        $outtime            = 0;


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
                                    AND b.id IN (SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                -- WHERE '$date' BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                                WHERE '$date' >= start_date AND '$date' <= end_date AND end_date IS NOT NULL
                                                UNION ALL
                                                SELECT
                                                    hrm_employee_job_info_id
                                                FROM
                                                    hrm_employee_activity
                                                WHERE end_date IS NULL AND '$date' >= start_date)

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
                                        -- AND DATE_FORMAT(a.created_at, '%Y-%m-%d') BETWEEN '$date' AND DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                                        AND DATE_FORMAT(a.created_at, '%Y-%m-%d') >= '$date' and DATE_FORMAT(a.created_at, '%Y-%m-%d') <= DATE(DATE_ADD('$date', INTERVAL 1 DAY))
                                        AND b.hrm_location_id = $hrm_location_id
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
                                        AND aa.process_date = DATE(DATE_ADD('$date', INTERVAL 1 DAY))))) ) aa
                                        ORDER BY aa.hrm_employee_id , aa.punch_date ");




        foreach($emp_punch as $keys) {

            if($hrm_employee_id != $keys->hrm_employee_id){
                $punch_status    = 0;
            }

            $insert[] = ['hrm_employee_id'           => $keys->hrm_employee_id,
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

            if($punch_status == 0){
                $punch_status    = 1;
            }else{
                $punch_status    = 0;
            }
        }



        if(!empty($insert)){
            // HrmAttendanceData::insert($insert);
            TmpHrmAttendanceData::insert($insert);
            return true;
        }else{
            return false;
        }

    }

    public function ProcessLogData($date,$hrm_location_id){

        DB::delete("DELETE FROM log WHERE punch_date = '$date' AND hrm_location_id = $hrm_location_id");

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
                                                    WHERE end_date IS NULL AND '$date' >= start_date)
                                ORDER BY a.hrm_employee_id , a.punch_date");

        $hrm_employee_id   = 0;
        $i                 = 0;
        $default_time      = $date." 00:00:00";
        $dteEnd            = "00:00:00";
        $dteStart          = "00:00:00";
        // $insert            = array();
        foreach($log_data as $keys) {

            if($hrm_employee_id != $keys->hrm_employee_id){
                $punch_status    = 0;
                $in_time         = 0;
                $out_time        = 0;
                $last_out_time   = '00:00:00';
                $outside_time    = '00:00:00';
                $hrm_employee_id = $keys->hrm_employee_id;
            }
            $i++;

            if($keys->punch_status == 0){
                $in_time         = $keys->punch_date;
            }else{
                $out_time        = $keys->punch_date;
            }
            $working_time        = '00:00:00';

            if(isset($log_data[$i])){
                if($hrm_employee_id != $log_data[$i]->hrm_employee_id){
                    if($out_time != 0 && $in_time != 0){
                        $dteStart         = new DateTime($in_time);
                        $dteEnd           = new DateTime($out_time);
                        $dteDiff          = $dteStart->diff($dteEnd);
                        $working_time     = $dteDiff->format("%h:%i:%s");

                        $outside_time     = '00:00:00';
                        if($last_out_time != '00:00:00'){
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }
                        $log_insert[] = ['hrm_employee_id'   => $hrm_employee_id,
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
                    }else{
                        if($keys->punch_status == 0){
                            $in_time         = $keys->punch_date;
                        }else{
                            $in_time         = $default_time;
                        }
                        if($keys->punch_status == 1){
                            $out_time        = $keys->punch_date;
                        }else{
                            $out_time        = $default_time;
                        }

                        $outside_time     = '00:00:00';
                        if($last_out_time != '00:00:00'){
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }
                        $log_insert[] = ['hrm_employee_id'   => $hrm_employee_id,
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
                }else{
                    // its down
                    if($out_time != 0 && $in_time != 0){
                        $dteStart         = new DateTime($in_time);
                        $dteEnd           = new DateTime($out_time);
                        $dteDiff          = $dteStart->diff($dteEnd);
                        $working_time     = $dteDiff->format("%h:%i:%s");

                        $outside_time     = '00:00:00';
                        if($last_out_time != '00:00:00'){
                            $dteDiff      = $last_out_time->diff($dteStart);
                            $outside_time = $dteDiff->format("%h:%i:%s");
                        }

                        $log_insert[] = ['hrm_employee_id'   => $hrm_employee_id,
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
            }else{

                if($out_time != 0 && $in_time != 0){
                    $dteStart         = new DateTime($in_time);
                    $dteEnd           = new DateTime($out_time);
                    $dteDiff          = $dteStart->diff($dteEnd);
                    $working_time     = $dteDiff->format("%h:%i:%s");
                }else{
                    if($keys->punch_status == 0){
                        $in_time         = $keys->punch_date;
                    }else{
                        $in_time         = $default_time;
                    }
                    if($keys->punch_status == 1){
                        $out_time        = $keys->punch_date;
                    }else{
                        $out_time        = $default_time;
                    }
                    $working_time     = '00:00:00';
                }

                $outside_time     = '00:00:00';
                if($last_out_time != '00:00:00'){
                    $dteStart     = new DateTime($in_time);
                    $dteDiff      = $last_out_time->diff($dteStart);
                    $outside_time = $dteDiff->format("%h:%i:%s");
                }

                $log_insert[] = ['hrm_employee_id'   => $hrm_employee_id,
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
        if(!empty($log_insert)){
            Log::insert($log_insert);
            return true;
        }else{
            return false;
        }
    }


    public function ProcessOT($date,$hrm_location_id){

        DB::delete("DELETE FROM hrm_ot_time WHERE attandance_date = '$date' AND hrm_location_id = $hrm_location_id");
        DB::delete("DELETE FROM log_ot      WHERE attandance_date = '$date' AND hrm_location_id = $hrm_location_id");

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
                                                    WHERE end_date IS NULL AND '$date' >= start_date)


                                        JOIN
                                    hrm_shift c ON a.hrm_shift_id = c.id
                                        AND c.date_status = 1
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
                                                    WHERE end_date IS NULL AND '$date' >= start_date)

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


        foreach($ot_data as $keys) {


            if($hrm_employee_id != $keys->hrm_employee_id){
                $calculate      = 0;
                $shift_end_time = 0;
                $ot_time        = 0;
                $lust_punch     = 0;
            }

            if($calculate == 0){
                $hrm_employee_id    = $keys->hrm_employee_id;
                $lust_punch         = $keys->punch_date;
                if($keys->punch_status == 1){
                    $ot_time += strtotime($keys->punch_date) - strtotime($keys->shift_end_time);
                }
                $calculate = 1;
            }else{
                if($keys->punch_status == 0){
                    $lust_punch         = $keys->punch_date;
                }else{

                    $ot_time    += strtotime($keys->punch_date) - strtotime($lust_punch);
                    $lust_punch  = $keys->punch_date;
                }
            }
            $i++;
            if(isset($ot_data[$i])){
                if($hrm_employee_id != $ot_data[$i]->hrm_employee_id){
                    if($ot_time>0){
                        $ottime   = $this->secToHR($ot_time);
                        $insert[] = ['hrm_employee_id'           => $keys->hrm_employee_id,
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
            }else{
                if($ot_time>0){
                    $ottime   = $this->secToHR($ot_time);
                    $insert[] = ['hrm_employee_id'           => $keys->hrm_employee_id,
                                 'attandance_date'           => $date,
                                 'punch_time'                => $ottime,
                                 'hrm_location_id'           => $hrm_location_id
                                ];
                }
            }
        }

        // need to insert
        if(!empty($insert)){
            OTLog::insert($insert);
            return true;
        }else{
            return false;
        }
    }

    public function secToHR($seconds) {
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
}
