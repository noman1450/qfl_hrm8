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
use App\Models\HrmPlantWithSection;


class PlantWithSectionController extends Controller
{



    function __construct(){
        $this->middleware('auth');
    }


    public function index(){

        return view('plantwithsection.plantwithsection_list');
    }



    public function create()
    {
        return view('plantwithsection.create_plantwithsection');
    }


    public function plantwiseemployeeadd()
    {
        return view('plant.availableemployee_roleadd');
    }

    public function plantwiseemployeelist(){
        return view('plant.palntwiseemployee_list');
    }


    public function plantwithsection_list(Request $request){

        $user_id=Auth::user()->id;

        $plant_list=DB::SELECT("SELECT
                                    a.id,
                                    CONCAT(b.plant_name,' -- ',c.location_name) as plant_name,
                                    d.section_name,
                                    a.valid
                                FROM
                                    hrm_plant_with_section a
                                        JOIN
                                    hrm_plant b ON a.hrm_plant_id = b.id
                                        AND b.valid = 1
                                        JOIN
                                    hrm_location c ON b.hrm_location_id = c.id AND c.valid = 1
                                        JOIN
                                    hrm_section d ON a.hrm_section_id=d.id
                                        JOIN
                                    user_location e ON c.id = e.hrm_location_id
                                        AND e.users_id = $user_id");

        return json_encode(array('data' =>$plant_list));

    }



    public function store(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'plant'        => 'required',
            'section'      => 'required',

        ]);


        if ($validator->fails()) {
            return redirect('plantwithsection/create')
                        ->withErrors($validator)
                        ->withInput();
        }



        $check_data=DB::SELECT("SELECT id
                                FROM hrm_plant_with_section
                                WHERE hrm_plant_id= $request->plant
                                AND hrm_section_id= $request->section ");

        if(!empty($check_data)){
            $request->session()->flash('alert-danger', 'Sorry,Already Exist!');
            return Redirect::to('plantwithsection');
        }





        $insert = new HrmPlantWithSection;
        $insert->hrm_plant_id     = $request->plant;
        $insert->hrm_section_id   = $request->section;
        $insert->users_id         = Auth::user()->id;
        $insert->valid            = 1;

        $insert->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('plantwithsection');
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
    public function destroy($id)
    {
        //
    }



    public function cancel(Request $request,$id){
        $user_id = Auth::user()->id;

        DB::UPDATE("UPDATE hrm_plant_with_section SET valid=0,users_id=$user_id WHERE id=$id");

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('plantwithsection');
    }


    public function reactive(Request $request,$id){
        $user_id = Auth::user()->id;

        DB::UPDATE("UPDATE hrm_plant_with_section SET valid=1,users_id=$user_id WHERE id=$id");

        $request->session()->flash('alert-success', 'successfully reactive !');
        return Redirect::to('plantwithsection');
    }



}
