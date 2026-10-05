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

use App\Models\HrmSalaryGrade;

class SalaryGradeController extends Controller
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
          return view('salary_grade.salary_grade_list');
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


    public function salarygrade_list(){

                $salarygrade_list=DB::SELECT("SELECT id,
                                            grade_name,
                                            IF((valid=1),'Active','Deactive') as status
                                            FROM hrm_salary_grade");

                return json_encode(array('data' => $salarygrade_list));




        // return json_encode(array('data' => HrmSalaryGrade::where('valid',1)->get()));
    }






    public function store(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
           'grade_name'   => 'required|unique:hrm_salary_grade,grade_name|max:55',

        ]);

        if ($validator->fails()) {
            session()->flash('alert-danger', 'This Salary Grade Name already exist!!');
            return Redirect()->back();
        }


        $insert = new HrmSalaryGrade;
        $insert->grade_name = $request->grade_name;
        $insert->valid      = 1;
        $insert->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('salarygrade');

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


  public function salarygradeupdate(Request $request)
    {

        $update_data = HrmSalaryGrade::find($request->id);
        $update_data->grade_name   = $request->grade_name;
        $update_data->valid        = $request->status;

        $update_data->save();

        $request->session()->flash('alert-success', 'data has been successfully Update!');
        return Redirect::to('salarygrade');

    }



    public function cancel(Request $request,$id){

        $cancel = HrmSalaryGrade::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Salary Grade Name !!');
            return Redirect()->back();
        }

        $cancel->valid              = 0;
        $cancel->save();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('salarygrade');

    }

}
