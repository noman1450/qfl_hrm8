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

class OtherFacilityGenerateController extends Controller
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
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('otherfacility_generate.of_generate')
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
        //dd("okk");

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
                            AND a.apply_for=2
                            AND a.salary_genarate_type=2
                            AND a.hrm_salary_generate_master_id= $request->salary_master LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"This month FB already Generated");
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
                            AND a.apply_for=2
                            AND a.salary_genarate_type<>0
                            AND a.hrm_salary_generate_master_id= $request->salary_master LIMIT 1");

        if (empty($check)){
            $validator->errors()->add('field',"This month FB yet Not Process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $payment_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date)));
        $user_id = Auth::user()->id;


        // Accounts Integration Table

        // DB::DELETE("DELETE FROM hrm_acc_processing_data_details WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
        //     hrm_location_id = $request->location AND salary_apply_type=2");
        
        // DB::insert("INSERT INTO hrm_acc_processing_data_details (hrm_acc_journal_type_id,journal_type_name,hrm_acc_headwise_emp_tag_id,hrm_acc_head_title_id,head_name,hrm_salary_head_id,hrm_location_id,location_name,status,location_type,year_id,hrm_month_id,debit_amount,credit_amount,users_id,salary_apply_type,hrm_category_id,category_name)
                       
        //                 SELECT 
        //                     d.id AS hrm_acc_journal_type_id,
        //                     d.journal_type_name,
        //                     a.id as hrm_acc_headwise_emp_tag_id,
        //                     a.hrm_acc_head_title_id,
        //                     c.head_name,
        //                     d.hrm_salary_head_id,
        //                     e.id AS hrm_location_id,
        //                     e.location_name,
        //                     c.status,
        //                     e.location_type,
        //                     $request->year,
        //                     $request->month_name,
        //                     0 as debit_amount,
        //                     0 as credit_amount,
        //                     $user_id,
        //                     2,
        //                     g.hrm_category_id,
        //                     h.category_name
        //                 FROM
        //                     hrm_acc_headwise_emp_tag AS a
        //                         JOIN
        //                     hrm_acc_headwise_emp_tag_details AS b ON b.hrm_acc_headwise_emp_tag_id = a.id
        //                         JOIN
        //                     hrm_acc_head_title AS c ON a.hrm_acc_head_title_id = c.id
        //                         JOIN
        //                     hrm_acc_journal_type AS d ON c.hrm_acc_journal_type_id = d.id
        //                         JOIN
        //                     hrm_location AS e ON b.hrm_location_id = e.id AND e.id=$request->location
        //                         JOIN
        //                     hrm_acc_headwise_emp_tag f ON a.id=f.id
        //                         JOIN
        //                     hrm_acc_headwise_emp_tag_details g ON  f.id=g.hrm_acc_headwise_emp_tag_id
        //                         JOIN
        //                     hrm_category h ON g.hrm_category_id=h.id");


        //          DB::UPDATE("UPDATE hrm_acc_processing_data_details 
        //             JOIN (SELECT
        //                         d.id,
        //                         if(d.status='Dr',SUM(b.actual_amount),0) as debit_amount,
        //                         if(d.status='Cr',SUM(b.actual_amount),0) as credit_amount
        //                     FROM
        //                         pay_register a
        //                             JOIN
        //                         pay_register_details b ON a.id = b.pay_register_id
        //                             AND a.apply_for = 2
        //                             AND a.salary_genarate_type = 1
        //                             AND a.year_id= $request->year AND a.hrm_month_id= $request->month_name
        //                             AND a.hrm_location_id = $request->location
        //                             JOIN
        //                         hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
        //                             JOIN
        //                         hrm_acc_processing_data_details d ON d.hrm_location_id = c.hrm_location_id
        //                         AND d.hrm_salary_head_id = b.hrm_salary_head_id Where d.hrm_category_id = c.hrm_category_id
        //                     GROUP BY d.id,d.hrm_location_id,d.hrm_category_id,d.status) aaa ON hrm_acc_processing_data_details.id=aaa.id
        //                     SET  hrm_acc_processing_data_details.debit_amount = aaa.debit_amount , hrm_acc_processing_data_details.credit_amount = aaa.credit_amount ");



        DB::beginTransaction();
        try {

            DB::update("UPDATE pay_register
                        JOIN(SELECT b.id FROM pay_register a
                            JOIN
                            hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                            AND a.hrm_location_id=$request->location
                            AND a.hrm_month_id = $request->month_name
                            AND a.year_id = $request->year
                            AND a.apply_for=2 AND a.salary_genarate_type=1
                            AND a.hrm_salary_generate_master_id= $request->salary_master) update_query
                        ON pay_register.hrm_employee_job_info_id = update_query.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.salary_genarate_type  = 2
                        WHERE pay_register.apply_for=2 AND pay_register.salary_genarate_type=1
                        AND pay_register.hrm_salary_generate_master_id=$request->salary_master");


           DB::table('hrm_salary_generate_master')
            ->where('id', $request->salary_master)
            ->update(['payment_date' => $payment_date]);


            DB::commit();

            $this->recordActivity(
                1,
               'Created Fringe Benefits Generate',
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
