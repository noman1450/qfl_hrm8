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

use App\Models\HrmShift;

class ShiftController extends Controller
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
        return view('shift.shift_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shift.create_shift');
    }

    public function shiftlist(){



        // return json_encode(array('data' => HrmShift::where('valid',1)->get()));


        $shift_list=DB::SELECT("SELECT a.id,
                                a.shift_name,
                                a.start_time,
                                a.end_time,
                                a.working_hours,
                                b.location_name
                                FROM hrm_shift a
                                LEFT JOIN hrm_location b ON a.hrm_location_id=b.id WHERE a.valid=1");


        return json_encode(array('data' =>$shift_list));


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
            'shift'         => 'required|unique:hrm_shift,shift_name|max:255',
            'description'   => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'working_hours' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('shift/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        // $start_time = $request->start_time;
        // $end_time   = $request->end_time;

        // dd($end_time);

        $start_time = strtotime($request->start_time);
        $end_time   = strtotime($request->end_time);
        // dd($time2." to ".$time1);
        // $diff = $time1 - $time2;
        // dd('Difference: '.date('H:i:s', $diff));

        if(($end_time-$start_time)>0) {
            $date_status=1;
        }else        {
            $date_status=2;
        }
        // dd($date_status);

        $insert = new HrmShift;
        $insert->shift_name         = $request->shift;
        $insert->shift_description  = $request->description;
        $insert->start_time         = $request->start_time;
        $insert->end_time           = $request->end_time;
        $insert->working_hours      = $request->working_hours;
        $insert->users_id           = Auth::user()->id;
        $insert->valid              = 1;
        $insert->date_status        = $date_status;
        $insert->hrm_location_id    = $request->location;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Working Shit Name',
             null,
             $insert->id,
             'hrm_shift'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('shift');
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
        $shift      = HrmShift::find($id);
        $activity   = DB::table('hrm_employee_shift')->where('hrm_shift_id','=',$id)->where('valid','=',1)->get();
        if (empty($shift)){
            session()->flash('alert-danger', 'Invalid shift !!');
            return Redirect()->back();
        }

        return view('shift.edit_shift')->with('shift',$shift)->with('activity',$activity);
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
            // 'shift'         => 'required|unique:hrm_shift,shift_name|max:255',
            'shift'         => 'required',
            'description'   => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'working_hours' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmShift::find($id);
        $insert->shift_name         = $request->shift;
        $insert->shift_description  = $request->description;
        $insert->start_time         = $request->start_time;
        $insert->end_time           = $request->end_time;
        $insert->working_hours      = $request->working_hours;
        $insert->users_id           = Auth::user()->id;
        $insert->valid              = 1;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Working Shit Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_shift'
        );

        $request->session()->flash('alert-success', 'successfully updated!');
        return Redirect::to('shift');
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

        $cancel = HrmShift::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid shift !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_shift')->where('hrm_shift_id','=',$id)->where('valid','=',1)->first();
        // dd(($activity));

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this shift !! This shift use to employees ");
            return Redirect()->back();
        }

        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $this->recordActivity(
             1,
             'Deleted Working Shift Name',
             null,
             $id,
             'hrm_shift'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('shift');

    }

}
