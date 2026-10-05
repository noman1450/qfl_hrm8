<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Response;

use App\Models\HrmMonth;
use App\Models\HrmLocation;

class CWSalaryGenerateController extends Controller
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
          return view('cw_salary.cw_salary_generate')
               ->with('month',HrmMonth::all())
               ->with('location',HrmLocation::all());
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
        //dd("okk");

        $validator = Validator::make($request->all(), [
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


        $check_data = DB::select("SELECT * FROM pay_register
                                        WHERE hrm_month_id = $request->month_name
                                        AND year_id = $request->year
                                        AND hrm_location_id=$request->location
                                        AND salary_genarate_type=2
                                        AND apply_for=3
                                        AND hrm_salary_generate_master_id= $request->salary_master
                                        LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"This month cw salary already Generated");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $check = DB::select("SELECT * FROM pay_register
            WHERE hrm_month_id = $request->month_name
            AND year_id = $request->year
            AND hrm_location_id=$request->location
            AND apply_for=3
            AND hrm_salary_generate_master_id= $request->salary_master
            AND salary_genarate_type<>0
            LIMIT 1");

        if (empty($check)){
            $validator->errors()->add('field',"This month cw salary yet Not Process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        DB::beginTransaction();
        try {

            DB::update("UPDATE pay_register
                        JOIN (SELECT a.id
                        FROM
                            hrm_employee_job_info a
                            JOIN
                            hrm_plantwise_employee b ON a.id = b.hrm_employee_job_info_id
                            JOIN
                            hrm_plant c ON b.hrm_plant_id=c.id AND c.hrm_location_id=$request->location) plant_employee
                        ON  pay_register.hrm_employee_job_info_id=plant_employee.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.salary_genarate_type  = 2
                        WHERE pay_register.apply_for=3 AND pay_register.salary_genarate_type  = 1
                        AND pay_register.hrm_salary_generate_master_id=$request->salary_master");

            DB::table('hrm_salary_generate_master')
            ->where('id', $request->salary_master)
            ->update(['payment_date' => $payment_date]);
 
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
