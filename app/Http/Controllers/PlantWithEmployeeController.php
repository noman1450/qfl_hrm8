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

class PlantWithEmployeeController extends Controller
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



    public function create()
    {

    }


    public function plantwiseemployeeadd()
    {
        return view('plantwithemployee.availableemployee_roleadd');
    }

    public function plantwiseemployeelist(){
        return view('plantwithemployee.palntwiseemployee_list');
    }



    public function remaining_plantrole_data(Request $request){


        $users_id    = Auth::user()->id;
        $hrm_plant =  HrmPlant::find($request->plant_name);

        if(!empty($request->plant_name)){
            $condition = ' AND b.hrm_location_id ='.$hrm_plant->hrm_location_id;
        }else{
            $condition = '';
        }

        $list           = DB::select("SELECT
                                b.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                h.employment_status as worker_type,
                                c.plant_name

                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                    AND b.id NOT IN (SELECT hrm_employee_job_info_id FROM hrm_plantwise_employee)
                                    $condition
                                    JOIN
                                hrm_plant c ON b.hrm_plant_id = c.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employment_status h ON b.hrm_employment_status_id=h.id AND h.id=3
                                    JOIN
                                user_location i ON g.id = i.hrm_location_id AND i.users_id = $users_id");


        return json_encode(array('data' => $list));

    }


   public function plantwise_employeelist(Request $request){


        $condition   = '';
        $users_id    = Auth::user()->id;

        if(!empty($request->plant_name)){
            $condition = " AND i.hrm_plant_id=".$request->plant_name;
        }

        if(!empty($request->hrm_plant_with_section_id)){
            $condition = " AND i.hrm_plant_with_section_id=".$request->hrm_plant_with_section_id;
        }


        $list        = DB::select("SELECT
                                b.id,
                                concat(a.employee_name,' | ',b.employee_code,' | ',f.designation_name,' | ',e.depertment_name) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                h.employment_status as worker_type,
                                c.section_name,
                                k.plant_name

                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id
                                    JOIN
                                hrm_section c ON b.hrm_section_id=c.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employment_status h ON b.hrm_employment_status_id=h.id
                                    JOIN
                                hrm_plantwise_employee i ON b.id=i.hrm_employee_job_info_id $condition
                                    JOIN
                                user_location j ON g.id = j.hrm_location_id AND j.users_id = $users_id
                                    Join
                                hrm_plant k ON i.hrm_plant_id=k.id");


        return json_encode(array('data' => $list));


    }




    public function store(Request $request)
    {


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


    public function plantroleemployeeadd(Request $request)
    {

        // dd($request->all());
        $count_row  = count($request->id);

        if(empty($count_row)){
            // $request->session()->flash('alert-success', 'data has been successfully save!');
            $request->session()->flash('alert-danger', "you can't submit without employee !!");
            return Redirect::to('plantwiseemployeeadd');
        }

        for($r = 0; $r <$count_row; $r++) {


                $employee_id                       = $request->id[$r];

                $insert                            = new HrmPlantWiseEmployee;
                $insert->hrm_plant_id              = $request->plant_name ;
                $insert->hrm_plant_with_section_id = $request->hrm_plant_with_section_id;
                $insert->hrm_employee_job_info_id  = $employee_id;



                $insert->save();
        }


        $request->session()->flash('alert-success', 'data has been successfully saved!');
        return Redirect::to('plantwiseemployeeadd');
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
    public function destroy($id)
    {
        //
    }



    public function cancel(Request $request,$id){

        DB::table('hrm_plantwise_employee')->where('hrm_employee_job_info_id', '=', $id)->delete();
        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('plantwiseemployeelist');
    }


    public function delete_all_plantwise_employee(Request $request){

        $count_row  = count($request->id);
        if(empty($count_row)){
            $request->session()->flash('alert-danger', "you can't submit without employee !!");
            return Redirect::to('plantwiseemployeelist');
        }

        for($r = 0; $r <$count_row; $r++) {
            $id = $request->id[$r];
            DB::table('hrm_plantwise_employee')->where('hrm_employee_job_info_id', '=', $id)->delete();
        }

        $request->session()->flash('alert-success', 'data has been successfully deleted!');
        return Redirect::to('plantwiseemployeelist');
    }





}
