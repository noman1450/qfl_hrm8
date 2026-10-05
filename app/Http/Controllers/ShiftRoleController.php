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

use App\Models\HrmShiftRole;


class ShiftRoleController extends Controller
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
          return view('shift_role.shiftrole_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          return view('shift_role.create_shiftrole');
        //
    }



    public function shiftrolelist(){

      $listdata=DB::SELECT("SELECT a.*,b.location_name FROM  hrm_shift_role a join hrm_location b ON a.hrm_location_id=b.id ");
                return json_encode(array('data' => $listdata));


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
            'shift_role_name'   => 'required|unique:hrm_shift_role,shift_role_name|max:255',
            'location'          =>'required'
        ]);

        if ($validator->fails()) {
            return redirect('shiftrole/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $insert = new HrmShiftRole;
        $insert->shift_role_name          = $request->shift_role_name;
        $insert->hrm_location_id          = $request->location;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Shift Role Name',
             null,
             $insert->id,
             'hrm_shift_role'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('shiftrole');
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



          $edit_data=DB::SELECT("SELECT a.*,b.location_name FROM  hrm_shift_role a join hrm_location b ON a.hrm_location_id=b.id where a.id=$id");



        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid shift Role !!');
            return Redirect()->back();
        }

        return view('shift_role.edit_shiftrole')
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
            'shift_role_name'   => 'required|max:255',
            'location'          =>'required'
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmShiftRole::find($id);
        $insert->shift_role_name          = $request->shift_role_name;
        $insert->hrm_location_id          = $request->location;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Shift Role Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_shift_role'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('shiftrole');
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
