<?php

namespace App\Http\Controllers;

use App\Models\HrmMonth;
use App\Models\HrmLocation;
use App\Models\HrmManualOT;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HrmEligibleOTEmployee;
use App\Models\HrmManualOTProcessMaster;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AttendanceDataProcessController;

class ManualOTController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('manual_ot.manual_ot_list')
            ->with('default_user_location', $default_user_location);
    }

    public function manual_ot_process()
    {
        $user_id = Auth::user()->id;

        $running_month_year = DB::SELECT("SELECT
                                                a.year_id,
                                                a.hrm_month_id,
                                                b.month_name,
                                                a.id AS master_id,
                                                e.id as hrm_location_id,
                                                e.location_name
                                            FROM
                                                hrm_ot_process_master a
                                                    JOIN
                                                hrm_month b ON a.hrm_month_id = b.id AND a.process_status<>0
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id
                                                    AND d.`users_id`= $user_id
                                                    JOIN
                                                hrm_location e ON d.hrm_location_id = e.id
                                            ORDER BY a.id DESC
                                            LIMIT 0 , 1");

        if(empty($running_month_year)){
            $running_month_year = DB::SELECT("SELECT
                                                    b.id AS hrm_location_id,
                                                    b.location_name,
                                                    0 AS year_id,
                                                    0 AS hrm_month_id,
                                                    0 AS month_name,
                                                    0 AS master_id,
                                                    0 As declaration_date
                                                FROM
                                                    user_location a
                                                        JOIN
                                                    hrm_location b ON a.`hrm_location_id` = b.id
                                                        AND a.`users_id` = $user_id
                                                        AND a.`default_location` = 1");
        }

        return view('manual_ot.manual_ot_process_list')
            ->with('running_month_year', $running_month_year);
    }

    public function otprocess_list(Request $request)
    {

        $condition = " AND a.year_id=".$request->year;

        if($request->location==null){
        }else{
            $condition .= " AND a.hrm_location_id=".$request->location;
        }

        if($request->month==null){
        }else{
            $condition .= " AND a.hrm_month_id=".$request->month;
        }


           $user_id = Auth::user()->id;
           $data = DB::select("SELECT
                                    a.id,
                                    a.hrm_location_id,
                                    a.hrm_month_id,
                                    a.year_id,
                                    -- a.declaration_date,
                                    -- a.payment_date,
                                    b.location_name,
                                    c.month_name,
                                    If((a.process_status=1),'OT Process','OT Generated') as status,
                                    a.process_status,
                                    e.name as user_name,
                                    a.created_at
                                FROM
                                    hrm_ot_process_master a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id AND a.process_status<>0
                                      $condition
                                        JOIN
                                    hrm_month c ON a.hrm_month_id = c.id
                                        JOIN
                                    user_location d ON a.hrm_location_id = d.hrm_location_id
                                        AND d.`users_id` =$user_id
                                        JOIN
                                    users e ON a.users_id=e.id");


        return json_encode(array('data' => $data));
    }

    public function create()
    {
         return view('manual_ot.manual_ot_entry');
    }

    public function ot_adjust_create()
    {
        return view('manual_ot.ot_adjust_create')->with('month',HrmMonth::all());
    }

    public function ot_adjust_submit(Request $request)
    {


        // dd("sdssdd");

        DB::beginTransaction();
            try{


                    if(request()->has('reserve')){

                        // dd("
                        //     SELECT
                        //        a.id,a.in_time,a.out_time,c.start_time,c.end_time,a.overtime_time, b.actual_overtime_time
                        //     FROM
                        //         hrm_attendance a
                        //             JOIN
                        //         pay_register_cw_ot b ON a.id = b.hrm_attendance_id
                        //             AND a.hrm_location_id = $request->location
                        //             AND YEAR(a.punche_date) = $request->year
                        //             AND MONTH(a.punche_date) = $hrm_month_id
                        //             AND comment = 'OT Auto Generated Adjusted'
                        //         JOIN
                        //         hrm_shift c ON a.hrm_shift_id = c.id");


                            DB::UPDATE("UPDATE hrm_attendance JOIN(
                            SELECT
                               a.id,a.in_time,a.out_time,c.start_time,c.end_time,a.overtime_time, b.actual_overtime_time
                            FROM
                                hrm_attendance a
                                    JOIN
                                pay_register_cw_ot b ON a.id = b.hrm_attendance_id
                                    AND a.hrm_location_id = $request->location
                                    AND YEAR(a.punche_date) = $request->year
                                    AND MONTH(a.punche_date) = $request->month_name
                                    AND comment = 'OT Auto Generated Adjusted'
                                JOIN
                                hrm_shift c ON a.hrm_shift_id = c.id) aa ON hrm_attendance.id = aa.id SET hrm_attendance.overtime_time = aa.actual_overtime_time , hrm_attendance.comment = ''");


                            $message = 'Reverse Done';


                    }else{
                            // dd("Working....");

                            $is_auto_ot_adjust = HrmLocation::where('id','=',$request->location)->first()->is_auto_ot_adjust;

                                if($is_auto_ot_adjust==2){

                                    $hrm_month_id =$request->month_name ;
                                    $year_id =$request->year ;
                                    $hrm_location_id =$request->location ;

                                    //actual_worked_days
                                    DB::UPDATE("UPDATE pay_register_cw
                                            JOIN
                                        (SELECT
                                            COUNT(*) AS actual_worked_days, e.id AS pay_register_id
                                        FROM
                                            hrm_attendance a
                                        JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                            AND b.employee_activity = 1
                                            AND MONTH(a.punche_date) = $hrm_month_id
                                            AND YEAR(a.punche_date) = $year_id
                                            AND a.in_time > '00:00:00'
                                            AND a.attendance_status NOT IN (7 , 8)
                                            AND b.hrm_location_id = $hrm_location_id
                                        JOIN hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                        JOIN hrm_plant d ON c.hrm_plant_id = d.id
                                            AND d.hrm_location_id = $hrm_location_id
                                        JOIN pay_register e ON b.id = e.hrm_employee_job_info_id
                                            AND e.salary_genarate_type <> 0
                                            AND e.hrm_month_id = $hrm_month_id
                                            AND e.year_id = $year_id
                                        WHERE
                                            e.apply_for = 3
                                        GROUP BY e.id) aa ON pay_register_cw.pay_register_id = aa.pay_register_id
                                    SET
                                        pay_register_cw.actual_worked_days = aa.actual_worked_days");


                                    // -- holiday_worked_days

                                    DB::UPDATE("UPDATE pay_register_cw
                                            JOIN
                                        (SELECT
                                            COUNT(*) AS holiday_worked_days, e.id AS pay_register_id
                                        FROM
                                            hrm_attendance a
                                        JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                            AND a.in_time <> '00:00:00'
                                            AND b.employee_activity = 1
                                            AND MONTH(a.punche_date) = $hrm_month_id
                                            AND YEAR(a.punche_date) = $year_id
                                            AND a.attendance_status IN (7 , 8)
                                            AND b.hrm_location_id = $hrm_location_id
                                        JOIN hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                        JOIN hrm_plant d ON c.hrm_plant_id = d.id
                                            AND d.hrm_location_id = $hrm_location_id
                                        JOIN pay_register e ON b.id = e.hrm_employee_job_info_id
                                            AND e.salary_genarate_type <> 0
                                            AND e.hrm_month_id = $hrm_month_id
                                            AND e.year_id = $year_id
                                        WHERE
                                            e.apply_for = 3
                                        GROUP BY e.id) aa ON pay_register_cw.pay_register_id = aa.pay_register_id
                                    SET
                                        pay_register_cw.holiday_worked_days = aa.holiday_worked_days");


                                    // exceed_days

                                    DB::UPDATE("UPDATE pay_register_cw
                                            JOIN
                                        (SELECT
                                            a.id,
                                                a.hrm_employee_job_info_id,
                                                a.day_of_month,
                                                a.total_present,
                                                (ifnull(a.day_of_month,0)  - ifnull(aa.actual_worked_days,0) ) remaining_days
                                        FROM
                                            pay_register a
                                        JOIN pay_register_cw aa ON aa.pay_register_id = a.id
                                            AND a.hrm_month_id = $hrm_month_id
                                            AND a.year_id = $year_id
                                            AND a.salary_genarate_type <> 0
                                        JOIN hrm_plantwise_employee b ON a.hrm_employee_job_info_id = b.hrm_employee_job_info_id
                                        JOIN hrm_plant c ON b.hrm_plant_id = c.id
                                            AND c.hrm_location_id = $hrm_location_id
                                        JOIN hrm_plant_details d ON c.id = d.hrm_plant_id
                                            AND b.hrm_plant_with_section_id = d.hrm_plant_with_section_id
                                            AND d.hrm_month_id = $hrm_month_id
                                            AND d.year_id = $year_id
                                        WHERE
                                            a.apply_for = 3
                                                AND (ifnull(a.day_of_month,0) - ifnull(aa.actual_worked_days,0) ) > 0) working_days ON pay_register_cw.pay_register_id = working_days.id
                                    SET
                                        pay_register_cw.exceed_days = working_days.remaining_days");



                                    DB::UPDATE("UPDATE pay_register_cw JOIN (SELECT
                                            aa.id,aa.day_of_month,bb.actual_worked_days,bb.holiday_worked_days,bb.exceed_days
                                        FROM
                                            pay_register aa
                                                JOIN
                                            pay_register_cw bb ON aa.id = bb.pay_register_id
                                        WHERE

                                            aa.salary_genarate_type <> 0
                                            AND bb.exceed_days > bb.holiday_worked_days
                                            AND aa.apply_for = 3
                                            AND aa.year_id = $year_id
                                            AND aa.hrm_month_id = $hrm_month_id
                                            AND aa.hrm_location_id = $hrm_location_id ) aaa ON aaa.id = pay_register_cw.pay_register_id
                                        SET pay_register_cw.exceed_days = aaa.holiday_worked_days");


                                        $query = DB::SELECT("SELECT
                                                                 c.hrm_employee_id,a.exceed_days
                                                             FROM
                                                                 pay_register_cw a
                                                                     JOIN
                                                                 pay_register b ON a.pay_register_id = b.id
                                                                     AND b.apply_for = 3
                                                                     AND a.exceed_days > 0
                                                                     AND b.salary_genarate_type <> 0
                                                                     AND year_id = $year_id
                                                                     AND hrm_month_id = $hrm_month_id
                                                                     AND hrm_location_id = $hrm_location_id
                                                                     JOIN
                                                                 hrm_employee_job_info c ON b.hrm_employee_job_info_id = c.id
                                                            ");
                                        // dd($query);

                                          foreach ($query as $key ){

                                            $exceed_days = $key->exceed_days ;
                                            $hrm_employees_id = $key->hrm_employee_id ;


                                            DB::insert("INSERT INTO pay_register_cw_ot (hrm_attendance_id, actual_overtime_time , hrm_employee_id,created_at)
                                                        SELECT
                                                                id, overtime_time , hrm_employee_id,now()
                                                            FROM
                                                                hrm_attendance
                                                            WHERE
                                                                YEAR(punche_date) = $year_id
                                                                    AND MONTH(punche_date) =$hrm_month_id
                                                                    AND hrm_location_id =$hrm_location_id
                                                                    AND attendance_status IN (7 , 8)
                                                                    AND hrm_employee_id =  $hrm_employees_id
                                                                    AND overtime_time > '00:00:00'
                                                            ORDER BY punche_date
                                                            LIMIT  $exceed_days");


                                            DB::UPDATE("UPDATE hrm_attendance JOIN (SELECT
                                                                            id,overtime_time
                                                                        FROM
                                                                            hrm_attendance
                                                                        WHERE
                                                                            YEAR(punche_date) = $year_id
                                                                                AND MONTH(punche_date) = $hrm_month_id
                                                                                AND hrm_location_id = $hrm_location_id
                                                                                AND attendance_status IN (7,8)
                                                                                AND hrm_employee_id = $hrm_employees_id
                                                                                AND overtime_time > '00:00:00'
                                                                        ORDER BY punche_date
                                                                        LIMIT $exceed_days) aa
                                            ON hrm_attendance.id = aa.id
                                            SET hrm_attendance.comment = 'OT Auto Generated Adjusted', hrm_attendance.overtime_time = DATE_SUB(hrm_attendance.overtime_time, INTERVAL 9 HOUR)");
                                }

                          $message = 'Adjustment Done';
                    }

                }



        DB::commit();
        } catch (\Exception $e) {
        DB::rollback();
        return Response::json(array(
            'success'           => false,
            'error_messages'    => true,
            'errors'          => "insert problem !! " . $e->getMessage()
        ));
        }

        return response::json(array(
            'success'    => true,
            'messages'   => $message
        ));

    }

    public function ot_adjust()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('manual_ot.ot_adjust_list')
            ->with('default_user_location', $default_user_location);
    }

    public function ot_process_create()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('manual_ot.manual_ot_process')
            ->with('month',HrmMonth::all())
            ->with('default_user_location', $default_user_location) ;
    }

    public function ot_process_generate()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('manual_ot.manual_ot_generate')
            ->with('month',HrmMonth::all())
            ->with('default_user_location', $default_user_location);
    }

    public function manual_ot_generate_submit(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'location'          => 'required',
            'year'              => 'required',
            'month_name'        => 'required',
        ]);


        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        DB::UPDATE("UPDATE hrm_ot_process_master SET process_status=2 WHERE hrm_location_id=$request->location AND
            hrm_month_id=$request->month_name AND year_id=$request->year ");


        $request->session()->flash('alert-success', 'Data has been successfully Generated!');
        return Redirect::to('manual_ot_process');



    }

    public function manual_ot_all()
    {
         return view('manual_ot.manual_ot_all');
    }

    public function manual_ot_process_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location'   => 'required',
            'year'       => 'required',
            'month_name' => 'required',
        ]);

        if( $validator->fails() ){
            return Response::json(array(
                'success' => false,
                'errors'  => $validator->getMessageBag()->toArray()
            ));
        }

        $date_of_month  = $request->year.'-'.$request->month_name.'-01';
        $year_month     = $request->year.'-'.$request->month_name;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));

        $check_data = DB::select("SELECT * FROM hrm_ot_process_master
                                    WHERE hrm_location_id=$request->location
                                    AND hrm_month_id = $request->month_name
                                    AND year_id = $request->year
                                    AND process_status=1
                                    ");

        if (!empty($check_data)){
            // $validator->errors()->add('field',"Sorry ! This Month OT Already Process");
            // return Response::json(array(
            //     'danger'   => true,
            //     'errors'    => $validator->getMessageBag()->toArray()
            // ));

            $request->session()->flash('alert-danger', 'Alresdy Processed!');
            return Redirect::to('manual_ot_process');
        }

        DB::beginTransaction();
            try {
            $insert     = new HrmManualOTProcessMaster;
            $insert->year_id         = $request->year ;
            $insert->hrm_month_id    = $request->month_name;
            $insert->hrm_location_id = $request->location;
            $insert->process_status  = 1;
            $insert->users_id        = Auth::user()->id;
            $insert->save();

            DB::insert("INSERT INTO hrm_ot_process_details(hrm_ot_process_master_id,hrm_employee_job_info_id,
                        actual_overtime,net_overtime,rate,basic_salary,actual_ot_rate,gross_salary)
                        SELECT $insert->id,c.id as hrm_employee_job_info_id,TIME_FORMAT(SEC_TO_TIME( SUM(TIME_TO_SEC(a.overtime_time))),'%H:%i') AS totalovertime,0,IFNULL(e.ot_rate,0) as rate,(c.basic_salary*.60),((c.basic_salary*.60)/(8*26)*2),c.basic_salary
                        FROM hrm_attendance  a
                                JOIN
                            hrm_employee b ON a.hrm_employee_id = b.id AND Month(a.punche_date)=$request->month_name
                            And Year(a.punche_date)=$request->year
                                JOIN
                            hrm_employee_job_info c ON b.id = c.hrm_employee_id AND c.overtime_status=1
                            AND c.hrm_location_id = $request->location
                            AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                        WHERE end_date BETWEEN '$date_of_month' AND '$lastdate'  AND end_date IS NOT NULL
                                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                                        UNION ALL
                                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5))

                                JOIN
                            hrm_employment_status d On c.hrm_employment_status_id=d.id and d.status=1
                                JOIN
                            hrm_category e ON c.hrm_category_id = e.id
                        GROUP BY c.id,e.ot_rate,c.basic_salary");
            // 400/470

            DB::insert("INSERT INTO hrm_ot_process_details(hrm_ot_process_master_id,hrm_employee_job_info_id,
                        actual_overtime,net_overtime,rate,basic_salary,actual_ot_rate,gross_salary)
                        SELECT $insert->id,c.id as hrm_employee_job_info_id,TIME_FORMAT(SEC_TO_TIME( SUM(TIME_TO_SEC(a.overtime_time))),'%H:%i') AS totalovertime,0,IFNULL(e.ot_rate,0) as rate,(c.basic_salary*.60),((c.basic_salary*.60)/(8*26)*2),(c.basic_salary)
                        FROM hrm_attendance  a
                                JOIN
                            hrm_employee b ON a.hrm_employee_id = b.id AND Month(a.punche_date)=$request->month_name
                            And Year(a.punche_date)=$request->year
                                JOIN
                            hrm_employee_job_info c ON b.id = c.hrm_employee_id AND c.overtime_status=1
                            AND c.hrm_location_id = $request->location
                            AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                        WHERE end_date BETWEEN '$date_of_month' AND '$lastdate'  AND end_date IS NOT NULL
                                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                                        UNION ALL
                                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5))

                                JOIN
                            hrm_employment_status d On c.hrm_employment_status_id=d.id and d.status=2
                                JOIN
                            hrm_category e ON c.hrm_category_id = e.id
                        GROUP BY c.id,e.ot_rate,c.basic_salary");



                DB::update("UPDATE hrm_ot_process_details
                        JOIN(
                        SELECT
                            b.hrm_employee_job_info_id, c.hrm_bank_id, c.account_no,b.id as hrm_ot_process_details_id , c.payment_mode
                        FROM
                            hrm_ot_process_master a
                                JOIN
                            hrm_ot_process_details b ON a.id = b.hrm_ot_process_master_id AND a.id = $insert->id
                                AND a.process_status <> 0
                                JOIN
                            hrm_employee_salary c ON b.hrm_employee_job_info_id = c.hrm_employee_job_info_id
                                join
                            hrm_employee_job_info d ON b.hrm_employee_job_info_id = d.id
                                JOIN
                            hrm_employment_status e ON d.hrm_employment_status_id = e.id AND e.status = 1
                            ) aa
                        ON hrm_ot_process_details.id = aa.hrm_ot_process_details_id
                        SET hrm_ot_process_details.hrm_bank_id = aa.hrm_bank_id , hrm_ot_process_details.account_no = aa.account_no , hrm_ot_process_details.payment_mode = aa.payment_mode");

             DB::update("UPDATE hrm_ot_process_details
                        JOIN(
                        SELECT
                            b.hrm_employee_job_info_id, c.hrm_bank_id, c.account_no,b.id as hrm_ot_process_details_id , c.payment_mode
                        FROM
                            hrm_ot_process_master a
                                JOIN
                            hrm_ot_process_details b ON a.id = b.hrm_ot_process_master_id AND a.id = $insert->id
                                AND a.process_status <> 0
                                JOIN
                            hrm_employee_salary c ON b.hrm_employee_job_info_id = c.hrm_employee_job_info_id
                                join
                            hrm_employee_job_info d ON b.hrm_employee_job_info_id = d.id
                                JOIN
                            hrm_employment_status e ON d.hrm_employment_status_id = e.id AND e.status = 2
                            ) aa
                        ON hrm_ot_process_details.id = aa.hrm_ot_process_details_id
                        SET  hrm_ot_process_details.payment_mode = 1");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return Response::json(array(
                'success'        => false,
                'error_messages' => true,
                'errors'         => "insert problem !! " . $e->getMessage()
            ));
        }

        DB::UPDATE("UPDATE hrm_ot_process_details SET net_overtime=hour(actual_overtime) WHERE hrm_ot_process_master_id = $insert->id");

        $request->session()->flash('alert-success', 'data has been successfully process!');
        return Redirect::to('manual_ot_process');
    }

    public function manual_otlistdata(Request $request){

        $condition    = "";
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        if ($request->location != 0){
           $condition  = " AND cc.hrm_location_id = ".$request->location;
        }else{
           $condition  = " AND cc.hrm_location_id = 0";
        }

        $data   = DB::select("SELECT
                                aa.id,
                                aa.hrm_employee_id,
                                concat(hh.employee_name,' | ',cc.employee_code) as employee_name,
                                ee.depertment_name,
                                ff.designation_name,
                                gg.location_name,
                                aa.punch_date,
                                aa.overtime,
                                aa.comment
                            FROM

                                hrm_manual_ot aa
                                    JOIN
                                hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id and cc.employee_activity=1 and aa.valid=1 and aa.punch_date='$date'
                                    JOIN
                                hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                    JOIN
                                hrm_designation ff ON cc.hrm_designation_id = ff.id
                                    JOIN
                                hrm_location gg ON cc.hrm_location_id = gg.id
                                    JOIN
                                hrm_employee hh ON cc.hrm_employee_id = hh.id
                                $condition ");

        return json_encode(array('data' => $data));
    }

    public function submitmanual_otall(Request $request)
    {
        if ($request->id == null) {
            $request->session()->flash('alert-danger', 'please select employee and resubmit!');
            return Redirect::to('manual_ot_all');
        }

        $count_row = count($request->id);

        for ($r = 0; $r <$count_row; $r++) {
            $overtime               = $request->punch_time[$request->id[$r]];
            $punch_date             = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date[$request->id[$r]])));
            $employee_id            = $request->id[$r];

            $insert     = new HrmManualOT;
            $insert->hrm_employee_id        = $employee_id ;
            $insert->punch_date             = $punch_date;
            $insert->overtime               = $overtime;
            // $insert->comment                = $request->comment;
            $insert->valid                  = 1;
            $insert->users_id               = Auth::user()->id;
            $insert->save();

            DB::update("UPDATE hrm_attendance SET overtime_time='$overtime' WHERE hrm_employee_id=$employee_id AND punche_date='$punch_date'");
        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('manual_ot');
    }

    public function submit_manual_process_ot_edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hrm_ot_process_master_id' => 'required',
            'id'                       => 'required',
            'net_overtime'             => 'required',
            'rate'                     => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json(array(
                'success' => false,
                'message' => 'Select at least one row.'
            ));
        }

        $hrm_ot_process_master_id = $request->hrm_ot_process_master_id;

        $find_data = HrmManualOTProcessMaster::find($hrm_ot_process_master_id);

        if ($find_data->process_status == 2) {
            return response()->json(array(
                'success' => false,
                'message' => "You can't update this!! This data already generated"
            ));
        }

        DB::beginTransaction();
        try {
            $count_row = count($request->id);

            for ($r = 0; $r < $count_row; $r++) {
                $employee_job_id = $request->id[$r];
                $net_overtime    = $request->net_overtime[$employee_job_id];
                $rate            = $request->rate[$employee_job_id];
                $addition_amt    = $request->addition_amt[$employee_job_id];
                $deduction_amt   = $request->deduction_amt[$employee_job_id];
                $account_no      = $request->account_no[$employee_job_id];
                $payment_mode    = $request->payment_mode[$employee_job_id];
                $bank_name       = $request->bank_name[$employee_job_id];

                DB::UPDATE("UPDATE hrm_ot_process_details SET
                        net_overtime = $net_overtime,
                        rate = $rate,
                        addition_amt = $addition_amt,
                        deduction_amt = $deduction_amt,
                        account_no = $account_no,
                        payment_mode = $payment_mode,
                        hrm_bank_id = $bank_name

                    WHERE hrm_ot_process_master_id = $hrm_ot_process_master_id AND hrm_employee_job_info_id = $employee_job_id");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(array(
                'success' => false,
                'message' => "insert problem !! " . $e->getMessage()
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Successfully Updated'
        ));
    }

    function submit_manual_process_ot_edit_single(Request $request)
    {
        $hrm_ot_process_master_id = $request->hrm_ot_process_master_id;

        $find_data = HrmManualOTProcessMaster::find($hrm_ot_process_master_id);

        if ($find_data->process_status == 2) {
            return response()->json(array(
                'success' => false,
                'message' => "You can't update this!! This data already generated"
            ));
        }

        $employee_job_id = $request->employee_job_id;
        $net_overtime    = $request->net_overtime;
        $rate            = $request->rate;
        $addition_amt    = $request->addition_amt;
        $deduction_amt   = $request->deduction_amt;
        $account_no      = $request->account_no;
        $bank_name       = $request->bank_name == null ? ', hrm_bank_id = null' : ', hrm_bank_id = '.$request->bank_name;

        DB::UPDATE("UPDATE hrm_ot_process_details SET
                net_overtime = $net_overtime,
                rate = $rate,
                addition_amt = $addition_amt,
                deduction_amt = $deduction_amt,
                account_no = '$account_no',
                payment_mode = $request->payment_mode
                $bank_name

            WHERE hrm_ot_process_master_id = $hrm_ot_process_master_id AND hrm_employee_job_info_id = $employee_job_id");

        return response('Okay');
    }

    public function manual_ot_view()
    {
        $user_id = Auth::user()->id;

        $default_user_location = DB::select("SELECT
                a.year_id,
                a.hrm_month_id,
                b.month_name,
                a.id master_id,
                e.id hrm_location_id,
                e.location_name
            FROM
                hrm_ot_process_master a
                    JOIN
                hrm_month b ON a.hrm_month_id = b.id
                    AND a.process_status <> 0
                    JOIN
                user_location d ON a.hrm_location_id = d.hrm_location_id
                    AND d.users_id = $user_id
                    JOIN
                hrm_location e ON d.hrm_location_id = e.id
            ORDER BY a.id DESC
            LIMIT 0 , 1
        ");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

        return view('manual_ot.manual_ot_view')
            ->with('location',  $location)
            ->with('default_user_location',  $default_user_location)
            ->with('month',HrmMonth::all());
    }

    public function manual_ot_view_data(Request $request)
    {
        $condition = '';

        if ($request->department_id != 0) {
            $condition .= " AND a.hrm_depertment_id = ".$request->department_id ;
        }

        if ($request->section_id != 0) {
            $condition .= " AND a.hrm_section_id = ".$request->section_id ;
        }

        if ($request->section_id != 0) {
            $condition .= " AND a.hrm_section_id = ".$request->section_id ;
        }

        if ($request->category_id != 0) {
            $condition .= " AND a.hrm_category_id = ".$request->category_id;
        }

        if ($request->payment_id != 0) {
            $condition .= " AND bb.payment_mode = ".$request->payment_id;
        }

        if ($request->bank_id != 0) {
            $condition .= " AND bb.hrm_bank_id = ".$request->bank_id;
        }

        $data = DB::select("SELECT
                a.id,
                concat (b.employee_name,' | ',a.employee_code) as employee_name,
                e.depertment_name,
                f.designation_name,
                g.location_name,
                i.section_name,
                bb.actual_overtime,
                bb.net_overtime,
                bb.rate,
                bb.addition_amt,
                bb.deduction_amt,
                aa.id as hrm_ot_process_master_id,
                bb.account_no,
                bb.payment_mode,
                bb.hrm_bank_id,
                j.bank_name
            FROM
                hrm_ot_process_master aa
                    JOIN
                hrm_ot_process_details bb ON aa.id= bb.hrm_ot_process_master_id
                    AND aa.year_id = $request->year
                    AND aa.hrm_month_id = $request->month_name
                    AND aa.hrm_location_id = $request->location
                    JOIN
                hrm_employee_job_info a ON bb.hrm_employee_job_info_id = a.id
                    $condition
                    JOIN
                hrm_employee b ON a.hrm_employee_id = b.id
                    JOIN
                hrm_depertment e ON a.hrm_depertment_id = e.id
                    JOIN
                hrm_designation f ON a.hrm_designation_id = f.id
                    JOIN
                hrm_location g ON a.hrm_location_id = g.id
                    JOIN
                hrm_section i ON a.hrm_section_id = i.id
                    left join
                hrm_bank j ON bb.hrm_bank_id = j.id
        ");

        return json_encode(array('data' => $data));
    }

    public function manual_ot_listdata(Request $request)
    {
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        $condition    = "a.active_status= 1 ";
        $punch_date   = $request->punch_date;

        if ($request->location != 0){
          $condition  = " b.hrm_location_id = ".$request->location;
        }

        if ($request->working_shift_id != 0){
          $condition  =  $condition . " AND d.id = ".$request->working_shift_id ;
        }

        if ($request->department_id != 0){
          $condition  =  $condition . " AND e.id = ".$request->department_id ;
        }

        if ($request->designation_id != 0){
          $condition  =  $condition . " AND f.id = ".$request->designation_id ;
        }

        if ($request->section_id != 0){
          $condition  =  $condition . " AND i.id = ".$request->section_id ;
        }


        $data   = DB::select("SELECT
                                a.id,
                                concat (a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                i.section_name,
                                '$punch_date' as punch_date,
                                '00:00:00' as punch_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                and b.overtime_status = 1
                                and a.id not in (select hrm_employee_id from hrm_manual_ot where valid=1 and `punch_date`='$date')
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date IS NULL
                                AND c.start_date <='$date'
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                    JOIN
                                hrm_section i ON i.id = b.hrm_section_id
                                Where
                                    $condition
                                UNION
                                SELECT
                                a.id,
                                concat (a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                i.section_name,
                                '$punch_date' as punch_date,
                                '00:00:00' as punch_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                and b.overtime_status = 1
                                and a.id not in (select hrm_employee_id from hrm_manual_ot where valid=1 and `punch_date`='$date')
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date IS NOT NULL
                                    AND '$date' between c.start_date AND c.end_date
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                    JOIN
                                hrm_section i ON i.id = b.hrm_section_id
                                Where
                                    $condition

                            ");

        return json_encode(array('data' => $data));
    }

    // SAIF
    public function eligible_ot_view_data(Request $request)
    {
        $condition=' AND a.hrm_location_id ='.$request->hrm_location_id;

        if ($request->department_id != 0){
          $condition  =  $condition . " AND a.hrm_depertment_id = ".$request->department_id ;
        }

        if ($request->section_id != 0){
          $condition  =  $condition . " AND a.hrm_section_id = ".$request->section_id ;
        }

        if ($request->section_id != 0){
          $condition  =  $condition . " AND a.hrm_section_id = ".$request->section_id ;
        }

        if ($request->category_id != 0){
          $condition  =  $condition . " AND a.hrm_category_id = ".$request->category_id;
        }

        $apply_date   = date('Y-m-d', strtotime(str_replace('/', '-', $request->ot_date)));

        $allow_ot = request()->allow_ot;

        $data   = DB::select("SELECT
                                b.id,
                                concat (b.employee_name,' | ',a.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                i.section_name,
                                '$allow_ot' allow_ot
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_employee b ON  a.hrm_employee_id = b.id AND a.employee_activity =1
                                AND a.overtime_status =1
                                $condition
                                AND b.id NOT IN (SELECT hrm_employee_id FROM hrm_eligible_ot_employee WHERE isActive=1 AND apply_date = '$apply_date' )
                                    JOIN
                                hrm_depertment e ON a.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON a.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON a.hrm_location_id = g.id
                                    JOIN
                                hrm_section i ON a.hrm_section_id = i.id ");

        return json_encode(array('data' => $data));
    }

    public function date_wise_eligible_ot_list(Request $request)
    {

        $condition=' AND a.hrm_location_id='.$request->hrm_location_id;

        if (isset($request->hrm_depertment_id)){
            $condition .= " AND a.hrm_depertment_id = ".$request->hrm_depertment_id ;
        }

        if (isset($request->hrm_employee_id)){
            $condition .= " AND a.hrm_employee_id = ".$request->hrm_employee_id ;
        }

        if (isset($request->hrm_section_id)){
            $condition .= " AND a.hrm_section_id = ".$request->hrm_section_id ;
        }

        if (isset($request->hrm_category_id)){
            $condition .= " AND a.hrm_category_id = ".$request->hrm_category_id;
        }

        $apply_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->ot_date)));

        $data = DB::select("SELECT
                b.id,
                concat (b.employee_name,' | ',a.employee_code) AS employee_name,
                e.depertment_name,
                f.designation_name,
                g.location_name,
                i.section_name,
                j.apply_date,
                j.eligible_ot_hour
            FROM
                hrm_employee_job_info a
                    JOIN
                hrm_employee b ON a.hrm_employee_id = b.id AND a.employee_activity = 1
                $condition
                    JOIN
                hrm_depertment e ON a.hrm_depertment_id = e.id
                    JOIN
                hrm_designation f ON a.hrm_designation_id = f.id
                    JOIN
                hrm_location g ON a.hrm_location_id = g.id
                    JOIN
                hrm_section i ON a.hrm_section_id = i.id
                    JOIN
                hrm_eligible_ot_employee j ON b.id = j.hrm_employee_id
            WHERE j.apply_date = '$apply_date' AND j.isActive = 1
        ");

        return json_encode(array('data' => $data));
    }

    public function update_date_wise_eligible_ot($id)
    {
        $apply_date = date('Y-m-d', strtotime(str_replace('/', '-', request()->ot_date)));

        $eligibleOtEmployee = DB::table('hrm_eligible_ot_employee')
            ->where('hrm_employee_id', $id)
            ->where('apply_date', $apply_date);

        if (request()->action === 'update') {
            $eligibleOtEmployee->update([
                'eligible_ot_hour' => request()->ot_time
            ]);

            $this->recordActivity(
                 1,
                 'Updated Eligible Ot',
                 null,
                 $id,
                 'hrm_eligible_ot_employee'
            );
        }

        if (request()->action === 'delete') {
            $eligibleOtEmployee->update([
                'isActive' => 0
            ]);


            $this->recordActivity(
                 1,
                 'Deleted Eligible Ot',
                 null,
                 $id,
                 'hrm_eligible_ot_employee'
            );
        }

        return response()->json('Data has been updated.!');
    }

    public function pending_ot_approval(Request $request)
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");


        if (request()->ajax()) {

                $condition='' ;

                if (isset($request->hrm_depertment_id)){
                    $condition .= " AND a.hrm_depertment_id = ".$request->hrm_depertment_id ;
                }

                if (isset($request->hrm_employee_id)){
                    $condition .= " AND a.hrm_employee_id = ".$request->hrm_employee_id ;
                }

                if (isset($request->hrm_section_id)){
                    $condition .= " AND a.hrm_section_id = ".$request->hrm_section_id ;
                }

                if (isset($request->hrm_category_id)){
                    $condition .= " AND a.hrm_category_id = ".$request->hrm_category_id;
                }

                $apply_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->ot_date)));

                $data = DB::select("SELECT
                        b.id,
                        concat (b.employee_name,' | ',a.employee_code) AS employee_name,
                        e.depertment_name,
                        f.designation_name,
                        g.location_name,
                        i.section_name,
                        j.apply_date,
                        j.eligible_ot_hour,
                        j.exceed_ot_hour
                    FROM
                        hrm_eligible_ot_pending j
                            JOIN
                        hrm_employee_job_info a ON a.hrm_employee_id = j.hrm_employee_id
                            AND a.employee_activity = 1 AND a.hrm_location_id=$request->hrm_location_id
                            AND j.apply_date = '$apply_date' AND j.isActive = 1
                            JOIN
                        hrm_employee b ON a.hrm_employee_id = b.id
                        $condition
                            JOIN
                        hrm_depertment e ON a.hrm_depertment_id = e.id
                            JOIN
                        hrm_designation f ON a.hrm_designation_id = f.id
                            JOIN
                        hrm_location g ON a.hrm_location_id = g.id
                            JOIN
                        hrm_section i ON a.hrm_section_id = i.id ");

                return json_encode(array('data' => $data));
        }

        return view('manual_ot.pending_ot_approval')
            ->with('location',  $location)
            ->with('default_user_location',  $default_user_location)
            ->with('month',HrmMonth::all());
    }

    public function update_date_wise_eligible_ot_pending($id)
    {
        $apply_date = date('Y-m-d', strtotime(str_replace('/', '-', request()->ot_date)));
        $users_id   = auth()->id();
        $ldate      = date('Y-m-d H:i:s');
        $ot_time    = request()->ot_time;
        $hrm_location_id = HrmEmployeeJobInfo::where('employee_activity',1)->where('hrm_employee_id',$id)->first()->hrm_location_id;

        if (request()->action === 'update') {

            DB::UPDATE("UPDATE hrm_eligible_ot_pending SET isActive=0,users_id = $users_id,updated_at='$ldate' WHERE hrm_employee_id=$id AND apply_date='$apply_date' ");
            DB::UPDATE("UPDATE hrm_eligible_ot_employee SET eligible_ot_hour='$ot_time',users_id = $users_id,updated_at='$ldate' WHERE hrm_employee_id=$id AND apply_date='$apply_date' ");
            DB::UPDATE("UPDATE hrm_attendance SET overtime_time='$ot_time' WHERE hrm_employee_id=$id AND punche_date='$apply_date' ");

            // $attendance = new AttendanceDataProcessController();
            // $attendance->attandanceProcess($hrm_location_id,$apply_date,$id);
        }

        if (request()->action === 'delete') {
            DB::UPDATE("UPDATE hrm_eligible_ot_pending SET isActive=0,users_id = $users_id,updated_at='$ldate' WHERE hrm_employee_id=$id AND apply_date='$apply_date' ");
        }

        return response()->json('Data has been updated.!');
    }

    public function submit_eligible_ot_emp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'  => 'required',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Please tickmark from employee list!');
            return back();
        }

        if(!empty($request->id)){
            $ot_date   = date('Y-m-d', strtotime(str_replace('/', '-', $request->ot_date)));
            $count_row = count($request->id);
            $ldate     = date('Y-m-d H:i:s');


            $insert = [];
            for($r = 0; $r <$count_row; $r++) {
                $hrm_employee_id  = $request->id[$r];
                $eligible_ot_hour = $request->allow_ot[$hrm_employee_id];

                $insert[] = [
                    'apply_date'       => $ot_date,
                    'hrm_employee_id'  => $hrm_employee_id,
                    'eligible_ot_hour' => $eligible_ot_hour,
                    'users_id'         => Auth::user()->id,
                    'isActive'         => 1,
                    'created_at'       => $ldate,
                    'updated_at'       => $ldate,
                ];
            }

            if(!empty($insert)){
                HrmEligibleOTEmployee::insert($insert);

/*                $this->recordActivity(
                     1,
                     'Created Eligile OT Employee',
                     null,
                     $hrm_employee_id,
                     'hrm_eligible_ot_employee'
                );*/
            }

        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return back();
    }
    //END  SAIF

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name'  => 'required',
            'punch_date'     => 'required',
            'overtime'       => 'required',
            'comment'        => 'required',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Please fillup all fields!');
            return Redirect::to('manual_ot/create');
        }

        $punch_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));
        $check_data = DB::select("SELECT * FROM hrm_manual_ot WHERE hrm_employee_id = $request->employee_name AND punch_date = '$punch_date' AND valid=1");

        if (!empty($check_data)){
            $request->session()->flash('alert-danger', 'Employee has already manual Entry in this day!');
            return Redirect::to('manual_ot/create');
        }

        $check_ot_status = DB::select("SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id = $request->employee_name AND overtime_status = 1");

        if (empty($check_ot_status)){
            $request->session()->flash('alert-danger', 'This Employee has no OT permission!');
            return Redirect::to('manual_ot/create');
        }

        $location = DB::SELECT("SELECT hrm_location_id from hrm_employee_job_info WHERE hrm_employee_id=$request->employee_name
                                AND employee_activity=1");

        $insert     = new HrmManualOT;
        $insert->hrm_employee_id        = $request->employee_name;
        $insert->hrm_location_id        = $location[0]->hrm_location_id;
        $insert->punch_date             = $punch_date;
        $insert->overtime               = $request->overtime;
        $insert->comment                = $request->comment;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Manual OT Entry',
             $insert,
             $insert->id,
             'hrm_manual_ot'
        );

        DB::update("UPDATE hrm_attendance SET overtime_time='$request->overtime',actual_overtime='$request->overtime' WHERE hrm_employee_id=$request->employee_name AND punche_date='$punch_date'");

         //-------Data Process
        $attendance = new AttendanceDataProcessController();
        $attendance->attandanceProcess($location[0]->hrm_location_id,$punch_date,$request->employee_name);

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('manual_ot');
    }

    public function deleteotprocess(Request $request,$id)
    {
        $find_data = HrmManualOTProcessMaster::find($id);
        $validator = Validator::make($request->all(), [
            // 'year'              => 'required',
            // 'month_name'        => 'required',
            // 'location'          => 'required',
        ]);

        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        if ($find_data->process_status==2){
            session()->flash('alert-danger', 'Sorry Already Generated !!');
            return Redirect()->back();
        }

        DB::delete("DELETE FROM hrm_ot_process_details WHERE hrm_ot_process_master_id=$id");
        DB::delete("DELETE FROM hrm_ot_process_master WHERE id=$id");


        // DB::UPDATE("UPDATE hrm_ot_process_master SET process_status=1 WHERE process_status=2 AND id=$id ");




        $request->session()->flash('alert-success', 'Seccessfully Deleted !');
        return Redirect::to('manual_ot_process');


        // $check_data = DB::select("SELECT * FROM hrm_ot_process_master
        //                             WHERE id=$id
        //                             AND a.salary_genarate_type=2  LIMIT 1");


        // if (!empty($check_data)){

        //     $request->session()->flash('alert-danger', 'Sorry This month salary already Generated !');
        //     return Redirect::to('salaryprocess');
        // }
    }

    public function cancel(Request $request,$id)
    {
        $cancel = HrmManualOT::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid, Please Check!!');
            return Redirect()->back();
        }
        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();


        $this->recordActivity(
             1,
             'Deleted Manual OT',
             $cancel,
             $id,
             'hrm_manual_ot'
        );

        //-------Data Process
        $attendance = new AttendanceDataProcessController();
        $attendance->attandanceProcess($cancel->hrm_location_id,$cancel->punch_date,$cancel->hrm_employee_id);

        $request->session()->flash('alert-success', 'Successfully deleted. Please Attendance Process this day again.');
        return Redirect::to('manual_ot');
    }

    public function add_eligible_ot_emp()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

        return view('manual_ot.add_eligible_ot_emp')
             ->with('location',  $location)
             ->with('default_user_location',  $default_user_location)
             ->with('month',HrmMonth::all());
    }

    public function eligible_ot_emp()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

        return view('manual_ot.eligible_ot_emp')
             ->with('location',  $location)
             ->with('default_user_location',  $default_user_location)
             ->with('month',HrmMonth::all());
    }
}
