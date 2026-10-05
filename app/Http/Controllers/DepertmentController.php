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

use App\Models\HrmDepertment;

class DepertmentController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        //
        return view('depertment.depertment_list');
    }

    public function create()
    {
        return view('depertment.create_depertment');
    }


    public function depertmentlist(Request $request){

        return json_encode(array('data' => HrmDepertment::where('valid',1)->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'depertment'   => 'required|unique:hrm_depertment,depertment_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('depertment/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $insert = new HrmDepertment;
        $insert->depertment_name        = $request->depertment;
        $insert->depertment_description = $request->description;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Department Name',
             null,
             $insert->id,
             'hrm_depertment'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('depertment');
    }

    public function edit($id)
    {
        $depertment =  HrmDepertment::find($id);

        if (empty($depertment)){
            session()->flash('alert-danger', 'Invalid depertment !!');
            return Redirect()->back();
        }

        return view('depertment.edit_depertment')->with('depertment',$depertment);
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
        // dd($id);
        // $validator = Validator::make($request->all(), [
        //     'depertment'   => 'required|unique:hrm_depertment,depertment_name,'.$id,
        //     'depertment'   => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return Redirect::back()->withErrors($validator)->withInput();
        // }

        $request->validate([
            'depertment'   => 'required|unique:hrm_depertment,depertment_name,'.$id.',id',
        ]);

        $insert = HrmDepertment::find($id);
        $insert->depertment_name        = $request->depertment;
        $insert->depertment_description = $request->description;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Department Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_depertment'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated !');
        return Redirect::to('depertment');
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

        $cancel = HrmDepertment::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid depertment !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_job_info')->where('hrm_depertment_id','=',$id)
                                                        ->where('employee_activity','=',1)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this Department !! This Department use to employees ");
            return Redirect()->back();
        }


        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $this->recordActivity(
             1,
             'Deleted Department Name',
             null,
             $id,
             'hrm_depertment'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('depertment');

    }
}
