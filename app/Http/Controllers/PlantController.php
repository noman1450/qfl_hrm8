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

class PlantController extends Controller
{



// update_plantwise_salary

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

        return view('plant.plant_list');
    }



    public function create()
    {
        return view('plant.create_plant');
    }




    public function plant_list(Request $request){

        $user_id=Auth::user()->id;

        $plant_list=DB::SELECT("SELECT
                                    a.id,
                                    a.plant_name,
                                    'All Location' location_name
                                FROM hrm_plant a WHERE a.hrm_location_id is null
                                UNION
                                SELECT
                                    a.id,
                                    a.plant_name,
                                    b.location_name
                                FROM hrm_plant a
                                JOIN hrm_location b ON a.hrm_location_id=b.id AND a.valid=1
                                JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id");


        return json_encode(array('data' =>$plant_list));

    }



    public function store(Request $request)
    {


         // dd($request->all());

        $validator = Validator::make($request->all(), [
            'plant_name'   => 'required|unique:hrm_plant,plant_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('plant/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmPlant;
        $insert->plant_name          = $request->plant_name;
        $insert->hrm_location_id     = $request->location;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Plant Name',
             null,
             $insert->id,
             'hrm_plant'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('plant');
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
        $edit_data = Hrmplant::find($id);
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid Plant Information !!');
            return Redirect()->back();
        }

        $editdata = DB::SELECT("SELECT a.id,
                                a.plant_name,
                                b.location_name,
                                b.id as location_id

                                FROM hrm_plant a JOIN hrm_location b ON a.hrm_location_id=b.id AND a.id=$id");

        return view('plant.edit_plant')
            ->with('edit_data',$editdata);
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

        // dd($request->all());
         $validator = Validator::make($request->all(), [
            'plant_name'   => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }


        $insert = HrmPlant::find($id);
        $insert->plant_name          = $request->plant_name;
        $insert->hrm_location_id     = $request->location;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Plant Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_plant'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('plant');
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



   public function plantnamecancel(Request $request,$id){

        $cancel = HrmPlant::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Plant Name !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_job_info')->where('hrm_plant_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this Plant !! This Plant use to employees ");
            return Redirect()->back();
        }

        $plant_employee   = DB::table('hrm_plantwise_employee')->where('hrm_plant_id','=',$id)->first();

        if (!empty($plant_employee)){
            session()->flash('alert-danger', "You can't delete this Plant !! This Plant use to employees ");
            return Redirect()->back();
        }



        // DB::table('hrm_plant')->where('id', '=', $id)->delete();

        DB::UPDATE("UPDATE hrm_plant SET valid=0 WHERE id=$id");

        $this->recordActivity(
             1,
             'Deleted Plant Name',
             null,
             $id,
             'hrm_plant'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('plant');

    }



}
