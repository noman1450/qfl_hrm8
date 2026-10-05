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


use App\Models\HrmEmployeeLongService;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeActivity;



class EmployeeLongServiceController extends Controller
{




    function __construct(){
        $this->middleware('auth');
    }
    /**
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $user_id = Auth::user()->id;
          $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('employee_longservice.employee_longservice_list')
          -> with('default_user_location',  $default_user_location) ;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee_longservice.create_employee_longservice');
    }


    public function service_completed_list()
    {
          $user_id = Auth::user()->id;
          $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('employee_longservice.service_completed_list')
          -> with('default_user_location',  $default_user_location) ;

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



   public function employeelongservice_list(Request $request){


        $condition='';

        if($request->location==null){
        }else{
            $condition = ' AND b.hrm_location_id ='.$request->location;
        }

        if($request->duration==0){
        }else{
            $condition = $condition.' AND TIMESTAMPDIFF(YEAR, f.confirmation_date,CURDATE()) IN ('.$request->duration.')';
        }


        $user_id=Auth::user()->id;
        $currentemployee = DB::select("SELECT
                                            a.id,
                                            concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                            c.location_name,
                                            d.depertment_name,
                                            a.Images,
                                            e.alis AS designation_name,
                                            a.contact_number,
                                            f.joining_date,
                                            f.confirmation_date,
                                            CONCAT(TIMESTAMPDIFF(YEAR,
                                                        f.confirmation_date,
                                                        CURDATE()),
                                                    ' Y(s) ',
                                                    MOD(TIMESTAMPDIFF(MONTH,
                                                            f.confirmation_date,
                                                            CURDATE()),
                                                        12),
                                                    ' M(s) ') AS jobduration
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                AND a.active_status = 1
                                                AND b.employee_activity = 1

                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                             $condition") ;


        return json_encode(array('data' => $currentemployee));

    }


    public function createnew($id)
    {


        $data = DB::select("SELECT
                                    a.id,
                                    concat(a.employee_name,' | ',b.employee_code,' | ',e.alis,' | ',d.depertment_name) as employee_name,
                                    c.location_name,
                                    d.depertment_name,
                                    e.alis AS designation_name,
                                    a.contact_number,
                                    f.joining_date,
                                    a.Images,
                                    f.confirmation_date,
                                    CONCAT(TIMESTAMPDIFF(YEAR,
                                                f.confirmation_date,
                                                CURDATE()),
                                            ' Y(s) ',
                                            MOD(TIMESTAMPDIFF(MONTH,
                                                    f.confirmation_date,
                                                    CURDATE()),
                                                12),
                                            ' M(s) ') AS jobduration
                                FROM
                                    hrm_employee a
                                        JOIN
                                    hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                        AND a.active_status = 1
                                        AND b.employee_activity = 1
                                        AND a.id=$id
                                        JOIN
                                    hrm_location c ON b.hrm_location_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_designation e ON b.hrm_designation_id = e.id
                                        JOIN
                                    hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                     ") ;

        return view('employee_longservice.create_employee_longservice')
                ->with('data' , $data);
    }


   public function service_completed_list_data(Request $request){


        $condition='';

        if($request->location==null){
        }else{
            $condition = ' AND b.hrm_location_id ='.$request->location;
        }
        $user_id=Auth::user()->id;
        $currentemployee = DB::select("SELECT
                                            a.id,
                                            a.Images,
                                            concat(a.employee_name,' | ',b.employee_code,' | ',e.alis,' | ',d.depertment_name) as employee_name,
                                            c.location_name,
                                            d.depertment_name,
                                            e.alis AS designation_name,
                                            a.contact_number,
                                            f.confirmation_date,
                                            g.service_completed_date,
                                            g.payment_date,
                                            g.duration,
                                            g.amount,
                                            g.comment

                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                AND a.active_status = 1
                                                AND b.employee_activity = 1
                                                $condition
                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                                JOIN
                                            hrm_employee_long_service g ON a.id=g.hrm_employee_id") ;


        return json_encode(array('data' => $currentemployee));

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
            return redirect('employeelongservice/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $service_completed_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->service_completed_date))) ;
        $payment_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date)));


        $check_data = DB::select("SELECT id FROM hrm_employee_long_service  WHERE hrm_employee_id =$request->employee_name
            AND duration=$request->duration");



        if(!empty($check_data)){
             $request->session()->flash('alert-danger', 'Sorry This Employee already created!');
             return redirect('employeelongservice');

        }

        $insert     = new HrmEmployeeLongService;
        $insert->hrm_employee_id        = $request->employee_name ;
        $insert->service_completed_date = $service_completed_date;
        $insert->payment_date           = $payment_date;
        $insert->duration               = $request->duration;
        $insert->amount                 = $request->payble_amount;
        $insert->comment                = $request->comment;
        $insert->users_id               = Auth::user()->id;;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Employee Long Service',
             null,
             $insert->id,
             'hrm_employee_long_service'
        );


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('service_completed_list');
    }


    // public function createnew($id)
    // {
    // $employee_date = DB::select("SELECT
    //                         a.id,
    //                         concat(a.employee_name,' | ',b.employee_code) as employee_name,
    //                         c.location_name,
    //                         d.depertment_name,

    //                         e.alis AS designation_name,
    //                         a.contact_number,
    //                         f.joining_date,
    //                         f.confirmation_date,
    //                         CONCAT(TIMESTAMPDIFF(YEAR,
    //                                     f.confirmation_date,
    //                                     CURDATE()),
    //                                 ' Y(s) ',
    //                                 MOD(TIMESTAMPDIFF(MONTH,
    //                                         f.confirmation_date,
    //                                         CURDATE()),
    //                                     12),
    //                                 ' M(s) ') AS jobduration
    //                     FROM
    //                         hrm_employee a
    //                             JOIN
    //                         hrm_employee_job_info b ON a.id = b.hrm_employee_id
    //                             AND a.active_status = 1
    //                             AND b.employee_activity = 1
    //                             AND a.id = $id
    //                             JOIN
    //                         hrm_location c ON b.hrm_location_id = c.id
    //                             JOIN
    //                         hrm_depertment d ON b.hrm_depertment_id = d.id
    //                             JOIN
    //                         hrm_designation e ON b.hrm_designation_id = e.id
    //                             JOIN
    //                         hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id ") ;

    //     return view('employee_longservice.create_employee_longservice')
    //     -> with('employee_date',  $employee_date) ;
    // }


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

       $employee_activity_data    = DB::SELECT("SELECT id FROM hrm_employee_activity WHERE id in (SELECT max(id) FROM hrm_employee_activity WHERE hrm_employee_job_info_id=$reactive_data->hrm_employee_job_info_id)");
       $employee_activity_data   = $employee_activity_data[0]->id;

        DB::update("UPDATE  hrm_employee_inactive SET status=2,date_to='$reactive_date' WHERE id=$request->inactive_id");
        DB::update("UPDATE  hrm_employee_job_info SET employee_activity=1 WHERE  id = $reactive_data->hrm_employee_job_info_id");
        DB::update("UPDATE  hrm_employee_activity SET end_date='$end_date' WHERE id = $employee_activity_data");




        $insert_employee_activity  = new HrmEmployeeActivity;
        $insert_employee_activity->hrm_employee_job_info_id  = $reactive_data->hrm_employee_job_info_id;
        $insert_employee_activity->activity_date             = $currentdate;
        $insert_employee_activity->comment                   = $request->note ;
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

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeinactive');

    }



}
