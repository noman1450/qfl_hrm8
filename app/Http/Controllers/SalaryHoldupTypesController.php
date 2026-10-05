<?php

namespace App\Http\Controllers;

use App\Models\SalaryHoldupTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SalaryHoldupTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {

        }
        return view('salary_holdup.slaray_holdup_types');
    }

    public function salaryHoldupList()
    {
        $slary_holdup_list = DB::SELECT("SELECT
                                            id, holdup_types_name, description, is_active
                                        FROM
                                            hrm_holdup_types");

        return json_encode(array('data' =>$slary_holdup_list));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('salary_holdup.create_salary_holdup');
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
            'holdup_types_name'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('salary_holdup/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new SalaryHoldupTypes();
        $insert->holdup_types_name     = $request->holdup_types_name;
        $insert->description           = $request->description;
        $insert->users_id              = auth()->id();
        $insert->created_at            = now();
        $insert->save();


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('salary_holdup');
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
        $edit_data = SalaryHoldupTypes::find($id);
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid Holiday Information !!');
            return Redirect()->back();
        }

        return view('salary_holdup.edit_salary_holdup')
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
            'holdup_types_name'   => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $insert = SalaryHoldupTypes::find($id);
        $insert->holdup_types_name     = $request->holdup_types_name;
        $insert->description           = $request->description;
        $insert->users_id              = auth()->id();
        $insert->updated_at            = now();
        $insert->save();


        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('salary_holdup');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        DB::table('hrm_holdup_types')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('salary_holdup');
    }


}
