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


use App\Models\HrmSalaryHeadGroup;
use App\Models\HrmSalaryHead;
use App\Models\HrmSalaryGrade;
use App\Models\HrmSalaryGradeMaster;
use App\Models\HrmSalaryGradeDetails;



class SalaryGradeSetupController extends Controller
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
          return view('salary_grade_setup.salarygradesetup_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          return view('salary_grade_setup.salarygradesetup');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function salarygradesetuplistdata(Request $request){

        $data   = DB::select("SELECT
                                a.id,
                                b.grade_name,
                                If((a.salary_type=1),'Basic','Gross') As status,
                                a.salary_size as grossamt,
                                GROUP_CONCAT(CONCAT(d.salary_head)  SEPARATOR '<br>') AS salary_head,
                                GROUP_CONCAT(CONCAT(c.amount)  SEPARATOR '<br>') AS amount,
                                GROUP_CONCAT(CONCAT(If((c.amount_type=1),'%','TK'))  SEPARATOR '<br>') AS type,
                                GROUP_CONCAT(CONCAT(e.group_name)  SEPARATOR '<br>') AS group_name,
                                GROUP_CONCAT(CONCAT(If((e.generate_type=1),'Addition','Deduction'))  SEPARATOR '<br>') AS generate_type
                                FROM `hrm_salary_grade_master` a
                                JOIn hrm_salary_grade  b  ON a.hrm_salary_grade_id=b.id
                                Join hrm_salary_grade_details c ON a.id=c.hrm_salary_grade_master_id
                                JOIN hrm_salary_head d On c.hrm_salary_head_id=d.id
                                JOIN hrm_salary_head_group e ON d.hrm_salary_head_group_id=e.id
                                Group By a.id,b.grade_name,a.salary_type,a.salary_size");

        return json_encode(array('data' => $data));

    }


    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'salary_type'          => 'required',
            'salary_grade'         => 'required|unique:hrm_salary_grade_master,hrm_salary_grade_id',

        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'This Salary Grade Already Exist!');
            return redirect('salarygradesetup')
                        ->withErrors($validator)
                        ->withInput();
        }

        $count_row_addition  = count($request->amountaddition);
        $count_row_deduction = count($request->amountdeduction);


                            // if (!empty($count_row_addition)){
                            //     return redirect('salarygradesetup');
                            // }

        DB::beginTransaction();
                    try{

                    //insert_master

                            $insert_master= new HrmSalaryGradeMaster;
                            $insert_master->hrm_salary_grade_id   =$request->salary_grade;
                            $insert_master->salary_type           =$request->salary_type;
                            $insert_master->salary_size           =$request->salary_size;
                            $insert_master->save();


                    //insert_Details

                            // foreach ($request->amountaddition as $keys ) {
                            for($r = 0; $r <$count_row_addition; $r++) {
                                $insert_details     = new HrmSalaryGradeDetails;
                                $insert_details->hrm_salary_grade_master_id = $insert_master->id;
                                $insert_details->hrm_salary_head_id         = $request->addition_id[$r];
                                if ($request->amountaddition[$r]>0){
                                    $insert_details->amount                 = $request->amountaddition[$r];
                                    $insert_details->amount_type            = $request->typeaddition[$r];
                                }else{
                                    $insert_details->amount                 = 0;
                                    $insert_details->amount_type            = $request->typeaddition[$r];
                                }

                                $insert_details->save();
                            }


                            for($r = 0; $r <$count_row_deduction; $r++) {
                                $insert_details_data     = new HrmSalaryGradeDetails;
                                $insert_details_data->hrm_salary_grade_master_id = $insert_master->id;
                                $insert_details_data->hrm_salary_head_id         = $request->deduction_id[$r];
                                if ($request->amountdeduction[$r]>0){
                                    $insert_details_data->amount                 = $request->amountdeduction[$r];
                                    $insert_details_data->amount_type            = $request->typededuction[$r];
                                }else{
                                    $insert_details_data->amount                 = 0;
                                    $insert_details_data->amount_type            = $request->typededuction[$r];
                                }

                                $insert_details_data->save();
                            }



        DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }


       $request->session()->flash('alert-success', 'data has been successfully added!');
       return Redirect::to('salarygradesetup');

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


    $edit_data=DB::SELECT("SELECT
                            a.id,
                            a.salary_type AS salary_type_id,
                            IF((a.salary_type = 1),
                                'Basic',
                                'Gross') salary_type,
                            b.id AS grade_id,
                            b.grade_name,
                            0 AS grossamt,
                            d.id AS salary_head_id,
                            d.salary_head,
                            c.amount,
                            IF((c.amount_type = 1), '%', 'TK') type,
                            c.amount_type,
                            e.group_name,
                            a.salary_size
                        FROM
                            `hrm_salary_grade_master` a
                                JOIN
                            hrm_salary_grade b ON a.hrm_salary_grade_id = b.id
                                AND a.id = $id
                                JOIN
                            hrm_salary_grade_details c ON a.id = c.hrm_salary_grade_master_id
                                RIGHT JOIN
                            hrm_salary_head d ON c.hrm_salary_head_id = d.id
                                LEFT JOIN
                            hrm_salary_head_group e ON d.hrm_salary_head_group_id = e.id WHERE e.generate_type=1");


    $edit_data_deduction=DB::SELECT("SELECT
                            a.id,
                            a.salary_type AS salary_type_id,
                            IF((a.salary_type = 1),
                                'Basic',
                                'Gross') salary_type,
                            b.id AS grade_id,
                            b.grade_name,
                            0 AS grossamt,
                            d.id AS salary_head_id,
                            d.salary_head,
                            c.amount,
                            IF((c.amount_type = 1), '%', 'TK') type,
                            c.amount_type,
                            e.group_name,
                            a.salary_size
                        FROM
                            `hrm_salary_grade_master` a
                                JOIN
                            hrm_salary_grade b ON a.hrm_salary_grade_id = b.id
                                AND a.id = $id
                                JOIN
                            hrm_salary_grade_details c ON a.id = c.hrm_salary_grade_master_id
                                RIGHT JOIN
                            hrm_salary_head d ON c.hrm_salary_head_id = d.id
                                LEFT JOIN
                            hrm_salary_head_group e ON d.hrm_salary_head_group_id = e.id WHERE e.generate_type=2");

     $master_data=DB::SELECT("SELECT
                            a.id,
                            b.id as grade_id,
                            b.grade_name,
                            a.salary_type as salary_type_id,
                            IF((a.salary_type = 1),
                                'Basic',
                                'Gross') salary_type,
                            a.salary_size
                        FROM
                            hrm_salary_grade_master a
                                JOIN
                            hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
                            WHERE a.id=$id");


                          return view('salary_grade_setup.edit_salarygradesetup')
                               ->with('edit_data',$edit_data)
                               ->with('edit_data_deduction',$edit_data_deduction)
                               ->with('master_data',$master_data);

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

// dd($request->all());
        $validator = Validator::make($request->all(), [
            'salary_type'          => 'required',
            'salary_grade'         => 'required',

        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'This Salary Grade Already Exist!');
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $count_row_addition  = count($request->amountaddition);
        $count_row_deduction = count($request->amountdeduction);


        DB::beginTransaction();
                    try{

                    //insert_master

                            $insert_master= HrmSalaryGradeMaster::find($id);
                            $insert_master->hrm_salary_grade_id   =$request->salary_grade;
                            $insert_master->salary_type           =$request->salary_type;
                            $insert_master->salary_size           =$request->salary_size;
                            $insert_master->save();


                    //insert_Details
                  DB::table('hrm_salary_grade_details')->where('hrm_salary_grade_master_id', '=', $id)->delete();

                            // foreach ($request->amountaddition as $keys ) {
                            for($r = 0; $r <$count_row_addition; $r++) {
                                $insert_details     = new HrmSalaryGradeDetails;
                                $insert_details->hrm_salary_grade_master_id = $insert_master->id;
                                $insert_details->hrm_salary_head_id         = $request->addition_id[$r];
                                if ($request->amountaddition[$r]>0){
                                    $insert_details->amount                 = $request->amountaddition[$r];
                                    $insert_details->amount_type            = $request->typeaddition[$r];
                                }else{
                                    $insert_details->amount                 = 0;
                                    $insert_details->amount_type            = $request->typeaddition[$r];
                                }

                                $insert_details->save();
                            }


                            for($r = 0; $r <$count_row_deduction; $r++) {
                                $insert_details_data     = new HrmSalaryGradeDetails;
                                $insert_details_data->hrm_salary_grade_master_id = $insert_master->id;
                                $insert_details_data->hrm_salary_head_id         = $request->deduction_id[$r];
                                if ($request->amountdeduction[$r]>0){
                                    $insert_details_data->amount                 = $request->amountdeduction[$r];
                                    $insert_details_data->amount_type            = $request->typededuction[$r];
                                }else{
                                    $insert_details_data->amount                 = 0;
                                    $insert_details_data->amount_type            = $request->typededuction[$r];
                                }

                                $insert_details_data->save();
                            }



        DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }


       $request->session()->flash('alert-success', 'data has been successfully added!');
       return Redirect::to('salarygradesetup');

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
