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

class SalaryHeadGroupController extends Controller
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
          return view('salary_head_group.salaryheadgroup_list');
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


    public function salaryheadgroup_list(){
        // $user_id    = Auth::user()->id;
        $salaryheadgroup_list = DB::select("SELECT
                                                id,
                                                group_name,
                                                generate_type AS generate_type_id,
                                                IF((generate_type) = 1,
                                                    'Addition',
                                                    'Deduction') AS generate_type,
                                                CASE group_status
                                                    WHEN 1 THEN 'Salary'
                                                    WHEN 2 THEN 'Bonus'
                                                    WHEN 3 THEN 'Loan'
                                                    WHEN 4 THEN 'PF'
                                                    WHEN 5 THEN 'Additional Salary'
                                                    ELSE 'Additional Deduction Salary'
                                                END AS group_status,
                                                group_status AS group_status_id,
                                                is_delete
                                            FROM
                                                hrm_salary_head_group");

        return json_encode(array('data' => $salaryheadgroup_list));


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
        'group_name'   => 'required|unique:hrm_salary_head_group,group_name|max:55',

        ]);

        if ($validator->fails()) {
            session()->flash('alert-danger', 'This Salary Head Group Name already exist!!');
            return Redirect()->back();
        }


        $insert = new HrmSalaryHeadGroup;
        $insert->group_name     = $request->group_name;
        $insert->generate_type  = $request->generate_type;
        $insert->group_status   = $request->group_status;

        $insert->save();

        $this->recordActivity(
             1,
             'Created Salary Group Head',
             null,
             $insert->id,
             'hrm_salary_head_group'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('salaryheadgroup');
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


  public function salaryheadgroupupdate(Request $request)
    {


// dd($request->all());

        $update_data = HrmSalaryHeadGroup::find($request->id);
        $update_data->group_name     = $request->group_name;
        $update_data->generate_type  = $request->generate_type;
        $update_data->group_status   = $request->group_status;

        $update_data->save();

        $this->recordActivity(
             1,
             'Updated Salary Group Head',
             $update_data->getChanges(),
             $update_data->id,
             'hrm_salary_head_group'
        );

        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('salaryheadgroup');

    }



    public function cancel(Request $request,$id){

        $cancel = HrmSalaryHeadGroup::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Salary Head Group Not Found !!');
            return Redirect()->back();
        }

        DB::table('hrm_salary_head_group')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('salaryheadgroup');

    }





}
