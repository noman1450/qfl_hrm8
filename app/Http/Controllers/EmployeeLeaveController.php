<?php

namespace App\Http\Controllers;

use DateTime;
use Redirect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\HrmLeaveLedger;
use App\Models\HrmLeaveApprove;
use App\Models\HrmEmployeeLeave;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AttendanceDataProcessController;

class EmployeeLeaveController extends Controller
{
    public function index()
    {
        return view('employee_leave.employee_leave_list');
    }

    public function create()
    {
        return view('employee_leave.create_employee_leave');
    }

    public function approvedlist()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_leave.leave_approved_list')
            ->with('default_user_location',  $default_user_location) ;
    }

    public function leaverejectedlist()
    {
        return view('employee_leave.leave_rejected_list');
    }

    public function employeeleavelist()
    {

        $condition = '';
        $date_from  = date('Y-m-d', strtotime(str_replace('/', '-', request('date_from'))));
        $date_to  = date('Y-m-d', strtotime(str_replace('/', '-', request('date_to'))));

        if (request('apply_date_range') == 'true') {
            $condition = " and a.applied between '$date_from'  and '$date_to'";
        }





        $user_id = auth()->id();
        $employee = DB::select("SELECT
                b.id,
                a.id as leave_app_id,
                f.id as leave_approve_id,
                concat(b.employee_name,' | ',d.employee_code) as employee_name,
                c.leave_type,
                (CASE
                    WHEN a.payment_mode = 1 THEN 'With Pay'
                    WHEN a.payment_mode = 2 THEN 'Without Pay'
                    ELSE ''
                END) as payment_mode,
                (CASE
                WHEN a.duration=1 THEN '.50'
                WHEN a.duration=3 THEN '.25'
                ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                END) as days,
                a.comment,
                date_format(a.date_from, '%d %b, %Y') date_from,
                date_format(a.date_to, '%d %b, %Y') date_to,
                b.Images,
                g.name as apply_to,
                dd.location_name,
                concat(g.name, '|', ifnull(a.created_at,'')) as users_name
            FROM
                hrm_leave_application a
                    JOIN
                hrm_employee b on b.id = a.hrm_employee_id and b.active_status = 1
                $condition
                    JOIN
                hrm_employee_leave_type c On a.hrm_employee_leave_type_id = c.id
                    JOIN
                hrm_employee_job_info d On a.hrm_employee_id = d.hrm_employee_id and d.employee_activity = 1
                    JOIN
                hrm_location dd ON d.hrm_location_id = dd.id
                    JOIN
                user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                    JOIN
                hrm_leave_approve f ON f.hrm_leave_application_id = a.id
                And f.action_type is null
                LEFT JOIN
                users g ON a.users_id = g.id
        ");

        return json_encode(array('data' => $employee));
    }

    public function multipleApprove(Request $request)
    {

        $request->validate([
            'leave_app_id' => 'required'
        ], [
            'leave_app_id.required' => 'Please check at least one checkbox.'
        ]);
        // dd($request->all());
        foreach ($request->leave_app_id as $key => $value) {

            $leave_approve_id = $request->leave_approve_id[$key];

            $update_leave_approve = HrmLeaveApprove::find($leave_approve_id);
            $update_leave_approve->action_type = $request->action;
            $update_leave_approve->forward     = 2;
            $update_leave_approve->save();



            if ($request->action == 1) { // Approve
                $query_data = HrmEmployeeLeave::find($request->leave_app_id[$key]);

                $hrm_employee_id   =    $query_data->hrm_employee_id;
                $hrm_month_id    = date('m', strtotime($query_data->date_from));
                $year_id         = date('Y', strtotime($query_data->date_from));


                $pay_register = DB::table('pay_register as a')
                            ->select('a.*','b.hrm_employee_id')
                            ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                            ->where('b.hrm_employee_id',$hrm_employee_id)
                            ->where('a.hrm_month_id',$hrm_month_id)
                            ->where('a.year_id',$year_id)
                            ->whereIn('a.salary_genarate_type', [1, 2])
                            ->first();

                if (!empty($pay_register)){
                    session()->flash('alert-danger', 'This months salary has already been processed. Leave Approved is not allowed. !!');
                    return Redirect()->back();
                }


                $fdate = $query_data->date_from;
                $tdate = $query_data->date_to;
                $datetime1 = new DateTime($fdate);
                $datetime2 = new DateTime($tdate);
                $interval = $datetime1->diff($datetime2);
                $days_total = $interval->format('%a');
                $duration = $days_total + 1;

                $insert                                 = new HrmLeaveLedger;
                $insert->hrm_leave_approve_id           = $leave_approve_id;
                $insert->used_leave                     = $query_data ->duration;
                $insert->hrm_employee_leave_type_id     = $query_data ->hrm_employee_leave_type_id;
                $insert->hrm_employee_id                = $query_data ->hrm_employee_id;
                $insert->hrm_leave_years_id             = $query_data ->hrm_leave_years_id;
                $insert->status                         = 2;
                $insert->users_id                       = Auth::user()->id;
                $insert->save();


                $this->recordActivity(
                     1,
                     'Multiple Leave Approved',
                     null,
                     $insert->id,
                     'hrm_leave_ledger'
                );


                //-------Data Process
                // $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $query_data->hrm_employee_id)->where('employee_activity','=',1)->first();

                // for ($x = 0; $x < $duration; $x++) {
                //     $processDate = date('Y-m-d', strtotime($query_data->date_from. ' + '.$x.'days'));

                //     if(date('Y-m-d') < $processDate) {
                //         break;
                //     }

                //     (new AttendanceDataProcessController)
                //         ->attandanceProcess(
                //             $location->hrm_location_id,
                //             $processDate,
                //             $query_data->hrm_employee_id
                //         );
                // }
            } else{
            // dd($update_leave_approve->getChanges());

                // dd("Working");
                $this->recordActivity(
                     1,
                     'Rejected Multiple Leave',
                     $update_leave_approve->getChanges(),
                     $leave_approve_id,
                     'hrm_leave_approve'
                );
            }



        }

        return back()->with('alert-success', 'data has been stored successfully!');
    }

    public function leaveapprovedlist(Request $request)
    {
        $condition = '';

        // dd($request->all());

        if($request->location==null){
            $location = 0;
        }else{
            $condition = ' and d.hrm_location_id = '.$request->location;
        }

        if($request->employeeid==null){
            $employeeid = 0;
        } else {
            $condition .= ' and d.hrm_employee_id ='.$request->employeeid;
        }

        $date_from  = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to  = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $condition = $condition." and a.date_to between '$date_from'  and '$date_to'";

        $user_id = Auth::user()->id;

        $employee = DB::select("SELECT
                b.id,
                a.id as leave_app_id,
                a.applied,
                concat(b.employee_name,' | ',d.employee_code) as employee_name,
                a.duration,
                c.leave_type,
                (CASE
                WHEN a.duration=1 THEN '.50'
                WHEN a.duration=3 THEN '.25'
                ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                END)  as days,
                a.comment,
                CONCAT(a.date_from,'  to  ',a.date_to) AS date_range,
                b.Images,
                'Approved' as status,
                g.name as apply_to
            FROM
                hrm_leave_application a
                    JOIN
                hrm_employee b ON b.id = a.hrm_employee_id AND b.active_status = 1
                    JOIN
                hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                    JOIN
                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id AND d.employee_activity = 1
                    $condition
                    JOIN
                user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                    JOIN
                hrm_leave_approve f ON f.hrm_leave_application_id = a.id AND f.action_type = 1 AND f.forward = 2
                    JOIN
                users g ON f.users_id = g.id
        ");

        return json_encode(array('data' => $employee));
    }

    public function leaverejected_list()
    {
        $user_id = Auth::user()->id;
        $condition = '';
        $date_from  = date('Y-m-d', strtotime(str_replace('/', '-', request('date_from'))));
        $date_to  = date('Y-m-d', strtotime(str_replace('/', '-', request('date_to'))));
        $condition = $condition." and a.date_to between '$date_from'  and '$date_to'";

        $employee = DB::select("SELECT
                b.id,
                a.id as leave_app_id,
                concat(b.employee_name,' | ',d.employee_code) as employee_name,
                c.leave_type,
                (CASE
                WHEN a.duration=1 THEN '.50'
                WHEN a.duration=3 THEN '.25'
                ELSE (DATEDIFF(a.date_to,a.date_from)+1)
                END)  as days,
                a.comment,
                a.date_from,
                a.date_to,
                b.Images,
                'Reject' as status,
                f.id as hrm_leave_approve_id,
                g.name as apply_to

            FROM
                hrm_leave_application a
                    JOIN
                hrm_employee b on b.id = a.hrm_employee_id and b.active_status = 1
                    JOIN
                hrm_employee_leave_type c On a.hrm_employee_leave_type_id = c.id
                    JOIN
                hrm_employee_job_info d On a.hrm_employee_id = d.hrm_employee_id and d.employee_activity = 1
                    $condition
                    JOIN
                user_location e ON d.hrm_location_id = e.hrm_location_id AND e.users_id = $user_id
                    JOIN
                hrm_leave_approve f ON f.hrm_leave_application_id = a.id AND f.action_type = 2 AND f.forward = 2
                    JOIN
                users g ON f.users_id = g.id

        ");

        return json_encode(array('data' => $employee));
    }


    public function store(Request $request)
    {
        // dd($request->all());
         $validator = Validator::make($request->all(), [
            'employee_name'              => 'required',
            'employee_leave_type'        => 'required',
            'duration'                   => 'required',
            'payment_mode'               => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeeleave/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $applied            = date('Y-m-d', strtotime(str_replace('/', '-', $request->applied)));
        $date_from          = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to            = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        $hrm_leave_years_id = DB::SELECT("SELECT id FROM hrm_leave_years WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'  ");

        if(empty($hrm_leave_years_id[0]->id)){
            $request->session()->flash('alert-danger', 'Leave Applied date cross the Leave Year !');
            return redirect('leaveyear');
        }


        $employeejobinfo = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_name)
                        ->where('employee_activity', 1)
                        ->first();

        // dd($employeejobinfo->overtime_status);

        if($employeejobinfo->overtime_status==1){
            $find = DB::SELECT("SELECT * FROM hrm_employee_leave_type WHERE id=$request->employee_leave_type AND leave_status=2 ");
            if(!empty( $find )){
                // dd("NOT sucess");
                $reservehal = DB::SELECT("SELECT count(id) as id FROM hrm_holiday_against_leave WHERE hrm_employee_id=$request->employee_name AND valid=1");
                $expensehal = DB::SELECT("SELECT count(b.id) as id
                                                FROM hrm_leave_application a
                                                JOIN hrm_employee b on b.id=a.hrm_employee_id and b.active_status=1 AND b.id=$request->employee_name
                                                JOIN hrm_employee_leave_type c On a.hrm_employee_leave_type_id=c.id and c.leave_status=2
                                                JOIN hrm_employee_job_info d On a.hrm_employee_id=d.hrm_employee_id and d.employee_activity=1
                                                JOIN hrm_leave_approve f ON f.hrm_leave_application_id=a.id AND f.action_type=1 AND f.forward=2");

                if (($reservehal[0]->id) > ($expensehal[0]->id)) {
                    // dd("YES");
                } else {
                    $request->session()->flash('alert-danger', 'Sorry you have no reserve holiday against leave!');
                    return redirect('employeeleave');
                }
            }
        }

        // $leave_check = DB::SELECT("SELECT id FROM hrm_leave_application WHERE '$date_from'  between date_from AND  date_to  AND valid=1 AND hrm_employee_id=$request->employee_name");
        $leave_check = DB::select("SELECT a.id
                            FROM hrm_leave_application a
                            JOIN hrm_leave_approve b
                                ON b.hrm_leave_application_id = a.id
                            WHERE '$date_from' BETWEEN a.date_from AND a.date_to
                            AND a.valid = 1
                            AND a.hrm_employee_id = $request->employee_name
                            AND ( b.action_type IS NULL OR b.action_type = 1 )
                        ");

        // dd($leave_check);
        if(!empty($leave_check)){
             $request->session()->flash('alert-danger', 'Sorry you have already entry this leave! Please check');
             return redirect('employeeleave');
        }

            $hrm_employee_id   =    $request->employee_name;
            $hrm_month_id    = date('m', strtotime($date_from));
            $year_id         = date('Y', strtotime($date_from));


            $pay_register = DB::table('pay_register as a')
                        ->select('a.*','b.hrm_employee_id')
                        ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                        ->where('b.hrm_employee_id',$hrm_employee_id)
                        ->where('a.hrm_month_id',$hrm_month_id)
                        ->where('a.year_id',$year_id)
                        ->whereIn('a.salary_genarate_type', [1, 2])
                        ->first();

            if (!empty($pay_register)){
                session()->flash('alert-danger', 'This months salary has already been processed. Leave Entry is not allowed. !!');
                return Redirect()->back();
            }


        $insert     = new HrmEmployeeLeave;
        $insert->applied                      = $applied;
        $insert->hrm_employee_id              = $request->employee_name;
        $insert->hrm_employee_leave_type_id   = $request->employee_leave_type;
        $insert->date_from                    = $date_from;
        $insert->date_to                      = $date_to;
        $insert->comment                      = $request->comment;
        $insert->duration                     = $request->duration;
        $insert->payment_mode                 = $request->payment_mode;
        $insert->valid                        = 1;
        $insert->hrm_leave_years_id           = $hrm_leave_years_id[0]->id;
        $insert->users_id                     = Auth::user()->id;
        $insert->created_at                   = now();
        $insert->save();

        $this->recordActivity(
             1,
             'Created Leave Application',
             $insert,
             $insert->id,
             'hrm_leave_application'
        );


        $approve                           = new HrmLeaveApprove;
        $approve->hrm_leave_application_id = $insert->id;
        $approve->users_id                 = $request->applytousername;
        $approve->save();

        // dd('ok');
        $request->session()->flash('alert-success', 'data has been successfully added!');
        return redirect('employeeleave');
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


    public function leaverejected_reactive(Request $request,$id)
    {
        $cancel = HrmLeaveApprove::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

       DB::UPDATE("UPDATE hrm_leave_approve SET action_type=null,forward=null WHERE id=$id");

       $this->recordActivity(
             1,
             'ReActive Leave Application',
             null,
             $id,
             'hrm_leave_approve'
        );

        $request->session()->flash('alert-success', 'successfully reactive !');
        return redirect('leaverejectedlist');

    }






    public function cancel(Request $request,$id){

        $cancel = HrmEmployeeLeave::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid leave application !!');
            return Redirect()->back();
        }

        DB::table('hrm_leave_approve')->where('hrm_leave_application_id', '=', $id)->delete();
        DB::table('hrm_leave_application')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Employee Leave Application',
             null,
             $id,
             'hrm_leave_application'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return redirect('employeeleave');

    }

    public function approve_delete($id){

        $query_data = HrmEmployeeLeave::find($id);


        if (empty($query_data)){
            session()->flash('alert-danger', 'Invalid leave application !!');
            return Redirect()->back();
        }

        $hrm_employee_id   =    $query_data->hrm_employee_id;
        $hrm_month_id    = date('m', strtotime($query_data->date_from));
        $year_id         = date('Y', strtotime($query_data->date_from));


        $pay_register = DB::table('pay_register as a')
                    ->select('a.*','b.hrm_employee_id')
                    ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                    ->where('b.hrm_employee_id',$hrm_employee_id)
                    ->where('a.hrm_month_id',$hrm_month_id)
                    ->where('a.year_id',$year_id)
                    ->whereIn('a.salary_genarate_type', [1, 2])
                    ->first();

        if (!empty($pay_register)){
            session()->flash('alert-danger', 'This months salary has already been processed. Leave Entry delete is not allowed. !!');
            return Redirect()->back();
        }


        $fdate = $query_data->date_from;
        $tdate = $query_data->date_to;
        $datetime1 = new DateTime($fdate);
        $datetime2 = new DateTime($tdate);
        $interval = $datetime1->diff($datetime2);
        $days_total = $interval->format('%a');
        $duration = $days_total+1;

        // $find_approve_id = HrmLeaveApprove::where('hrm_leave_application_id',$id)->first();
        $approveIds = HrmLeaveApprove::where('hrm_leave_application_id', $id)->pluck('id');

        DB::table('hrm_leave_ledger')->whereIn('hrm_leave_approve_id',  $approveIds)->delete();
        DB::table('hrm_leave_approve')->whereIn('id', $approveIds)->delete();
        DB::table('hrm_leave_application')->where('id', '=', $id)->delete();

        //-------Data Process

        $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $query_data->hrm_employee_id)->where('employee_activity','=',1)->first();

        for ($x = 0; $x < $duration; $x++) {
            $processDate = date('Y-m-d', strtotime($query_data->date_from. ' + '.$x.'days'));
            if(date('Y-m-d')< $processDate){
                break;
            }
            $attendance = new AttendanceDataProcessController();
            $attendance->attandanceProcess($location->hrm_location_id,$processDate,$query_data->hrm_employee_id);
        }

        $this->recordActivity(
             1,
             'Deleted Leave Approved',
             null,
             $id,
             'hrm_leave_approve'
        );


        session()->flash('alert-success', 'successfully deleted !');
        return redirect('leaveapprovedlist');
    }
}
