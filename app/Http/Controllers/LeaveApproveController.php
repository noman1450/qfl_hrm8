<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Jaspersoft\Client\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Models\HrmLeaveLedger;
use App\Models\HrmLeaveApprove;
use App\Models\HrmEmployeeLeave;
use App\Models\HrmEmployeeJobInfo;
use App\Http\Controllers\AttendanceDataProcessController;

class LeaveApproveController extends Controller
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
        // dd(request()->all());
        $hrm_location = DB::table('hrm_location')->where('id',request()->location_id)->first();
        return view('leave_approve.leave_approve_waiting_list',compact('hrm_location'));
    }

    public function leaveapprove_waitinglist(Request $request)
    {

        // dd($request->all());
        $searchFilter = '';

        if($request->location){
            $searchFilter .= ' AND d.hrm_location_id = '.$request->location;
        }
        // if($request->hrm_location_id){
        //     $searchFilter .= ' And d.hrm_location_id = '.$request->hrm_location_id;
        // }

        if($request->employee_id){
            $searchFilter .= ' And a.id = '.$request->employee_id;
        }

        if($request->employee_leave_type_id){
            $searchFilter .= ' And b.hrm_employee_leave_type_id = '.$request->employee_leave_type_id;
        }

        if($request->payment_mode){
            $searchFilter .= ' And b.payment_mode = '.$request->payment_mode;
        }

        $user_id = auth()->id();

        $forwardTo = " AND g.users_id = $user_id";

        if (auth()->user()->user_type == 2) {
            $forwardTo = "";
        }
        // dd($user_id);


        $employee = DB::select("SELECT
                                a.id,
                                b.id as hrm_leave_application_id,
                                g.id as hrm_leave_approve_id,
                                concat(a.employee_name,' | ',ifnull(d.employee_code,''),' | ',h.designation_name,' | ',e.depertment_name ) as employee_name,
                                e.depertment_name,
                                c.leave_type,
                                date_format(b.date_from, '%d-%b-%Y') date_from,
                                date_format(b.date_to, '%d-%b-%Y') date_to,
                                date_format(k.joining_date, '%d-%b-%Y') joining_date,
                                ifnull(date_format(k.confirmation_date, '%d-%b-%Y'),'Provision')  confirmation_date,
                                (CASE
                                    WHEN b.payment_mode = 1 THEN 'With Pay'
                                    WHEN b.payment_mode = 2 THEN 'Without Pay'
                                    ELSE ''
                                END) as payment_mode,
                                (CASE
                                WHEN b.duration=1 THEN '.50'
                                WHEN b.duration=3 THEN '.25'
                                ELSE (DATEDIFF(b.date_to,b.date_from)+1)
                                END)  as days,
                                b.comment,
                                h.designation_name,
                                i.category_name,
                                a.Images,
                                j.name as apply_to,
                                dd.location_name
                            FROM
                                hrm_employee a
                                JOIN hrm_leave_application b on a.id=b.hrm_employee_id
                                JOIN hrm_employee_leave_type c On b.hrm_employee_leave_type_id=c.id  And a.active_status=1
                                JOIN hrm_employee_job_info d On a.id=d.hrm_employee_id AND d.employee_activity=1
                                $searchFilter
                                JOIN hrm_location dd ON d.hrm_location_id = dd.id
                                JOIN hrm_depertment e On e.id=d.hrm_depertment_id
                                JOIN user_location f ON d.hrm_location_id = f.hrm_location_id AND f.users_id = $user_id
                                JOIN hrm_leave_approve g On b.id=g.hrm_leave_application_id
                                JOIN hrm_designation h ON d.hrm_designation_id = h.id
                                JOIN hrm_category i ON d.hrm_category_id = i.id
                                JOIN users j ON g.users_id = j.id
                                JOIN hrm_employee_joining k ON a.id = k.hrm_employee_id
                                WHERE a.active_status=1

                                 AND g.id in
                                (SELECT id  FROM hrm_leave_approve WHERE action_type is null and forward is null
                                 $forwardTo
                                  )
                                GROUP By b.id");

        // dd($employee);
        return json_encode(array('data' => $employee));

    }

    public function store(Request $request)
    {
        // dd($request->all());
        $comment = $request->comment;
        if (empty($comment)){
            $comment = 'N/A';
        } else {
            $comment = $request->comment;
        }

        $forwardStatus=$request->forward;

        try {
            DB::beginTransaction();

            if ($forwardStatus==1) {
                $insert     = new HrmLeaveApprove;
                $insert->hrm_leave_application_id = $request->hrm_leave_application_id;
                $insert->users_id                 = Auth::user()->id;
                $insert->save();

                DB::update("UPDATE hrm_leave_approve SET action_type = $request->action,comment='$comment', forward  = $request->forward WHERE id = $request->hrm_leave_approve_id ");


            }else{

                DB::update("UPDATE hrm_leave_approve SET action_type = $request->action,forward  = $request->forward,comment='$comment' WHERE id = $request->hrm_leave_approve_id ");

                if ($request->action == 1) {// Approve
                    $query_data = HrmEmployeeLeave::find($request->hrm_leave_application_id);
                    // dd($query_data);
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
                    // dd($pay_register);
                    if (isset($pay_register)){
                        DB::rollBack();
                        session()->flash('alert-danger', 'This months salary has already been processed. Leave Approved is not allowed. !!');
                        return Redirect()->back();
                    }

                    $fdate = $query_data->date_from;
                    $tdate = $query_data->date_to;
                    $datetime1 = new DateTime($fdate);
                    $datetime2 = new DateTime($tdate);
                    $interval = $datetime1->diff($datetime2);
                    $days_total = $interval->format('%a');
                    $duration = $days_total+1;

                    // dd($duration);

                    $insert                                 = new HrmLeaveLedger;
                    $insert->hrm_leave_approve_id           = $request ->hrm_leave_approve_id;
                    $insert->used_leave                     = $request ->days;
                    $insert->hrm_employee_leave_type_id     = $query_data ->hrm_employee_leave_type_id;
                    $insert->hrm_employee_id                = $query_data ->hrm_employee_id;
                    $insert->hrm_leave_years_id             = $query_data ->hrm_leave_years_id;
                    $insert->status                         = 2;
                    $insert->users_id                       = Auth::user()->id;
                    $insert->save();

                    $this->recordActivity(
                         1,
                         'Accepted Leave application From Pending List',
                         null,
                         $insert->id,
                         'hrm_leave_approve'
                    );
                }else{

                    /*$this->recordActivity(
                         1,
                         'Rejected Leave application From Pending List',
                         null,
                         $insert->id,
                         'hrm_leave_approve'
                    );*/
                }
            }

            DB::commit();

            //-------Data Process (runs AFTER the outer transaction is committed,
            // because attandanceProcess manages its own nested transactions and
            // calling it inside this transaction raises "no active transaction")
            if ($request->action == 1 && $forwardStatus == 0) {
                $location = HrmEmployeeJobInfo::where('hrm_employee_id', $query_data->hrm_employee_id)->where('employee_activity', '=', 1)->first();

                try {
                    for ($x = 0; $x < $duration; $x++) {
                        $processDate = date('Y-m-d', strtotime($query_data->date_from . ' + ' . $x . 'days'));

                        if (date('Y-m-d') < $processDate) {
                            break;
                        }

                        $attendance = new AttendanceDataProcessController();
                        $attendance->attandanceProcess($location->hrm_location_id, $processDate, $query_data->hrm_employee_id);
                    }
                } catch (\Exception $e) {
                    // attandanceProcess already swallows its own errors; this is a defensive
                    // guard only. Approval has already been committed, so we just notify.
                    session()->flash('alert-danger', 'Leave approved, but attendance processing had an issue: ' . $e->getMessage());
                }
            }

            
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('alert-danger', 'Something went wrong: ' . $e->getMessage());
            return Redirect::back();
        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('leaveapprove');
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

    public function print_leave_form(Request $request)
    {
       // dd( $request->id);
        $parameter = $request->id;
        // dd($parameter);
        $jasper_server = new Client(
            config('configaration.jasperjasper_url'),
            config('configaration.jasper_user'),
            config('configaration.jasper_password')
        );


        $controls = array(
            'company_name'          => config('configaration.company_name'),
            'address'               => config('configaration.company_address'),
            'title'                 => "APPLICATION FOR LEAVE",
            'id'                    => $parameter,

        );


        $report_path = config('configaration.report_path').'hrm_leave_application_form';
        $exporttype  = "pdf";
        $report      = $jasper_server->reportService()->runReport($report_path, $exporttype,null,null,$controls);
        // dd("NOMAN");


        // dd($report_path);
        if (strlen($report) > 940){
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($report));
            header('Content-Type: application/'.$exporttype);
            echo $report;
            echo $report;
            echo $report;
            echo $report;
            echo "data:application/pdf;base64, " . $report;
        }else{
            // Session::flash('flash_message', 'No More Data For View');
            dd("No data found");
        }
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
}
