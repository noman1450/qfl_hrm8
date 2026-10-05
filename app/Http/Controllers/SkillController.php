<?php

namespace App\Http\Controllers;

use App\Models\{HrmSkill, HrmSkillEmployeewise};
use Illuminate\Http\Request;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;
use Response;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('skill.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'skill_name' => 'required|string|max:255|unique:hrm_skill,skill_name'
        ]);

        if( $validator->fails() ){
            return response::json(array(
                'success'   => false,
                'message'  => 'Failure!, Validation failed!'
            ));
        }

        DB::beginTransaction();
        try {
            $skill = HrmSkill::find($request->skill_id);
            // dd($skill);
            if ($skill==null){
                $skill = new Hrmskill;
            }

            $skill->skill_name = $request->skill_name;
            $skill->users_id = auth()->user()->id;

            $skill->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response::json(array(
                'success'   => false,
                'message'   => 'Failure!'
            ));
        }
        // Session::flash('message','Successfully Insert!');
        // Session::flash('alert-type','success');

        return response::json(array(
            'success'   => true,
            'message'   => 'Success!'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\HrmSkill  $hrmSkill
     * @return \Illuminate\Http\Response
     */
    public function show(HrmSkill $hrmSkill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\HrmSkill  $hrmSkill
     * @return \Illuminate\Http\Response
     */
    public function edit($hrmSkill)
    {
        dd($hrmSkill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\HrmSkill  $hrmSkill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrmSkill $hrmSkill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\HrmSkill  $hrmSkill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hrmSkill = HrmSkill::where('id', $id)->first();
        $hrmSkillEmployeewise = HrmSkillEmployeewise::where('hrm_skill_id', $id)->first();

        if ($hrmSkillEmployeewise==null){
            $hrmSkill->delete();
            session()->flash('success', 'data has been successfully deleted!');
            return redirect()->route('skill.index');
        }else{
            session()->flash('warning', 'data has a dependency');
            return redirect()->route('skill.index');
        }
    }

    public function skill_list()
    {
        $skill_list = DB::select("SELECT * FROM hrm_skill");
        return json_encode(array('data' => $skill_list));
    }

    public function skill_list_select(Request $request)
    {
        $skill_list = [];
        if ($request->type == 1){
            $skill_list = DB::select(
            "   SELECT id, skill_name as text
                FROM hrm_skill
                WHERE skill_name LIKE '%$request->term%'
            ");
        }else if ($request->type == 2){
            $skill_list = DB::select
            ("  SELECT
                     a.id as id, a.skill_name as text
                FROM
                    hrm_skill a Where a.id Not IN (select hrm_skill_id from hrm_skill_employeewise where hrm_employee_id = $request->id AND is_valid = 1)
            ");
        }

        // dd($skill_list);
        return response()->json($skill_list);
    }
}
