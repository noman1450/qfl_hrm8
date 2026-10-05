<?php

namespace App\Http\Controllers;

use App\Models\HrmSkillEmployeewise;
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

class SkillEmployeewiseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('skillemployeewise.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $datac = DB::select(
        "   SELECT
                c.id as id,
                b.skill_name as skill_name,
                b.id as skill_id,
                a.description as description
            FROM
                hrm_skill_employeewise as a
            JOIN
                hrm_skill as b on b.id = a.hrm_skill_id AND a.is_valid = 1
            JOIN
                hrm_employee as c ON c.id = a.hrm_employee_id
            JOIN
                hrm_employee_job_info d ON c.id=d.hrm_employee_id AND  c.active_status=1 AND d.employee_activity=1
        ");

        $num = DB::select("SELECT * FROM hrm_skill");

        // dd($datac, $num);
        return view('skillemployeewise.create', compact('datac', 'num'));
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
            'hrm_employee_id' => 'required',
            'chk' => 'required'
        ]);

        if( $validator->fails() ){
            return response::json(array(
                'success'   => false,
                'message'  => 'Failure!, Validation failed!'
            ));
        }

        HrmSkillEmployeewise::where('hrm_employee_id', $request->hrm_employee_id)->update(['is_valid' => 0]);

        DB::beginTransaction();
        try {
            for ($i = 0; $i<count($request->description); $i++){
                if (!empty($request->chk[$i])){
                    $SkillEmployeewise = new HrmSkillEmployeewise;
                    $SkillEmployeewise->hrm_employee_id = $request->hrm_employee_id;
                    $SkillEmployeewise->hrm_skill_id = $request->chk[$i];
                    $SkillEmployeewise->is_valid = 1;
                    $SkillEmployeewise->description = $request->description[$i];
                    $SkillEmployeewise->users_id = auth()->user()->id;
                    $SkillEmployeewise->save();
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            return response::json(array(
                'success'   => false,
                'message'   => 'Failure!'
            ));
        }

        return response::json(array(
            'success'   => true,
            'message'   => 'Success!'
        ));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\models\HrmSkillEmployeewise  $hrmSkillEmployeewise
     * @return \Illuminate\Http\Response
     */
    public function show(HrmSkillEmployeewise $hrmSkillEmployeewise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\models\HrmSkillEmployeewise  $hrmSkillEmployeewise
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = DB::select
        ("  SELECT
                f.id as id,
                l.skill_name,
                l.id as skill_id,
                CONCAT(f.employee_name, ' | ', g.employee_code, ' | ', j.designation_name, ' | ', i.depertment_name, ' | ', h.location_name) as employee
            FROM
                hrm_employee as f
            JOIN
                hrm_employee_job_info g ON f.id=g.hrm_employee_id AND  f.active_status=1 AND g.employee_activity=1 AND f.id=$id
            JOIN
                hrm_location h ON g.hrm_location_id=h.id
            JOIN
                hrm_depertment i ON g.hrm_depertment_id=i.id
            JOIN
                hrm_designation j ON g.hrm_designation_id=j.id
            JOIN
                hrm_skill_employeewise k ON k.hrm_employee_id = f.id AND k.is_valid = 1
            JOIN
                hrm_skill l ON l.id = k.hrm_skill_id
        ");
        // $arr = $data->toArray();
        // dd($data);

        return view('skillemployeewise.edit', compact('id', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\models\HrmSkillEmployeewise  $hrmSkillEmployeewise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $activity = DB::table('hrm_skill_employeewise')->where('is_valid','=',0)
            ->where('id','=',$id)->first();
            // dd($activity);

        if (!empty($activity)){
                return response::json(array(
                    'success'   => false,
                    'message'  => 'This data already updated, Please check!',
                ));
        }

        // $d = HrmSkillEmployeewise::where('hrm_employee_id', $id)->get();
        // dd($d);

        $validator = Validator::make($request->all(), [
            'hrm_employee_id' => 'required',
            'chk' => 'required'
        ]);

        if( $validator->fails() ){
            return response::json(array(
                'success'   => false,
                'message'  => 'Failure!, Validation failed!'
            ));
        }

        // dd($checkSkillEmployeewise);

        HrmSkillEmployeewise::where('hrm_employee_id', $id)->update(['is_valid' => 0]);

        $checkSkillEmployeewise = HrmSkillEmployeewise::where('hrm_employee_id', $request->hrm_employee_id)
        ->where('is_valid', '=', 1)
        ->get();

        $arr = [];
        for ($i = 0; $i<count($checkSkillEmployeewise); $i++){
            $arr[$i] = $checkSkillEmployeewise[$i]->hrm_skill_id;
        }
        if ( !$checkSkillEmployeewise->count() )
        {
            DB::beginTransaction();
            try {
                for ($i = 0; $i<count($request->chk); $i++){
                    $SkillEmployeewise = new HrmSkillEmployeewise;
                    $SkillEmployeewise->hrm_employee_id = $request->hrm_employee_id;
                    $SkillEmployeewise->hrm_skill_id = $request->chk[$i];
                    $SkillEmployeewise->note = $request->note;
                    $SkillEmployeewise->is_valid = 1;
                    $SkillEmployeewise->users_id = auth()->user()->id;
                    $SkillEmployeewise->save();
                }
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
                return response::json(array(
                    'success'   => false,
                    'message'   => 'Failure!'
                ));
            }

            return response::json(array(
                'success'   => true,
                'message'   => 'Success!'
            ));

        }else {
            DB::beginTransaction();
            try {

                for($j=0; $j<count($arr); $j++){
                    for ($i = 0; $i<count($request->chk); $i++){
                    // dd($request->chk);
                        // dd($request->chk);
                        if (in_array(($request->chk[$i]), ($arr)) == false) {
                            // dd($checkSkillEmployeewise[$j]);
                            $SkillEmployeewise = new HrmSkillEmployeewise;
                            $SkillEmployeewise->hrm_employee_id = $request->hrm_employee_id;
                            $SkillEmployeewise->hrm_skill_id = $request->chk[$i];
                            $SkillEmployeewise->note = $request->note;
                            $SkillEmployeewise->is_valid = 1;
                            $SkillEmployeewise->users_id = auth()->user()->id;
                            $SkillEmployeewise->save();
                        }
                    }
                }
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
                'message'   => 'Warning! (1 or more skill already exists)'
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\HrmSkillEmployeewise  $hrmSkillEmployeewise
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hrmSkillEmployeewise = HrmSkillEmployeewise::where('hrm_employee_id', $id)->get();
        // dd(count($hrmSkillEmployeewise));
        for ($i=0; $i<count($hrmSkillEmployeewise); $i++){
            $hrmSkillEmployeewise[$i]->is_valid = 0;
            $hrmSkillEmployeewise[$i]->save();
        }
        session()->flash('success', 'data has been successfully deleted!');
        return redirect()->route('skillemployeewise.index');
    }

    public function skill_employeewise_list(Request $request)
    {
        // dd($request->all());
        if ($request->skill_id == null && $request->location == null && $request->department == null){
            $skill_employeewise_list = DB::select(
            "   SELECT
                    c.id as id,
                    CONCAT(c.employee_name, ' | ', d.employee_code, ' | ', g.designation_name, ' | ', f.depertment_name, ' | ', e.location_name) as employee,
                    c.Images as imgpath,
                    GROUP_CONCAT( CONCAT( b.skill_name ) SEPARATOR '<br>') AS skill_name,
                    GROUP_CONCAT( CONCAT( a.description ) SEPARATOR '<br>') AS description,
                    GROUP_CONCAT( CONCAT( b.skill_name ) SEPARATOR ' | ') AS skill_name2
                FROM
                    hrm_skill_employeewise as a
                JOIN
                    hrm_skill as b on b.id = a.hrm_skill_id AND a.is_valid = 1
                JOIN
                    hrm_employee as c ON c.id = a.hrm_employee_id
                JOIN
                    hrm_employee_job_info d ON c.id=d.hrm_employee_id AND  c.active_status=1 AND d.employee_activity=1
                JOIN
                    hrm_location e ON d.hrm_location_id=e.id
                JOIN
                    hrm_depertment f ON d.hrm_depertment_id=f.id
                JOIN
                    hrm_designation g ON d.hrm_designation_id=g.id
                GROUP BY
                    id, employee, imgpath
            ");
        }else{
            $temp = 1;
            $skill_id = "";
            $location = "";
            $department = "";
            if($request->skill_id != null){
                $skill_id = "AND b.id = $request->skill_id";
            }
            if($request->location != null){
                $location = "AND e.id = $request->location";
            }
            if($request->department != null){
                $department = "AND f.id = $request->department";
            }

            $skill_employeewise_list = DB::select(
            "   SELECT
                    c.id as id,
                    CONCAT(c.employee_name, ' | ', d.employee_code, ' | ', g.designation_name, ' | ', f.depertment_name, ' | ', e.location_name) as employee,
                    c.Images as imgpath,
                    GROUP_CONCAT( CONCAT( b.skill_name ) SEPARATOR '<br>') AS skill_name,
                    GROUP_CONCAT( CONCAT( a.description ) SEPARATOR '<br>') AS description,
                    GROUP_CONCAT( CONCAT( b.skill_name ) SEPARATOR ' | ') AS skill_name2
                FROM
                    hrm_skill_employeewise as a
                JOIN
                    hrm_skill as b on b.id = a.hrm_skill_id
                JOIN
                    hrm_employee as c ON c.id = a.hrm_employee_id
                JOIN
                    hrm_employee_job_info d ON c.id=d.hrm_employee_id AND  c.active_status=1 AND d.employee_activity=1
                JOIN
                    hrm_location e ON d.hrm_location_id=e.id
                JOIN
                    hrm_depertment f ON d.hrm_depertment_id=f.id
                JOIN
                    hrm_designation g ON d.hrm_designation_id=g.id
                WHERE
                    a.is_valid = $temp $skill_id $location $department
                GROUP BY
                    id, employee, imgpath
            ;");
        }

        return json_encode(array('data' => $skill_employeewise_list));
    }

    public function skill_employeewise_list_get(Request $request)
    {
        if ($request->employee_id == null){

            $skill_employeewise_list = DB::select(
            "   SELECT
                    c.id as id,
                    b.skill_name as skill_name,
                    b.id as skill_id
                FROM
                    hrm_skill_employeewise as a
                JOIN
                    hrm_skill as b on b.id = a.hrm_skill_id AND a.is_valid = 1
                JOIN
                    hrm_employee as c ON c.id = a.hrm_employee_id
                JOIN
                    hrm_employee_job_info d ON c.id=d.hrm_employee_id AND  c.active_status=1 AND d.employee_activity=1
                JOIN
                    hrm_location e ON d.hrm_location_id=e.id
                JOIN
                    hrm_depertment f ON d.hrm_depertment_id=f.id
                JOIN
                    hrm_designation g ON d.hrm_designation_id=g.id
            ");
        }else {
            $skill_employeewise_list = DB::select(
                "   SELECT
                        c.id as id,
                        b.skill_name as skill_name,
                        b.id as skill_id,
                        a.description
                    FROM
                        hrm_skill_employeewise as a
                    JOIN
                        hrm_skill as b on b.id = a.hrm_skill_id AND a.is_valid = 1 AND a.hrm_employee_id = $request->employee_id
                    JOIN
                        hrm_employee as c ON c.id = a.hrm_employee_id
                    JOIN
                        hrm_employee_job_info d ON c.id=d.hrm_employee_id AND  c.active_status=1 AND d.employee_activity=1
                    JOIN
                        hrm_location e ON d.hrm_location_id=e.id
                    JOIN
                        hrm_depertment f ON d.hrm_depertment_id=f.id
                    JOIN
                        hrm_designation g ON d.hrm_designation_id=g.id
                ");
        }

        return json_encode(array('data' => $skill_employeewise_list));
    }
}
