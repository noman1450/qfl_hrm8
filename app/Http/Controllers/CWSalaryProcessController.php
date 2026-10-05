<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

use App\Models\HrmMonth;
use App\Models\HrmPayRegister;
use App\Models\HrmPayRegisterDetails;
use App\Models\HrmLocation;
use App\Models\HrmSalaryGenerateMaster;

class CWSalaryProcessController extends Controller
{


    function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $user_id = Auth::user()->id;


        $running_month_year = DB::SELECT("SELECT
                                                a.year_id,
                                                a.hrm_month_id,
                                                b.month_name,
                                                c.id AS master_id,
                                                c.declaration_date,
                                                e.id as hrm_location_id,
                                                e.location_name
                                            FROM
                                                `pay_register` a
                                                    JOIN
                                                hrm_month b ON a.hrm_month_id = b.id
                                                    AND a.`salary_genarate_type` <> 0
                                                    AND a.apply_for = 3
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id AND c.valid=1 AND c.status=3
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
                                                    `user_location` a
                                                        JOIN
                                                    hrm_location b ON a.`hrm_location_id` = b.id
                                                        AND a.`users_id` = $user_id
                                                        AND a.`default_location` = 1");
        }





         return view('cw_salary.cw_salary_process_list')
               ->with('running_month_year',$running_month_year);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('cw_salary.cw_salary_process')
               ->with('month',HrmMonth::all())
               ->with('location',HrmLocation::all())
               -> with('default_user_location',  $default_user_location) ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cw_salary_process_list(Request $request)
    {

        $condition = " AND a.year_id=".$request->year;

        if($request->location==null){
        }else{
            $condition = $condition." AND a.hrm_location_id=".$request->location;
        }

        if($request->month==null){
        }else{
            $condition = $condition." AND a.hrm_month_id=".$request->month;
        }


           $user_id = Auth::user()->id;
           $data = DB::select("SELECT
                                    a.id,
                                    a.hrm_location_id,
                                    a.hrm_month_id,
                                    a.year_id,
                                    a.declaration_date,
                                    a.payment_date,
                                    b.location_name,
                                    c.month_name,
                                    If((a.payment_date is null),'CW Salary Process','CW Salary Generated') as status,
                                    e.name as user_name
                                FROM
                                    hrm_salary_generate_master a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id AND a.valid = 1 AND a.status=3
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




    public function store(Request $request)
    {


        // dd("developing.....");

        // return response::json(array(
        //     'success'    => true,
        //     'messages'   => 'System Upgrading...Please try after 30 minutes.'
        // ));
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


        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$request->location
                                    AND a.hrm_month_id = $request->month_name
                                    AND a.year_id = $request->year
                                    AND a.apply_for=3
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.status=3
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.valid =1
                                    AND a.salary_genarate_type<>0  LIMIT 1");


        if (!empty($check_data)){
            $validator->errors()->add('field',"This month CW salary already process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }




        $finds = DB::select("SELECT * FROM hrm_plant_details c
                                JOIN
                            hrm_plant_with_section d ON c.hrm_plant_with_section_id=d.id
                                AND c.hrm_month_id=$request->month_name AND c.year_id=$request->year
                                JOIN
                            hrm_plant e ON d.hrm_plant_id=e.id AND e.hrm_location_id=$request->location  LIMIT 1");


        if (empty($finds)){
            $validator->errors()->add('field',"This month CW salary yet not setup");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }



        if ($request->month_name>9){
            $month = $request->month_name;
        }else{
            $month = '0'.$request->month_name;
        }

        $date_of_month  = $request->year.'-'.$month.'-01';
        $year_month     = $request->year.'-'.$month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = Auth::user()->id;




        DB::beginTransaction();
        try {

            $ldate = date('Y-m-d H:i:s');

            $insert     = new HrmSalaryGenerateMaster;
            $insert->hrm_location_id        = $request->location;
            $insert->hrm_month_id           = $request->month_name;
            $insert->year_id                = $request->year;
            $insert->declaration_date       = $ldate;
            $insert->valid                  = 1;
            $insert->users_id               = $users_id;
            $insert->status                 = 3;
            $insert->save();

            $master_id = $insert->id;



        DB::insert("INSERT INTO pay_register(year_id,hrm_month_id,hrm_employee_job_info_id,
                        day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_location_id,accounts_code,hrm_salary_generate_master_id)
                        SELECT $request->year,$request->month_name,a.id,c.working_days,
                        0,1,$users_id,aa.id,c.max_rate,3,aa.payment_mode,aa.account_no,aa.by_bank_percent,aa.hrm_bank_id,$request->location,aa.accounts_code,$master_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary aa ON a.id=aa.hrm_employee_job_info_id
                                JOIN
                            hrm_plantwise_employee b ON a.id = b.hrm_employee_job_info_id
                                JOIN
                            hrm_plant_details c ON b.hrm_plant_with_section_id = c.hrm_plant_with_section_id
                                AND c.hrm_month_id=$request->month_name AND c.year_id=$request->year
                                JOIN
                            hrm_plant_with_section d ON c.hrm_plant_with_section_id=d.id
                                JOIN
                            hrm_plant e ON d.hrm_plant_id=e.id AND e.hrm_location_id=$request->location
                                JOIN
                            hrm_employee_joining f ON a.hrm_employee_id=f.hrm_employee_id AND f.joining_date<'$lastdate' ");




          DB::insert("INSERT INTO pay_register_cw(pay_register_id,adv_adjust,due_adjust,hrm_plant_with_section_id)
                        SELECT a.id as pay_register_id,0,0,b.hrm_plant_with_section_id
                            FROM
                            pay_register a
                                JOIN
                            hrm_plantwise_employee b ON a.hrm_employee_job_info_id = b.hrm_employee_job_info_id
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.apply_for = 3
                            AND a.hrm_salary_generate_master_id = $master_id");


            DB::update("UPDATE pay_register
                        JOIN(SELECT SUM(nn.total_present) as total_present,nn.id
                        FROM
                           (SELECT
                            COUNT(*) as total_present,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year  AND a.in_time>'00:00:00'
                                AND b.hrm_location_id= $request->location
                                JOIN
                            hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                JOIN
                            hrm_plant d ON c.hrm_plant_id=d.id AND d.hrm_location_id=$request->location
                                GROUP BY b.id
                        UNION ALL
                        SELECT
                            COUNT(*) as total_present,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year
                                AND  a.attendance_status = 5
                                AND b.hrm_location_id= $request->location
                                JOIN
                            hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                JOIN
                            hrm_plant d ON c.hrm_plant_id=d.id AND d.hrm_location_id=$request->location
                                GROUP BY b.id) nn GROUP BY nn.id
                                ) totalpresent
                        ON pay_register.hrm_employee_job_info_id = totalpresent.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = totalpresent.total_present
                        WHERE pay_register.apply_for=3");


            DB::update("UPDATE pay_register
                        JOIN(SELECT
                                    a.hrm_employee_job_info_id,
                                    a.day_of_month,
                                    a.total_present,
                                    IF(a.day_of_month <= a.total_present,
                                        d.max_rate,
                                        d.minimum_rate) AS salary
                                FROM
                                    pay_register a
                                        JOIN
                                    hrm_plantwise_employee b ON a.hrm_employee_job_info_id = b.hrm_employee_job_info_id
                                        AND a.salary_genarate_type = 1
                                        AND a.hrm_month_id = $request->month_name
                                        AND a.year_id = $request->year
                                        JOIN
                                    hrm_plant c ON b.hrm_plant_id = c.id
                                        AND c.hrm_location_id =$request->location
                                        JOIN
                                    hrm_plant_details d ON c.id = d.hrm_plant_id
                                        AND b.hrm_plant_with_section_id = d.hrm_plant_with_section_id
                                        AND d.hrm_month_id = $request->month_name
                                        AND d.year_id = $request->year
                                        JOIN
                                    hrm_plant_with_section e ON d.hrm_plant_with_section_id = e.id
                                WHERE
                                a.apply_for = 3) working_days
                                        ON pay_register.hrm_employee_job_info_id = working_days.hrm_employee_job_info_id
                                        AND pay_register.hrm_month_id   = $request->month_name
                                        AND pay_register.year_id        = $request->year
                                        AND pay_register.apply_for      = 3
                                        SET pay_register.amount         = working_days.salary");



   // //  Start Working........  // March 14 2023 OT UPDATE (Auto Calculation OT for CW ) .. ...
            // ======================================>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // actual_worked_days             
            DB::UPDATE("UPDATE pay_register_cw
                        JOIN(SELECT
                            COUNT(*) as actual_worked_days,e.id as pay_register_id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity  = 1
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year
                                AND a.in_time>'00:00:00'
                                AND a.attendance_status  not in (7,8) 
                                AND b.hrm_location_id= $request->location
                                JOIN
                            hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                JOIN
                            hrm_plant d ON c.hrm_plant_id=d.id AND d.hrm_location_id=$request->location
                                JOIN
                            pay_register e ON b.id = e.hrm_employee_job_info_id
                                AND e.salary_genarate_type <> 0
                                AND e.hrm_month_id   = $request->month_name
                                AND e.year_id        = $request->year
                                WHERE e.apply_for=3
                                GROUP BY e.id) aa
                        ON pay_register_cw.pay_register_id = aa.pay_register_id
                        SET pay_register_cw.actual_worked_days  = aa.actual_worked_days");

            // holiday_worked_days
            DB::UPDATE("UPDATE pay_register_cw
                        JOIN(SELECT
                            COUNT(*) as holiday_worked_days,e.id as pay_register_id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND YEAR(a.punche_date)  = $request->year
                                AND MONTH(a.punche_date) = $request->month_name
                                AND a.in_time<>'00:00:00'
                                AND b.employee_activity  = 1
                                AND a.attendance_status  IN (7,8)
                                AND b.hrm_location_id= $request->location
                                JOIN
                            hrm_plantwise_employee c ON b.id = c.hrm_employee_job_info_id
                                JOIN
                            hrm_plant d ON c.hrm_plant_id=d.id AND d.hrm_location_id=$request->location
                                JOIN
                            pay_register e ON b.id = e.hrm_employee_job_info_id
                                AND e.salary_genarate_type <> 0
                                AND e.hrm_month_id   = $request->month_name
                                AND e.year_id        = $request->year
                                WHERE e.apply_for=3
                                GROUP BY e.id)  aa
                        ON pay_register_cw.pay_register_id = aa.pay_register_id
                        SET pay_register_cw.holiday_worked_days  = aa.holiday_worked_days");


                        // exceed_days
                        DB::update("UPDATE pay_register_cw
                        JOIN(SELECT a.id,a.hrm_employee_job_info_id,a.day_of_month,a.total_present ,(a.day_of_month-aa.actual_worked_days) remaining_days
                            FROM pay_register a
                                JOIN
                            pay_register_cw aa ON aa.pay_register_id = a.id
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.salary_genarate_type <> 0
                                JOIN 
                            hrm_plantwise_employee b ON a.hrm_employee_job_info_id=b.hrm_employee_job_info_id
                                JOIN
                            hrm_plant c ON b.hrm_plant_id=c.id AND c.hrm_location_id=$request->location
                                JOIN
                            hrm_plant_details d ON c.id=d.hrm_plant_id
                            AND b.hrm_plant_with_section_id = d.hrm_plant_with_section_id
                            AND d.hrm_month_id=$request->month_name AND d.year_id=$request->year
                            WHERE a.apply_for=3 AND (a.day_of_month - aa.actual_worked_days)>0) working_days
                        ON pay_register_cw.pay_register_id = working_days.id
                        SET pay_register_cw.exceed_days  = working_days.remaining_days");


                        // for adjustment only
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
                                AND aa.year_id = $request->year
                                AND aa.hrm_month_id = $request->month_name
                                AND aa.hrm_location_id = $request->location ) aaa ON aaa.id = pay_register_cw.pay_register_id 
                            SET pay_register_cw.exceed_days = aaa.holiday_worked_days
                        ");




   // //  End Working........  // March 14 2023 OT UPDATE (Auto Calculation OT for CW ) .. ...
            // ======================================>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                        

 

            DB::update("UPDATE pay_register
                        JOIN(SELECT a.hrm_employee_job_info_id,a.day_of_month,a.total_present FROM pay_register a
                                JOIN
                            hrm_plantwise_employee b ON a.hrm_employee_job_info_id=b.hrm_employee_job_info_id
                                JOIN
                            hrm_plant c ON b.hrm_plant_id=c.id AND c.hrm_location_id=$request->location
                                JOIN
                            hrm_plant_details d ON c.id=d.hrm_plant_id
                            AND b.hrm_plant_with_section_id = d.hrm_plant_with_section_id
                            AND d.hrm_month_id=$request->month_name AND d.year_id=$request->year
                            WHERE a.`apply_for`=3 AND a.day_of_month<a.total_present) working_days
                        ON pay_register.hrm_employee_job_info_id = working_days.hrm_employee_job_info_id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = working_days.day_of_month
                        WHERE pay_register.apply_for=3");




            DB::commit();

            $this->recordActivity(
                1,
               'Created CW Salary Process',
                null,
                null,
                'hrm_salary_generate_master'
            );

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
            'messages'   => 'successfully process'
        ));

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




    public function cwsalaryprocessdelete(Request $request,$id)
    {


        $find_data = HrmSalaryGenerateMaster::find($id);

        $validator = Validator::make($request->all(), [

        ]);


        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $year_id     = $find_data->year_id;
        $month_id    = $find_data->hrm_month_id;
        $location_id = $find_data->hrm_location_id;
        $master_id   = $find_data->id;


        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$location_id
                                    AND a.hrm_month_id = $month_id
                                    AND a.year_id = $year_id
                                    AND a.apply_for=3
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.id=$master_id
                                    AND a.salary_genarate_type=2  LIMIT 1");


        if (!empty($check_data)){

            $request->session()->flash('alert-danger', 'Sorry This month salary already Generated !');
            return Redirect::to('cwsalaryprocess');
        }



        DB::beginTransaction();
        try {

               DB::table('hrm_salary_generate_master')
                  ->where('id', $master_id)
                  ->update(['valid' => 0]);


                $details_delete = DB::delete("DELETE FROM  pay_register_cw WHERE pay_register_id in(SELECT id FROM pay_register WHERE apply_for=3 AND hrm_location_id=$location_id  AND year_id =$year_id AND hrm_month_id = $month_id AND hrm_salary_generate_master_id =$master_id)");


                $master_delete =  DB::delete("DELETE FROM pay_register WHERE id IN (
                                                    SELECT implicitTemp.id FROM
                                                    (SELECT id FROM pay_register WHERE apply_for=3 AND hrm_location_id=$location_id  AND year_id =$year_id AND hrm_month_id = $month_id AND hrm_salary_generate_master_id =$master_id) implicitTemp)");



        DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

        }

        if (!empty($master_delete)){
              $request->session()->flash('alert-success', 'successfully deleted !');
              return Redirect::to('cwsalaryprocess');
        }


        if (empty($master_delete)){
              $request->session()->flash('alert-danger', 'This Month Salary Yet Not Process!');
              return Redirect::to('cwsalaryprocess');
        }

    }




}
