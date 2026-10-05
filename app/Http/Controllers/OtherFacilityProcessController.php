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

use App\Models\HrmMonth;
use App\Models\HrmPayRegister;
use App\Models\HrmPayRegisterDetails;
use App\Models\HrmSalaryGenerateMaster;


class OtherFacilityProcessController extends Controller
{


    function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {

    //     $user_id = Auth::user()->id;
    //     $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");


    //     return view('otherfacility_process.otherfacility_process')
    //     ->with('month',HrmMonth::all())
    //     -> with('default_user_location',  $default_user_location) ;
    // }


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
                                                    AND a.apply_for = 2
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id AND c.status=2
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

        return view('otherfacility_process.otherfacility_process_list')
              ->with('running_month_year',$running_month_year);


    }




    public function otherfacilityprocess_list(Request $request)
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
                                    If((a.payment_date is null),'FB Process','FB Generated') as status,
                                    e.name as user_name
                                FROM
                                    hrm_salary_generate_master a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id AND a.valid = 1 AND a.status=2
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






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");


        return view('otherfacility_process.otherfacility_process')
        ->with('month',HrmMonth::all())
        -> with('default_user_location',  $default_user_location) ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request->all());

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

        $declation_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->declation_date)));
        $check_data     = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$request->location
                                    AND a.hrm_month_id = $request->month_name
                                    AND a.year_id = $request->year
                                    AND a.apply_for=2
                                    AND a.salary_genarate_type<>0
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.valid =1 LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"This month Fringe Benefits already process !!");
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

            // $ldate = date('Y-m-d H:i:s');

            $insert     = new HrmSalaryGenerateMaster;
            $insert->hrm_location_id        = $request->location;
            $insert->hrm_month_id           = $request->month_name;
            $insert->year_id                = $request->year;
            $insert->declaration_date       = $declation_date;
            $insert->valid                  = 1;
            $insert->users_id               = $users_id;
            $insert->status                 = 2;

            $insert->save();


            $master_id = $insert->id;


            DB::insert("INSERT INTO pay_register(year_id,hrm_month_id,hrm_employee_job_info_id,
                        day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_location_id,accounts_code,hrm_salary_generate_master_id)
                        SELECT $request->year,$request->month_name,a.id,(SELECT RIGHT(LAST_DAY('$date_of_month'),2)),
                        0,1,$users_id,b.id,b.salary_amount,2,b.payment_mode_fb,b.account_no_fb,b.by_bank_percent,b.hrm_bank_id_fb,$request->location,b.accounts_code,$master_id
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location
                            AND a.id in
                            (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$date_of_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$date_of_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                )
                                JOIN
                            -- hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id<>1 and c.status<>2
                            hrm_employment_status c ON a.hrm_employment_status_id = c.id
                                JOIN
                            hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id AND d.joining_date<'$lastdate' ");





            DB::update("UPDATE pay_register
                        JOIN(SELECT
                            COUNT(*) as total_present,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1 AND b.hrm_location_id=$request->location
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year  AND a.attendance_status in(2,3,5,6,7,8,11)
                                GROUP BY b.id) totalpresent
                        ON pay_register.hrm_employee_job_info_id = totalpresent.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = totalpresent.total_present
                        WHERE pay_register.apply_for=2");

 // Deduct for late

        $check_status_value = DB::SELECT("SELECT effect_apply FROM hrm_attendance_status WHERE id=6 ");
        $check_status=$check_status_value[0]->effect_apply;


        if(!empty($check_status)){

            DB::update("UPDATE pay_register
                        JOIN(SELECT
                            left((COUNT(*)/$check_status),1) as deduct_late,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1 AND b.hrm_location_id=$request->location
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year  AND a.attendance_status=6
                                GROUP BY b.id HAVING  Round((COUNT(*)/$check_status)) >0) deduct_late
                        ON pay_register.hrm_employee_job_info_id = deduct_late.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = (pay_register.total_present-deduct_late.deduct_late)
                        WHERE pay_register.apply_for=2");

        }


            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT a.id,c.hrm_salary_head_id,c.amount,c.amount_type,c.actual_amount
                        FROM pay_register a
                        JOIN
                        hrm_employee_salary b ON a.hrm_employee_salary_id = b.id  AND a.apply_for=2
                        AND a.hrm_month_id = $request->month_name
                        AND a.year_id = $request->year
                        AND a.apply_for = 2
                        JOIN
                        hrm_employee_salary_details c ON b.id = c.hrm_employee_salary_id AND c.actual_amount>0
                        JOIN
                        hrm_salary_head d ON c.hrm_salary_head_id=d.id and d.apply_for=2
                        JOIN
                        hrm_employee_job_info e ON e.id=a.hrm_employee_job_info_id AND e.hrm_location_id=$request->location");

        DB::commit();

        $this->recordActivity(
            1,
           'Created Fringe Benefits Process',
            null,
            $insert->id,
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

        //
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

    //     public function deleteotherfacilityprocess(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'year'              => 'required',
    //         'month_name'        => 'required',
    //         'location'        => 'required',
    //     ]);


    //     $year_id  = $request->year;
    //     $month_id = $request->month_name;
    //     $location_id = $request->location;


    //     $check_data = DB::select("SELECT * FROM pay_register a
    //                                 JOIN
    //                                 hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
    //                                 AND b.hrm_location_id=$request->location
    //                                 AND a.hrm_month_id = $request->month_name
    //                                 AND a.year_id = $request->year
    //                                 AND a.apply_for=2
    //                                 AND a.salary_genarate_type=2 LIMIT 1");


    //     if (!empty($check_data)){
    //         $validator->errors()->add('field',"Sorry This month salary already Generated");
    //         return Response::json(array(
    //             'danger'   => true,
    //             'errors'    => $validator->getMessageBag()->toArray()
    //         ));
    //     }



    //     $details_delete = DB::delete("DELETE FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=2 AND b.hrm_location_id=$request->location   WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id )");

    //     $master_delete =  DB::delete("DELETE FROM pay_register WHERE id IN (
    //                                         SELECT implicitTemp.id FROM
    //                                         (SELECT a.id FROM pay_register a
    //                                             JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
    //                                             AND a.apply_for=2 AND b.hrm_location_id=$request->location
    //                                             AND a.year_id =$year_id AND a.hrm_month_id = $month_id) implicitTemp)");

    //     if (!empty($master_delete)){
    //         $validator->errors()->add('field',"Succesfully Delete");
    //         return Response::json(array(
    //             'success'    => true,
    //             'messages'   => 'Successfully Deleted'
    //         ));
    //     }

    //    if (empty($master_delete)){
    //         $validator->errors()->add('field',"This Month Salary Yet Not Process");
    //         return Response::json(array(
    //            'danger'   => true,
    //            'errors'    => $validator->getMessageBag()->toArray()
    //         ));
    //     }




    // }


 public function deleteotherfacilityprocess(Request $request,$id)
    {

        $find_data = HrmSalaryGenerateMaster::find($id);

        $validator = Validator::make($request->all(), [

        ]);

        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $year_id  = $find_data->year_id;
        $month_id = $find_data->hrm_month_id;
        $location_id = $find_data->hrm_location_id;
        $master_id = $find_data->id;


        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$location_id
                                    AND a.hrm_month_id = $month_id
                                    AND a.year_id = $year_id
                                    AND a.apply_for=2
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.id=$master_id
                                    AND a.salary_genarate_type=2  LIMIT 1");


        if (!empty($check_data)){

            $request->session()->flash('alert-danger', 'Sorry This month salary already Generated !');
            return Redirect::to('otherfacilityprocess');
        }

        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND b.hrm_location_id=$location_id
                                    AND a.hrm_month_id = $month_id
                                    AND a.year_id = $year_id
                                    AND a.apply_for=2
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is NOT Null
                                    AND a.salary_genarate_type=1  LIMIT 1");


        if (!empty($check_data)){

            $request->session()->flash('alert-danger', 'Sorry ! Please delete from Partial Salary Option');
            return Redirect::to('otherfacilityprocess');

        }


        $details_delete = DB::delete("DELETE FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=2 AND b.hrm_location_id=$location_id    WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id AND a.hrm_salary_generate_master_id =$master_id)");


        $master_delete =  DB::delete("DELETE FROM pay_register WHERE id IN (
                                            SELECT implicitTemp.id FROM
                                            (SELECT a.id FROM pay_register a
                                                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                                AND a.apply_for=2 AND b.hrm_location_id=$location_id
                                                AND a.year_id =$year_id AND a.hrm_month_id = $month_id
                                            AND a.hrm_salary_generate_master_id =$master_id) implicitTemp)");

       DB::table('hrm_salary_generate_master')
        ->where('id', $master_id)
        ->update(['valid' => 0]);

        $this->recordActivity(
             1,
             'Deleted Fringe Benefits Process',
             null,
             $master_id,
             'hrm_salary_generate_master'
        );


        if (!empty($master_delete)){
                  $request->session()->flash('alert-success', 'successfully deleted !');
                  return Redirect::to('otherfacilityprocess');
        }

       if (empty($master_delete)){
                  $request->session()->flash('alert-danger', 'This Month FB Yet Not Process!');
                  return Redirect::to('otherfacilityprocess');
        }


    }




}
