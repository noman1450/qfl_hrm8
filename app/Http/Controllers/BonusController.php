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


use App\Models\HrmBonus;

class BonusController extends Controller
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
        return view('bonus.bonus_list');
    }


    public function bonuslist(){
        return json_encode(array('data' => HrmBonus::all()));


        // $view_data = DB::select("SELECT
        //                             id,bonus_name
        //                         FROM
        //                             hrm_bonus");


        // $reservation_data   = collect($view_data);
        // return datatables()->of($reservation_data)
        // ->setRowId('id')
        // ->make(true);


    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bonus.create_bonus');
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
            'bonus_name'    => 'required|unique:hrm_bonus,bonus_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('bonus/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmBonus;
        $insert->bonus_name = $request->bonus_name;
        $insert->bonus_description = $request->bonus_description;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Bonus Type Setup Name',
             null,
             $insert->id,
             'hrm_bonus'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('bonus');
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
        $bonus      = HrmBonus::find($id);

        if (empty($bonus)){
            session()->flash('alert-danger', 'Invalid Bonus Name !!');
            return Redirect()->back();
        }

        return view('bonus.edit_bonus')->with('bonus',$bonus);
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
            // 'group_name'    => 'required|unique:hrm_blood_group,blood_group|max:255',
            'bonus_name'    => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmBonus::find($id);
        $insert->bonus_name = $request->bonus_name;
        $insert->bonus_description = $request->bonus_description;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Bonus Type Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_bonus'
        );

        $request->session()->flash('alert-success', 'successfully updated!');
        return Redirect::to('bonus');
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

        $cancel = HrmBonus::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid bonus info !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_bonus_master')->where('hrm_bonus_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this bonus Name !! This bonus aleady used to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_bonus')->where('id', '=', $id)->delete();

        // $this->recordActivity(
        //      1,
        //      'Deleted Bonus Type Name',
        //      null,
        //      $id,
        //      'hrm_bonus'
        // );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('bonus');

    }


}
