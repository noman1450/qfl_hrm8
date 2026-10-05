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


use App\Models\HrmHoliday;

class HolidayController extends Controller
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
        return view('holiday.holiday_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('holiday.create_holiday');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function holidaylist(Request $request){

        $holidaylist=DB::SELECT("SELECT id,
                                holiday_name,
                                holiday_description
                                FROM hrm_holiday");

        return json_encode(array('data' =>$holidaylist));

    }




    public function store(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'holiday_name'   => 'required',


        ]);

        if ($validator->fails()) {
            return redirect('holiday/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmHoliday;
        $insert->holiday_name          = $request->holiday_name;
        $insert->holiday_description   = $request->holiday_description;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Holiday Name',
             null,
             $insert->id,
             'hrm_holiday'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holiday');
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
        $edit_data = HrmHoliday::find($id);
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid Holiday Information !!');
            return Redirect()->back();
        }

        return view('holiday.edit_holiday')
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
            'holiday_name'   => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmHoliday::find($id);
        $insert->holiday_name          = $request->holiday_name;
        $insert->holiday_description   = $request->holiday_description;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Holiday Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_holiday'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holiday');
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

        $cancel = HrmHoliday::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Holiday Information !!');
            return Redirect()->back();
        }


        $activity   = DB::table('hrm_employee_holiday')->where('hrm_holiday_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this Holiday Info !! This Holiday Info use to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_holiday')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Holiday Name',
             null,
             $id,
             'hrm_holiday'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('holiday');

    }





}
