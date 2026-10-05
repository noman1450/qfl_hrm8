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


use App\Models\HrmEmployeeInactive;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeActivity;



class EmployeeInactiveController extends Controller
{




    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
          $user_id = Auth::user()->id;
          $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('employee_inactive.employee_inactive_list')
          -> with('default_user_location',  $default_user_location) ;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee_inactive.create_employee_inactive');
    }


    public function reactive()
    {

      $user_id = Auth::user()->id;
      $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_inactive.reactive_list')
          -> with('default_user_location',  $default_user_location) ;

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


   public function employeeinactivelist(Request $request){

        $condition='';

        if($request->location==null){
        }else{
            $condition = ' AND b.hrm_location_id ='.$request->location;
        }

        $user_id=Auth::user()->id;
        $employee_inactive_list = DB::select("SELECT a.id,
                                                    c.id as employee_id,
                                                    Concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                                    e.depertment_name,
                                                    f.designation_name,
                                                    a.date_from,
                                                    a.date_to,
                                                    a.comment,
                                                    a.status,
                                                    (CASE
                                                    WHEN a.status=0 THEN 'Processing '
                                                    WHEN a.status=1 THEN 'Inactive'
                                                    ELSE 'Reactive'
                                                    END)  as status_value,
                                                    c.Images
                                            FROM hrm_employee_inactive a
                                            JOIN hrm_employee_job_info b on b.id=a.hrm_employee_job_info_id
                                            $condition
                                            JOIN hrm_employee c on c.id=b.hrm_employee_id
                                            Join hrm_location d On b.hrm_location_id=d.id
                                            JOIN hrm_depertment e On b.hrm_depertment_id=e.id
                                            Join hrm_designation f On b.hrm_designation_id=f.id
                                            JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                                            AND c.active_status=1  AND a.status in (0,1)") ;


        return json_encode(array('data' => $employee_inactive_list));

    }



   public function employeereactive_list(Request $request){

        $condition='';

        if($request->location==null){
        }else{
            $condition = ' AND b.hrm_location_id ='.$request->location;
        }

        $user_id=Auth::user()->id;


        $employee_inactive_list = DB::select("SELECT a.id,
                                c.id as employee_id,
                                Concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                a.date_from,
                                a.date_to,
                                a.comment,
                                a.status,
                                (CASE
                                WHEN a.status=0 THEN 'Processing '
                                WHEN a.status=1 THEN 'Inactive'
                                ELSE 'Reactive'
                                END)  as status_value,
                                c.Images
                                FROM hrm_employee_inactive a
                                JOIN hrm_employee_job_info b on b.id=a.hrm_employee_job_info_id
                                $condition
                                JOIN hrm_employee c on c.id=b.hrm_employee_id
                                Join hrm_location d On b.hrm_location_id=d.id
                                JOIN hrm_depertment e On b.hrm_depertment_id=e.id
                                Join hrm_designation f On b.hrm_designation_id=f.id
                                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                                and c.active_status=1   and a.status=2") ;


        return json_encode(array('data' => $employee_inactive_list));

    }









    public function store(Request $request)
    {

        // dd($request->all());

         $validator = Validator::make($request->all(), [
            'employee_name'              => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeeinactive/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $date_from      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from))) ;
        $date_to        = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $xmasDay        = new DateTime($date_from.'- 1 day');
        $end_date       = $xmasDay->format('Y-m-d');





        $hrm_employee_job_info_id=DB::Select("SELECT id FROM hrm_employee_job_info
                                                WHERE hrm_employee_id= $request->employee_name AND
                                                employee_activity=1");

        $job_id = $hrm_employee_job_info_id[0]->id;

        $check_data = DB::select("SELECT start_date
                                    FROM hrm_employee_activity
                                    WHERE hrm_employee_job_info_id =$job_id
                                    AND end_date is null");


        if($date_from<$check_data[0]->start_date){
             $request->session()->flash('alert-danger', 'Sorry you can not give back date employee activity!');
                return redirect('employeeinactive');

        }


            DB::beginTransaction();
            try{

                $insert     = new HrmEmployeeInactive;
                $insert->hrm_employee_job_info_id     = $hrm_employee_job_info_id[0]->id;
                $insert->date_from                    = $date_from;
                $insert->date_to                      = $date_to;
                $insert->comment                      = $request->comment;
                $insert->users_id                     = Auth::user()->id;;
                $insert->save();



                // DB::update("UPDATE  hrm_employee_activity SET end_date='$end_date' WHERE end_date is NULL AND
                //             hrm_employee_job_info_id = $job_id  ");

                // THIS UPDATE WILL BE APPLY WHEN ATTENDANCE IS PROCESSING......


            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Inactive Employee',
                 null,
                 $insert->id,
                 'hrm_employee_inactive'
            );

            }catch (\Exception $e) {
                DB::rollback();
                $validator->errors()->add('field', $e->getMessage());
                return response()->json($validator->errors()->all());
                // return response()->json(['errors'=>$validator->errors()->add('field', $e->getMessage())]);

            }



        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('employeeinactive');
    }


    public function employee_reactive($id)
    {

        $employee_date = DB::select("SELECT a.id,
                                c.id as employee_id,
                                Concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                a.date_from,
                                d.location_name,
                                a.date_to,
                                a.comment,
                                a.status,
                                (CASE
                                WHEN a.status=0 THEN 'Processing '
                                WHEN a.status=1 THEN 'Inactive'
                                ELSE 'Reactive'
                                END)  as status_value,
                                c.Images
                                FROM hrm_employee_inactive a
                                JOIN hrm_employee_job_info b on b.id=a.hrm_employee_job_info_id and a.id=$id
                                JOIN hrm_employee c on c.id=b.hrm_employee_id
                                Join hrm_location d On b.hrm_location_id=d.id
                                JOIN hrm_depertment e On b.hrm_depertment_id=e.id
                                Join hrm_designation f On b.hrm_designation_id=f.id
                                ") ;
        return view('employee_inactive.employee_reactive')
        -> with('employee_date',  $employee_date) ;





    }


   public function reactive_employee(Request $request)
    {

        $reactive_data = HrmEmployeeInactive::find($request->inactive_id);

        if (empty($reactive_data)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $currentdate   = date('Y-m-d');
        $reactive_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->reactive_date)));
        $xmasDay       = new DateTime($reactive_date.'- 1 day');
        $end_date      = $xmasDay->format('Y-m-d');


        $check_data    = DB::SELECT("SELECT * FROM hrm_employee_inactive WHERE id=$request->inactive_id AND date_from>'$reactive_date' ");

        if (!empty($check_data)){
            $request->session()->flash('alert-danger', 'you can not give back date data process!');
            return Redirect::to('employeeinactive');

        }

       $employee_activity_data    = DB::SELECT("SELECT id FROM hrm_employee_activity
        WHERE id in (SELECT max(id) FROM hrm_employee_activity WHERE hrm_employee_job_info_id=$reactive_data->hrm_employee_job_info_id)");
       $employee_activity_data   = $employee_activity_data[0]->id;

        DB::update("UPDATE  hrm_employee_inactive SET status=2,date_to='$reactive_date' WHERE id=$request->inactive_id");
        DB::update("UPDATE  hrm_employee_job_info SET employee_activity=1  WHERE  id = $reactive_data->hrm_employee_job_info_id");
        DB::update("UPDATE  hrm_employee_activity SET end_date='$end_date' WHERE  id = $employee_activity_data");




        $insert_employee_activity  = new HrmEmployeeActivity;
        $insert_employee_activity->hrm_employee_job_info_id  = $reactive_data->hrm_employee_job_info_id;
        $insert_employee_activity->activity_date             = $currentdate;
        $insert_employee_activity->comment                   = 'ReActive' ;
        $insert_employee_activity->users_id                  = Auth::user()->id;
        $insert_employee_activity->activity                  = 1;
        $insert_employee_activity->hrm_employee_activity_status_id  = 6;
        $insert_employee_activity->start_date                = $reactive_date;
        $insert_employee_activity->save();



        $request->session()->flash('alert-success', 'Successfully Reactive !');
        return Redirect::to('employeeinactive');


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
        //
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
        //
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

        $find = HrmEmployeeInactive::find($id);

        if (empty($find)){
            session()->flash('alert-danger', 'Invalid Application !!');
            return Redirect()->back();
        }

        DB::update("UPDATE  hrm_employee_job_info SET employee_activity=1 WHERE  id = $find->hrm_employee_job_info_id");
        DB::table('hrm_employee_inactive')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Employee Inactive',
             null,
             $id,
             'hrm_employee_inactive'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeinactive');

    }



}
