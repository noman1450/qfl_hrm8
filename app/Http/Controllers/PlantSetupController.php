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


use App\Models\HrmPlant;
use App\Models\HrmPlantDetails;
use App\Models\HrmPlantWiseEmployee;

class PlantSetupController extends Controller
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

    }

    public function plant_setup()
    {
        $current_month = date('m');
        $current_year  = date('Y');
        $user_id       = Auth::user()->id;
        $default_user_location = DB::select("SELECT
                                                    b.id,b.location_name
                                                FROM `user_location` a
                                                JOIN hrm_location b ON a.`hrm_location_id`=b.id
                                                and a.`users_id`= $user_id
                                                AND a.`default_location`=1");

        $running_month_year = DB::SELECT("SELECT id as hrm_month_id,month_name FROM hrm_month WHERE id=$current_month");

        return view('plantsetup.plantwisesalary_list')
               ->with('default_user_location',  $default_user_location)
               ->with('year',$current_year)
               ->with('running_month_year',$running_month_year);

    }

    public function plantwisesalarycreate()
    {
        $month=DB::Select("SELECT id,month_name FROM hrm_month");
        return view('plantsetup.plantwisesalary_create')
        ->with('month',$month);
    }

    public function create()
    {

    }




    public function plantwisesalary_list(Request $request){

        $condition = '';

        if($request->location==null){

        }else{
            $condition = ' AND c.hrm_location_id ='.$request->location;
        }

        if($request->month==null){

        }else{
            $condition = $condition.' AND a.hrm_month_id ='.$request->month;
        }

        if($request->year==null){

        }else{
            $condition = $condition.' AND a.year_id ='.$request->year;
        }

        $users_id= Auth::user()->id;

        $plantwisesalarylist = DB::SELECT("SELECT
                                                    a.id,
                                                    a.hrm_plant_id,
                                                    CONCAT(c.plant_name, ' | ', d.location_name) AS plant_name,
                                                    e.month_name,
                                                    a.year_id,
                                                    a.working_days,
                                                    a.minimum_rate,
                                                    a.max_rate,
                                                    f.section_name
                                                FROM
                                                    hrm_plant_details a
                                                        JOIN
                                                    hrm_plant_with_section b ON a.hrm_plant_with_section_id = b.id
                                                        JOIN
                                                    hrm_plant c ON a.hrm_plant_id = c.id
                                                    $condition
                                                        JOIN
                                                    hrm_location d ON c.hrm_location_id = d.id
                                                        JOIN
                                                    hrm_month e ON a.hrm_month_id = e.id
                                                        JOIN
                                                    hrm_section f ON b.hrm_section_id = f.id
                                                        JOIN
                                                    user_location g ON d.id = g.hrm_location_id AND g.users_id = $users_id");
                                                    return json_encode(array('data' =>$plantwisesalarylist));

        }



    public function plant_setup_edit($id){

        // dd($id);
        $month=DB::Select("SELECT id,month_name FROM hrm_month");

        $editdata=DB::SELECT("SELECT
                                    a.id,
                                    a.hrm_plant_id,
                                    CONCAT(c.plant_name, ' | ', d.location_name) AS plant_name,
                                    e.month_name,
                                    a.year_id,
                                    a.working_days,
                                    a.minimum_rate,
                                    a.max_rate,
                                    f.section_name,
                                    b.id as hrm_plant_with_section_id,
                                    a.hrm_month_id

                                FROM
                                    hrm_plant_details a
                                        JOIN
                                    hrm_plant_with_section b ON a.hrm_plant_with_section_id = b.id AND a.id = $id
                                        JOIN
                                    hrm_plant c ON a.hrm_plant_id = c.id
                                        JOIN
                                    hrm_location d ON c.hrm_location_id = d.id
                                        JOIN
                                    hrm_month e ON a.hrm_month_id = e.id
                                        JOIN
                                    hrm_section f ON b.hrm_section_id = f.id");


        return view('plantsetup.edit_plantwisesalary')
             ->with('month',$month)
             ->with('edit_data',$editdata);
    }



    public function store(Request $request)
    {

    }


    public function plantwisesalary(Request $request)
    {


         // dd("okk");

         $validator = Validator::make($request->all(), [
            'plant_name'    => 'required',
            'month_name'    => 'required',
            'hrm_plant_with_section_id'       => 'required',
            'year'          => 'required',
            'days'          => 'required',
            'maximum_rate'  => 'required',
            'minimum_rate'  => 'required',


        ]);

        if ($validator->fails()) {
            return redirect('plantwisesalary')
                        ->withErrors($validator)
                        ->withInput();
        }

        $check_data=DB::SELECT("SELECT id
                                FROM hrm_plant_details
                                WHERE hrm_plant_id= $request->plant_name
                                AND hrm_plant_with_section_id= $request->hrm_plant_with_section_id
                                AND hrm_month_id= $request->month_name
                                AND year_id= $request->year ");

        if(!empty($check_data)){
            $request->session()->flash('alert-danger', 'Sorry This Month Salary Setup Already Done, Only Editable!');
            return Redirect::to('plant_setup');
        }


        $insert = new HrmPlantDetails;
        $insert->hrm_plant_id     = $request->plant_name;
        $insert->hrm_month_id     = $request->month_name;
        $insert->hrm_plant_with_section_id   = $request->hrm_plant_with_section_id;
        $insert->year_id          = $request->year;
        $insert->working_days     = $request->days;
        $insert->max_rate         = $request->maximum_rate;
        $insert->minimum_rate     = $request->minimum_rate;
        $insert->users_id         = Auth::user()->id;

        $insert->save();

        $this->recordActivity(
            1,
           'Created Plant Wise CW Salary',
            $insert,
            $insert->id,
            'hrm_plant_details'
        );

       // dd("NOMAN");

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('plant_setup');
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
    public function edit($id){


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


        public function update_plantwise_salary(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'plant_name'    => 'required',
            'month_name'    => 'required',
            'hrm_plant_with_section_id'       => 'required',
            'year'          => 'required',
            'days'          => 'required',
            'maximum_rate'  => 'required',
            'minimum_rate'  => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }


        $insert = HrmPlantDetails::find($request->id);
        $insert->hrm_plant_id     = $request->plant_name;
        $insert->hrm_month_id     = $request->month_name;
        $insert->hrm_plant_with_section_id   = $request->hrm_plant_with_section_id;
        $insert->year_id          = $request->year;
        $insert->working_days     = $request->days;
        $insert->max_rate         = $request->maximum_rate;
        $insert->minimum_rate     = $request->minimum_rate;
        $insert->users_id         = Auth::user()->id;;
        $insert->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('plant_setup');
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




}
