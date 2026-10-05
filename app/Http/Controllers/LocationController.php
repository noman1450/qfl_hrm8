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
use App\Models\HrmLocation;
class LocationController extends Controller
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
        return view('location.location_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('location.create_location');
    }

    public function locationlist(){

        $data = DB::select("SELECT 
                                    id,location_name,
                                    CASE location_type
                                        WHEN '1' THEN 'Head Office'
                                        WHEN '2' THEN 'Factory'
                                        ELSE 'Depot'
                                    END AS 'location_type'
                                FROM
                                    hrm_location
                                WHERE
                                    valid = 1");



        return json_encode(array('data' => $data));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location'   => 'required|unique:hrm_location,location_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('location/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $insert = new HrmLocation;
        $insert->location_name          = $request->location;
        $insert->location_description   = $request->description;
        $insert->location_type          = $request->location_type;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Location Name',
             null,
             $insert->id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('location');
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
        $edit_data = HrmLocation::find($id);
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid location !!');
            return Redirect()->back();
        }

        return view('location.edit_location')
            ->with('edit_data',$edit_data);
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
            'location'   => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }


        $insert = HrmLocation::find($id);
        $insert->location_name          = $request->location;
        $insert->location_description   = $request->description;
        $insert->location_type          = $request->location_type;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Location Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('location');
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

        $cancel = HrmLocation::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid location !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_job_info')->where('hrm_location_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this location !! This location use to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_location')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Location Name',
             null,
             $id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('location');

    }
}
