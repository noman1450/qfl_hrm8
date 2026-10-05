<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use DataTables;
use Crypt;
use Validator;
use Config;
use Session;
use Response;
use App\Models\HrmReligion;
use App\Models\HrmAttendanceComment;



class ReligionController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

        return view('religion.religion_list');
    }

    public function create()
    {
        return view('religion.create_religion');
    }

    public function religionlist()
    {
        $view_data = DB::select("SELECT id,religion FROM hrm_religion");
     // $view_data = DB::connection('mysql2')->select("SELECT id,description as religion FROM cost_unit");

        $religion_data   = collect($view_data);

        return datatables()->of($religion_data)
            ->addColumn('Link', function ($religion_data) {
            return
                ' <a href="'. url('/religion') . '/' .
                Crypt::encrypt($religion_data->id) .
                '/edit' .'"' .
                'class="btn btn-success btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'religion'    => 'required|unique:hrm_religion,religion|max:255',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $insert = new HrmReligion;
        $insert->religion = $request->religion;
        $insert->save();

        $this->recordActivity(
            1,
           'Created Religion Name',
            null,
            $insert->id,
            'hrm_religion'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');

        return Response::json(array(
            'success'       => true,
            'messages'      => 'successfully save'
        ));
    }

    public function edit($id)
    {
        $religion = HrmReligion::find(decrypt($id));

        // dd($religion);

        if (empty($religion)){
            session()->flash('alert-danger', 'Invalid religion !!');
            return Redirect()->back();
        }

        return view('religion.edit_religion')->with('religion', $religion);
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
            // 'religion'    => 'required|unique:hrm_religion,religion|max:255',
            'religion'    => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmReligion::find($id);
        $insert->religion = $request->religion;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Religion Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_religion'
        );

        $request->session()->flash('alert-success', 'successfully updated');
        return Redirect::to('religion');
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

        $cancel = HrmReligion::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid religion !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee')->where('hrm_religion_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this religion !! This religion use to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_religion')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Religion Name',
             null,
             $id,
             'hrm_religion'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('religion');

    }
}
