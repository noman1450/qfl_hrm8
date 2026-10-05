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

use App\Models\HrmMaritalStatus;


class MaritalStatusController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        return view('marital_status.marital_status_list');
    }

    public function create()
    {
        return view('marital_status.create_marital_status');
    }

    public function maritalstatuslist(){
        return json_encode(array('data' => HrmMaritalStatus::all()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'marital_status'    => 'required|unique:hrm_marital_status,marital_status|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('maritalstatus/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmMaritalStatus;
        $insert->marital_status = $request->marital_status;
        $insert->save();

        $this->recordActivity(
             1,
             'Create Marital Status',
             null,
             $insert->id,
             'hrm_marital_status'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('maritalstatus');
    }

    public function edit($id)
    {
        $edit      = HrmMaritalStatus::find($id);

        if (empty($edit)){
            session()->flash('alert-danger', 'Invalid marital status !!');
            return Redirect()->back();
        }

        return view('marital_status.edit_marital_status')->with('edit_data',$edit);
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
            // 'marital_status'    => 'required|unique:hrm_marital_status,marital_status|max:255',
            'marital_status'    => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmMaritalStatus::find($id);
        $insert->marital_status = $request->marital_status;
        $insert->save();

        $this->recordActivity(
             1,
             'Update Marital Status',
             $insert->getChanges(),
             $insert->id,
             'hrm_marital_status'
        );

        $request->session()->flash('alert-success', 'successfully updated!');
        return Redirect::to('maritalstatus');
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

        $cancel = HrmMaritalStatus::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid marital status !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee')->where('hrm_marital_status_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this marital status !! This marital status use to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_marital_status')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Marital Status',
             null,
             $id,
             'hrm_marital_status'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('maritalstatus');

    }
}
