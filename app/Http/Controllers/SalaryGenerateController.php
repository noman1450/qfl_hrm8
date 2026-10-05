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

class SalaryGenerateController extends Controller
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
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");



          return view('salary_generate.salary_generate')
               ->with('month',HrmMonth::all())
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

        $payment_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date)));


        $check_data = DB::select("SELECT * FROM pay_register a
                            JOIN
                            hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                            AND b.hrm_location_id=$request->location
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.apply_for=1
                            AND a.salary_genarate_type=2
                            AND a.hrm_salary_generate_master_id= $request->salary_master  LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"This month salary already Generated");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $check = DB::select("SELECT * FROM pay_register a
                            JOIN
                            hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                            AND b.hrm_location_id=$request->location
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.apply_for=1
                            AND a.salary_genarate_type<>0
                            AND a.hrm_salary_generate_master_id= $request->salary_master LIMIT 1");

        if (empty($check)){
            $validator->errors()->add('field',"This month salary yet Not Process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        DB::beginTransaction();
        try {

            DB::update("UPDATE pay_register
                        JOIN(SELECT b.id FROM pay_register a
                            JOIN
                            hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                            AND a.hrm_location_id=$request->location
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.apply_for=1 AND a.salary_genarate_type=1
                            AND a.hrm_salary_generate_master_id= $request->salary_master ) update_query
                        ON pay_register.hrm_employee_job_info_id = update_query.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.salary_genarate_type  = 2
                        WHERE pay_register.apply_for=1 AND pay_register.salary_genarate_type=1
                        AND pay_register.hrm_salary_generate_master_id=$request->salary_master");



      $insert = DB::table('hrm_salary_generate_master')
        ->where('id', $request->salary_master)
        ->update(['payment_date' => $payment_date]);



        DB::commit();

            $this->recordActivity(
                 1,
                 'Created Salary Generated',
                 null,
                 $request->salary_master,
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
            'messages'   => 'Salary Successfully Generated'
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
}
