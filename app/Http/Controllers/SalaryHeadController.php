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
use DateTime;


use App\Models\HrmSalaryHead;
use App\Models\HrmSetDefaultHead;
use App\Models\HrmSetPFConfiq;




class SalaryHeadController extends Controller
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
        return view('salary_head.salaryhead_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

         return view('salary_head.create_salaryhead');
    }

    public function defaultsalaryheadsetup()
    {

        $data = DB::SELECT("SELECT
                                a.id,
                                a.description,
                                b.hrm_salary_head_id,
                                a.head_name,
                                (SELECT
                                        salary_head
                                    FROM
                                        hrm_salary_head
                                    WHERE
                                        b.hrm_salary_head_id = hrm_salary_head.id) salary_head
                            FROM
                                hrm_default_head a
                                    LEFT JOIN
                                hrm_set_default_head b ON a.id = b.hrm_default_head_id WHERE a.status=1");

        $data_pf = DB::SELECT("SELECT * FROM
                                        (SELECT
                                            a.id,
                                            a.description,
                                            a.head_name,
                                            b.amount_own as amount,
                                            b.date_from
                                        FROM
                                            hrm_default_head a
                                                LEFT JOIN
                                            hrm_set_pf_confiq b ON a.id = b.hrm_default_head_id_own AND b.date_to IS NULL
                                        WHERE
                                            a.status = 2 AND a.head_name='own_contribution'
                                        UNION ALL
                                        SELECT
                                            a.id,
                                            a.description,
                                            a.head_name,
                                            b.amount_company as amount,
                                            b.date_from
                                        FROM
                                            hrm_default_head a
                                                LEFT JOIN
                                            hrm_set_pf_confiq b ON a.id = b.hrm_default_head_id_company AND b.date_to IS NULL
                                        WHERE
                                            a.status = 2  AND a.head_name='company_contribution' ) aa
                                        GROUP BY aa.id,aa.description,aa.head_name,aa.amount,aa.date_from");

         $calculation_on = HrmSetPFConfiq::find(1);

         if(empty($calculation_on)){
                $calculation_on  = 1;
         };



         return view('salary_head.defaultsalaryheadsetup')
                ->with('data',$data)
                ->with('calculation_on',$calculation_on)
                ->with('datapf',$data_pf);

    }





    public function providentfundconfiqu_data()
    {
           $data = DB::select("SELECT *,IFNULL(date_to,'Running..') as end_date  FROM hrm_set_pf_confiq ");
           return json_encode(array('data' => $data));

    }



    public function providentfundconfiquration(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'own_contribution_id'           => 'required',
            'own_contribution_amount'       => 'required',
            'company_contribution_id'       => 'required',
            'company_contribution_amount'   => 'required',
            'calculation_on'                => 'required',
            'start_date'                    => 'required',



        ]);


        if ($validator->fails()) {
            session()->flash('alert-danger', 'Sorry Filup Full Information Field!!');
            return Redirect()->back();
        }

        $start_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        $xmasDay              = new DateTime($start_date.'- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');


        $check_data = DB::select("SELECT * FROM hrm_set_pf_confiq WHERE date_to is null");

        if (!empty($check_data[0]->date_from)) {

          if($end_date<$check_data[0]->date_from){
                $request->session()->flash('alert-danger', 'Back date not allow!');
                return Redirect::to('defaultsalaryheadsetup');
          }

        }



        DB::beginTransaction();
            try{


                    DB::UPDATE("UPDATE hrm_set_pf_confiq SET date_to='$end_date' WHERE date_to is null ");

                    $insert = new HrmSetPFConfiq;
                    $insert->hrm_default_head_id_own     = $request->own_contribution_id;
                    $insert->amount_own                  = $request->own_contribution_amount;
                    $insert->amount_company              = $request->company_contribution_amount;
                    $insert->hrm_default_head_id_company = $request->company_contribution_id;
                    $insert->users_id                    = Auth::user()->id;
                    $insert->date_from                   = $start_date ;
                    $insert->calculation_on              = $request->calculation_on;
                    $insert->amount_type                 ='%';

                    $insert->save();



            DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'fail to insert!');

            }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('defaultsalaryheadsetup');




    }



    public function salaryhead_list(Request $request){


        $condition='';

        if($request->generate_type==null){

        }else{
            $condition = 'AND b.generate_type='.$request->generate_type;
        }

        if($request->apply_for==null){

        }else{
            $condition = $condition.' AND a.apply_for='.$request->apply_for;
        }



        $salaryhead_list = DB::select("SELECT
                                            a.id,
                                            a.salary_head,
                                            b.group_name,

                                            If((b.generate_type=1),'Addition','Deduction') as generate_type,
                                            If((a.apply_for=1),'Salary Sheet','Fringe Benefit') as adjust_with,
                                            a.is_delete

                                            FROM `hrm_salary_head` a
                                            JOIN hrm_salary_head_group b
                                            ON a.hrm_salary_head_group_id=b.id
                                            WHERE  active_status=1 $condition ");
        return json_encode(array('data' => $salaryhead_list));


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
        'salary_head'       => 'required|unique:hrm_salary_head,salary_head|max:55',
        'salaryheadgroup'   => 'required',
        ]);


        if ($validator->fails()) {
            session()->flash('alert-danger', 'This Salary Head  already exist!!');
            return Redirect()->back();
        }


        $insert = new HrmSalaryHead;
        $insert->hrm_salary_Head_group_id   = $request->salaryheadgroup;
        $insert->salary_head                = $request->salary_head;
        $insert->apply_for                  = $request->apply_for;
        $insert->active_status              = 1 ;
        $insert->users_id                   = Auth::user()->id;
        $insert->is_delete                  = 1 ;

        $insert->save();


        $this->recordActivity(
             1,
             'Created Salary Head',
             null,
             $insert->id,
             'hrm_salary_head'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('salaryhead');
    }



    public function setdefaultsalaryhead(Request $request)
    {

        // dd($request->all());

            $t    = $request->all(); unset($t['_token']);

            $data = array();

            // dd($t);
            DB::beginTransaction();
            try{

                DB::DELETE("DELETE FROM hrm_set_default_head");

                $user_id = Auth::user()->id;

                foreach ($t as $key => $value) {
                    // var_dump($key,$value);
                    // var_dump($key,$value);
                    array_push($data,array('hrm_default_head_id'=>$key, 'hrm_salary_head_id'=>$value, 'users_id'=>$user_id));

                }
                // dd(  $data );

                HrmSetDefaultHead::insert($data);

            DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
                
                $request->session()->flash('alert-danger', 'fail to insert!');

            }

            $request->session()->flash('alert-success', 'data has been successfully added!');
            return Redirect::to('defaultsalaryheadsetup');
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

        $edit_data=DB::SELECT("SELECT
                                    a.id,
                                    a.salary_head,
                                    b.group_name,
                                    b.id as group_head_id,
                                    a.apply_for
                                    FROM hrm_salary_head a
                                    JOIN hrm_salary_head_group b
                                    On a.hrm_salary_Head_group_id=b.id And active_status=1 Where a.id=$id");



       return view('salary_head.edit_salaryhead')
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
        'salary_head'       => 'required',
        'salaryheadgroup'   => 'required',
        ]);


        if ($validator->fails()) {
            session()->flash('alert-danger', 'This Salary Head Group already exist!!');
            return Redirect()->back();
        }


        $update =HrmSalaryHead::find($id);
        $update->hrm_salary_Head_group_id   = $request->salaryheadgroup;
        $update->salary_head                = $request->salary_head;
        $update->apply_for                  = $request->apply_for;

        $update->active_status              = 1 ;
        $update->users_id                   = Auth::user()->id;
        $update->is_delete                  = 1 ;

        $update->save();

        $this->recordActivity(
             1,
             'Updated Salary Head',
             $update->getChanges(),
             $update->id,
             'hrm_salary_head'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('salaryhead');
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

        $cancel = HrmSalaryHead::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Salary Head!!');
            return Redirect()->back();
        }

        $delete_data = HrmSalaryHead::find($id);
        $delete_data->active_status              = 0 ;
        $delete_data->users_id                   = Auth::user()->id;
        $delete_data->save();

        // DB::table('hrm_salary_head_group')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('salaryhead');

    }






}
