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


use App\Models\HrmHolidayConvertToWorking;

use App\Models\HrmLeaveYear;
use App\Models\HrmEmployeeHoliday;


class HolidayConvertToWorkingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('holidayconvert_to_working.holidayconvert_to_working_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('holidayconvert_to_working.create_holidayconvert_to_working')
             ->with('leavetypeyear',HrmLeaveYear::all());
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


   public function holidayconvert_to_working_list()
    {

            $user_id=Auth::user()->id;

            $holidayconvertlist= DB::select("SELECT
                                            a.id,
                                            a.action_date,
                                            a.purpose,
                                            IF(a.status = 1,
                                                'Make Holiday to Working Day',
                                                'Make Workingday to Holiday') AS apply_type,
                                            b.location_name

                                        FROM
                                            hrm_holidayconvert_to_working a
                                            join
                                            hrm_location b ON a.hrm_location_id = b.id AND a.valid=1
                                            join
                                            user_location c ON a.hrm_location_id = c.hrm_location_id AND c.users_id = $user_id");



            return json_encode(array('data' => $holidayconvertlist));

    }







    public function store(Request $request)
    {


        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'action_date'   => 'required',
            'purpose'       => 'required',
            'apply_type'    => 'required',


        ]);

        if ($validator->fails()) {
            return redirect('holiday_convert/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user_id=Auth::user()->id;

        $action_date     = date('Y-m-d', strtotime(str_replace('/', '-', $request->action_date)));




        $insert = new HrmHolidayConvertToWorking;
        $insert->action_date     = $action_date;
        $insert->purpose         = $request->purpose;
        $insert->status          = $request->apply_type;
        $insert->hrm_location_id = $request->location_name;
        $insert->users_id        = $user_id;

        $insert->save();

        $this->recordActivity(
             1,
             'Created Holiday Convert to Working',
             $insert,
             $insert->id,
             'hrm_holidayconvert_to_working'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holiday_convert');


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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $update = HrmHolidayConvertToWorking::find($id);

        if (empty($update)){
            session()->flash('alert-danger', 'Invalid Input !!');
            return Redirect()->back();
        }

        $update->valid     = 0;
        $update->save();

        $this->recordActivity(
             1,
             'Deleted Holiday Convert to Working',
             $update,
             $id,
             'hrm_holidayconvert_to_working'
        );

        $request->session()->flash('alert-success', 'Successfully Delete,Please Attendance Process Again!');
        return Redirect::to('holiday_convert');
    }

}
