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
use Entrust;

use App\Models\HrmDesignation;

class DesignationController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        return view('designation.designation_list');
    }

    public function create()
    {
        return view('designation.create_designation');
    }

    public function designationlist(Request $request)
    {
        return json_encode(array('data' => HrmDesignation::where('valid',1)->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation'   => 'required|unique:hrm_designation,designation_name|max:255',
            'alis'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('designation/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmDesignation;
        $insert->designation_name   = $request->designation;
        $insert->alis               = $request->alis;
        $insert->priority           = $request->priority;
        $insert->valid              = 1;
        $insert->users_id           = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Designation Name',
             null,
             $insert->id,
             'hrm_designation'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('designation');
    }

    public function edit($id)
    {
        $designation =  HrmDesignation::find($id);


        if (empty($designation)){
            session()->flash('alert-danger', 'Invalid designation !!');
            return Redirect()->back();
        }

        return view('designation.edit_designation')->with('designation',$designation);
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

        $validator = Validator::make($request->all(), [
            // 'designation'   => 'required|unique:hrm_designation,designation_name|max:255',
            'designation'   => 'required',
            'alis'          => 'required',
        ]);

        if ($validator->fails()) {
            // return redirect('designation/create')
            //             ->withErrors($validator)
            //             ->withInput();
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $update = HrmDesignation::find($id);
        $update->designation_name   = $request->designation;
        $update->alis               = $request->alis;
        $update->priority           = $request->priority;
        $update->users_id           = Auth::user()->id;
        $update->save();

        $this->recordActivity(
             1,
             'Updated Designation Name',
             $update->getChanges(),
             $update->id,
             'hrm_designation'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated !');
        return Redirect::to('designation');
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

    public function cancel(Request $request,$id){



        $cancel = HrmDesignation::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid designation !!');
            return Redirect()->back();
        }

      $activity   = DB::table('hrm_employee_job_info')->where('hrm_designation_id','=',$id)
      ->where('employee_activity','=',1)->first();

        // dd($activity);

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this Designation !! This Designation use to employees ");
            return Redirect()->back();
        }

        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $this->recordActivity(
             1,
             'Deleted Designation Name',
             null,
             $id,
             'hrm_designation'
        );

        // DB::table('hrm_designation')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('designation');

    }
}
