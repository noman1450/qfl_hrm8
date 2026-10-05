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
use App\Models\HrmSection;
class SectionController extends Controller
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
        return view('section.section_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('section.create_section');
    }

    public function sectionlist(){
        return json_encode(array('data' => HrmSection::all()));
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
            'section'   => 'required|unique:hrm_section,section_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('section/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $insert = new HrmSection;
        $insert->section_name          = $request->section;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Sub Department Name',
             null,
             $insert->id,
             'hrm_section'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('section');
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
        $edit_data = HrmSection::find($id);
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid section !!');
            return Redirect()->back();
        }

        return view('section.edit_section')
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
            // 'section'   => 'required|unique:hrm_section,section_name|max:255',
            'section'   => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }


        $insert = HrmSection::find($id);
        $insert->section_name          = $request->section;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Sub Department Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_section'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('section');
    }


    public function cancel(Request $request,$id){

        $cancel = HrmSection::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Section !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_employee_job_info')->where('hrm_section_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this Section !! This Section use to employees ");
            return Redirect()->back();
        }

        DB::table('hrm_section')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Sub Department Name',
             null,
             $id,
             'hrm_section'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('section');

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
