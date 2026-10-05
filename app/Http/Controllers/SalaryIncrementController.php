<?php

namespace App\Http\Controllers;

use App\Exports\IncrementPromotionReportExport;
use Crypt;
use Session;
use App\User;
use DateTime;
use Datatables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeShift;
use App\Models\HrmLastPromotion;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmSalaryIncrement;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;

use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Models\HrmSalaryIncrementDetails;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SalaryIncrementController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
    }



    public function index()

    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('salary_increment.salary_increment_list')
            ->with('default_user_location',  $default_user_location);
    }

    public function salaryincrementpreviousdata()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('salary_increment.past_salary_increment_list')
            ->with('default_user_location',  $default_user_location);
    }





    public function salary_increment_listdata(Request $request)
    {




        $condition    = "a.active_status=1";

        if ($request->location != 0) {
            $condition  = " AND b.hrm_location_id = " . $request->location;
        }


        if ($request->department_id != 0) {
            $condition  =  $condition . " AND b.hrm_depertment_id = " . $request->department_id;
        }

        if ($request->designation_id != 0) {
            $condition  =  $condition . " AND b.hrm_designation_id = " . $request->designation_id;
        }


        if ($request->employee_id != 0) {
            $condition  =  $condition . " AND a.id = " . $request->employee_id;
        }


        if ($request->category_id != 0) {
            $condition  =  $condition . " AND b.hrm_category_id = " . $request->category_id;
        }


        if ($request->section_id != 0) {
            $condition  =  $condition . " AND b.hrm_section_id = " . $request->section_id;
        }

        if (isset($request->salary_amount_search)) {
            $condition  =  $condition . " AND b.basic_salary " . $request->search_type . $request->salary_amount_search;
        }


        // dd($condition);

        $amount      = $request->amount;
        $amount_type = $request->amount_type;

        if ($amount_type == 1) {
            $type = '%';
        } else {
            $type = 'Tk.';
        }

        $data   = DB::select("SELECT aa.id,
                                    aa.employee_name,
                                    aa.depertment_name,
                                    aa.designation_name,
                                    aa.hrm_designation_id,
                                    aa.location_name,
                                    aa.salary_amount,
                                    aa.joining_date,
                                    aa.amount,
                                    IFNULL(aa.last_increment_note, '-') AS last_increment_note,
                                    aa.grade_name,
                                    aa.hrm_salary_grade_id,
                                    if(right(aa.increment_amount,2)<50 , aa.increment_amount-right(aa.increment_amount,2) , aa.increment_amount + (100- right(aa.increment_amount,2)) ) as increment_amount
                                FROM (
                                    SELECT
                                        b.id,
                                        CONCAT_WS(' | ', a.employee_name, b.employee_code, es.employment_status) AS employee_name,
                                        c.depertment_name,
                                        d.designation_name,
                                        d.id as hrm_designation_id,
                                        d.priority,
                                        e.location_name,
                                        g.salary_amount,
                                        hej.joining_date,
                                        i.grade_name,
                                        i.id as hrm_salary_grade_id,
                                        ('$amount $type') as amount,
                                        If('$amount_type' = 1,

                                            round(g.salary_amount+(g.salary_amount*$amount/100)),
                                            round(g.salary_amount+$amount)

                                        ) as increment_amount,

                                      (
                                        SELECT
                                            CONCAT(
                                               ' Date ', IFNULL(DATE_FORMAT(si.effective_date, '%d-%m-%Y'), ''),
                                                ' | Amount: ',
                                                IFNULL((sidt.new_salary_amount - sidt.salary_amount), 0)
                                            )
                                        FROM hrm_salary_increment si
                                        JOIN hrm_salary_increment_details sidt
                                            ON si.id = sidt.hrm_salary_increment_id
                                        JOIN hrm_employee_job_info ji
                                            ON sidt.hrm_employee_job_info_id = ji.id
                                        WHERE ji.hrm_employee_id = a.id
                                          AND si.status = 2
                                          AND si.effective_date < CURDATE()
                                        ORDER BY si.effective_date DESC
                                        LIMIT 1
                                    ) AS last_increment_note

                                    FROM
                                        hrm_employee a
                                            JOIN
                                        hrm_employee_job_info b ON b.hrm_employee_id = a.id
                                            AND b.employee_activity = 1
                                            JOIN
                                        hrm_employment_status es ON b.hrm_employment_status_id = es.id
                                            JOIN
                                        hrm_employee_joining hej ON b.hrm_employee_id = hej.hrm_employee_id
                                            JOIN
                                        hrm_depertment c ON b.hrm_depertment_id = c.id
                                            JOIN
                                        hrm_designation d ON b.hrm_designation_id = d.id
                                            JOIN
                                        hrm_location e ON b.hrm_location_id = e.id
                                            JOIN
                                        hrm_employee_card_code f ON f.hrm_employee_job_info_id = b.id
                                            JOIN
                                        hrm_employee_salary g ON g.hrm_employee_job_info_id = b.id
                                            JOIN
                                        hrm_salary_grade_master h ON h.id = g.hrm_salary_grade_master_id
                                            JOIN
                                        hrm_salary_grade i ON i.id = h.hrm_salary_grade_id
                                            $condition

                                ) aa
                                ORDER BY aa.priority ASC
                            ");

        return json_encode(array('data' => $data));
    }


    public function role_salaryincrement_listdata(Request $request)
    {

        $condition    = '';

        if ($request->location != 0) {
            $condition  = " AND d.hrm_location_id = " . $request->location;
        }



        $data   = DB::select("SELECT
                                    a.id,
                                    a.note,
                                    CONCAT(b.month_name,
                                            ' - ',
                                            YEAR(a.effective_date)) AS effective_month,
                                    h.location_name,
                                    If(a.status = 1,'Processing','Completed') as status,
                                    If(a.apply_for = 1,'Increment','Promotion') as apply_for,
                                    a.hrm_location_id,
                                    hej.joining_date,
                                    GROUP_CONCAT(CONCAT(e.employee_name, ' || ', d.employee_code)
                                        SEPARATOR '<br>') AS employee_name,
                                    GROUP_CONCAT(CONCAT(f.alis)
                                        SEPARATOR '<br>') AS designation_name,
                                    GROUP_CONCAT(CONCAT(g.depertment_name)
                                        SEPARATOR '<br>') AS depertment_name,
                                    GROUP_CONCAT(CONCAT(c.salary_amount)
                                        SEPARATOR '<br>') AS salary_amount,
                                    GROUP_CONCAT(CONCAT(c.increase_amount)
                                        SEPARATOR '<br>') AS increase_amount,
                                    GROUP_CONCAT(CONCAT(c.new_salary_amount)
                                        SEPARATOR '<br>') AS new_salary_amount

                                FROM
                                    hrm_salary_increment a
                                        JOIN
                                    hrm_month b ON MONTH(a.effective_date) = b.id AND a.status= $request->status
                                        JOIN
                                    hrm_salary_increment_details c ON a.id = c.hrm_salary_increment_id
                                        JOIN
                                    hrm_employee_job_info d ON c.hrm_employee_job_info_id = d.id
                                       $condition
                                        JOIN
                                    hrm_employee e ON d.hrm_employee_id = e.id
                                        JOIN
                                    hrm_employee_joining hej ON e.id = hej.hrm_employee_id
                                        JOIN
                                    hrm_designation f ON c.hrm_designation_id = f.id
                                        JOIN
                                    hrm_depertment g ON d.hrm_depertment_id = g.id
                                        JOIN
                                    hrm_location h ON a.hrm_location_id = h.id
                                GROUP BY a.id , b.month_name ,a.effective_date,h.location_name,a.note,a.status,a.apply_for");


        return json_encode(array('data' => $data));
    }




    public function past_salaryincrement_listdata(Request $request)
    {



        // $condition    = '';

        // if ($request->location != 0){
        //   $condition  = " AND d.hrm_location_id = ".$request->location;
        // }

        $parameter = '';

        $date_from = date('Y-m-d', strtotime( $request->date_from));
        $date_to = date('Y-m-d', strtotime( $request->date_to));

        if (isset($request->location)) {
            $parameter  = $parameter . " AND d.hrm_location_id = " . $request->location;
        }

        if (isset($request->date_from)) {
            $parameter  =  $parameter . " AND a.effective_date BETWEEN '$date_from' AND '$date_to' ";
        }

        if (isset($request->status)) {

            $parameter  =  $parameter . " AND a.apply_for = '{$request->status}'  ";
        }



        $data   = DB::select("SELECT
                                    a.id,
                                    a.note,
                                    CONCAT(b.month_name,
                                            ' - ',
                                            YEAR(a.effective_date)) AS effective_month,
                                    h.location_name,
                                    If(a.status = 1,'Processing','Completed') as status,
                                    CONCAT_WS(' | ', e.employee_name, d.employee_code, es.employment_status) AS employee_name,
                                    d.employee_code,
                                    e.id as employee_id,
                                    e.Images,
                                    f.alis AS designation_name,
                                    hej.joining_date,
                                    (SELECT  designation_name
                                   from hrm_designation  where c.hrm_designation_id = id limit 1) as new_designation_name,
                                    g.depertment_name AS depertment_name,
                                    c.salary_amount AS salary_amount,
                                    c.new_salary_amount - c.salary_amount AS increase_amount,
                                    c.new_salary_amount AS new_salary_amount,
                                    IF(a.apply_for = 1, 'Increment', 'Promotion') AS apply_for,
                                    i.category_name,
                                    c.notes,
                                    c.id as hrm_salary_increment_details_id

                                FROM
                                    hrm_salary_increment a
                                        JOIN
                                    hrm_month b ON MONTH(a.effective_date) = b.id AND a.status= 2
                                        JOIN
                                    hrm_salary_increment_details c ON a.id = c.hrm_salary_increment_id
                                        JOIN
                                    hrm_employee_job_info d ON c.hrm_employee_job_info_id = d.id
                                       $parameter
                                        JOIN
                                    hrm_employee e ON d.hrm_employee_id = e.id
                                        JOIN
                                    hrm_employee_joining hej ON e.id = hej.hrm_employee_id
                                        JOIN
                                    hrm_employment_status es ON d.hrm_employment_status_id = es.id
                                        JOIN
                                    hrm_designation f ON d.hrm_designation_id = f.id
                                        JOIN
                                    hrm_depertment g ON d.hrm_depertment_id = g.id
                                        JOIN
                                    hrm_location h ON a.hrm_location_id = h.id
                                        join
                                    hrm_category i ON d.hrm_category_id = i.id
                                    $parameter
                                    Order by f.priority asc
                                ");

        return json_encode(array('data' => $data));
    }


    public function create()
    {
        $user_id = Auth::user()->id;
        $user_location = DB::select("SELECT a.id,a.location_name,b.default_location FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

        return view('salary_increment.create_salary_increment')
            ->with('user_location',  $user_location);
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

        if ($request->id == null) {
            $request->session()->flash('alert-danger', 'Please Select Employee and Resubmit!');
            return Redirect::to('salaryincrement');
        }

        $validator = Validator::make($request->all(), [
            'date_from'      => 'required',
            'note'           => 'required',
            'amount'         => 'required',
            'amount_type'    => 'required',
        ]);


        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $user_id = Auth::user()->id;


        DB::beginTransaction();
        try {

            $insert     = new HrmSalaryIncrement;
            $insert->effective_date     = $date_from;
            $insert->note               = $request->note;
            $insert->users_id           = $user_id;
            $insert->hrm_location_id    = $request->location;
            $insert->status             = 1;
            $insert->apply_for          = $request->apply_for;
            $insert->save();

            $count_row  = count($request->id);
            $skip_employees = [];
            foreach($request->id as $jobid){


                $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id = $jobid AND end_date is null");
                // dd($check_data);
                if (!empty($check_data)) {

                    if ($date_from < $check_data[0]->start_date) {
                        $skip_employees[] = DB::table('hrm_employee_job_info')
                            ->join('hrm_employee', 'hrm_employee_job_info.hrm_employee_id', '=', 'hrm_employee.id')
                            ->where('hrm_employee_job_info.id', $jobid)
                            ->value(DB::raw("CONCAT(hrm_employee.employee_name, ' (', hrm_employee_job_info.employee_code, ')')"));
                    } else {


                        $check_two = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $jobid)) AND start_date='$date_from' ");

                        // dd($check_two);
                        if (empty($check_two)) {


                            $hrm_salary_grade_id = null;

                            if (config('module_config.payroll_module') == 1) {
                                $salary_grade_master  = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->hrm_salary_grade_id[$jobid])->first();

                                if (!empty($salary_grade_master)) {
                                    $hrm_salary_grade_id = $salary_grade_master->hrm_salary_grade_id;
                                }
                            }


                            $insert_details     = new HrmSalaryIncrementDetails;
                            $insert_details->hrm_salary_increment_id     = $insert->id;
                            $insert_details->hrm_employee_job_info_id    = $jobid;
                            $insert_details->salary_amount               = $request->salaryamount[$jobid];
                            $insert_details->increase_amount             = $request->increment[$jobid];
                            $insert_details->amount_type                 = $request->amount_type;
                            $insert_details->new_salary_amount           = $request->new_salary_amount[$jobid];
                            $insert_details->hrm_designation_id          = $request->hrm_designation_id[$jobid];
                            $insert_details->hrm_salary_grade_id          = $request->hrm_salary_grade_id[$jobid];
                            $insert_details->notes                        = $request->last_increment_note[$jobid];


                            $insert_details->save();
                        } else {
                            $skip_employees[] = DB::table('hrm_employee_job_info')
                                ->join('hrm_employee', 'hrm_employee_job_info.hrm_employee_id', '=', 'hrm_employee.id')
                                ->where('hrm_employee_job_info.id', $jobid)
                                ->value(DB::raw("CONCAT(hrm_employee.employee_name, ' (', hrm_employee_job_info.employee_code, ')')"));
                        }
                    }
                } else {
                        $skip_employees[] = DB::table('hrm_employee_job_info')
                                ->join('hrm_employee', 'hrm_employee_job_info.hrm_employee_id', '=', 'hrm_employee.id')
                                ->where('hrm_employee_job_info.id', $jobid)
                                ->value(DB::raw("CONCAT(hrm_employee.employee_name, ' (', hrm_employee_job_info.employee_code, ')')"));
                }
            }

            DB::commit();

            $this->recordActivity(
                1,
                'Created Salary Increment',
                null,
                $insert->id,
                'hrm_salary_increment'
            );
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }
        $message = 'Successfully inserted!';
        $message .= '<ul class="mb-0 mt-2">';

        if($skip_employees){

            foreach ($skip_employees as $employee) {
                $message .= '<li>' . e($employee) . '</li>';
            }
            $message .= ' However, the following employee(s) already have a salary increment for this date: ';
            $message .= '</ul>';

        }




        $request->session()->flash('alert-success', $message);

        return Redirect::to('salaryincrement');
    }



    public function salaryincrementupdate(Request $request)
    {

        if ($request->id == null) {

            $request->session()->flash('alert-danger', 'Please Select Employee and Resubmit!');
            return Redirect::to('salaryincrement');
        }

        $validator = Validator::make($request->all(), [
            'date_from'         => 'required',
            'date_to'           => 'required',
            'purpose'           => 'required',
            'salary_head'       => 'required',
        ]);


        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $user_id = Auth::user()->id;


        DB::beginTransaction();
        try {
            DB::table('hrm_salary_extra_feature_details')->where('hrm_salary_extra_feature_id', '=', $request->id)->delete();
            DB::table('hrm_salary_extra_feature')->where('id', '=', $request->id)->delete();

            $insert     = new HrmSalaryIncrement;
            $insert->month_from         = $date_from;
            $insert->month_to           = $date_to;
            $insert->purpose            = $request->purpose;
            $insert->hrm_salary_head_id = $request->salary_head;
            $insert->users_id           = $user_id;
            $insert->hrm_location_id    = $request->location;
            $insert->apply_for          = $request->apply_for;
            $insert->save();


            $count_row  = count($request->hrm_employee_job_info_id);
            for ($r = 0; $r < $count_row; $r++) {


                $insert_details     = new HrmSalaryIncrementDetails;
                $insert_details->hrm_salary_extra_feature_id = $insert->id;
                $insert_details->hrm_employee_job_info_id    = $request->hrm_employee_job_info_id[$r];
                // $insert_details->amount                      = $request->amount[$request->id[$r]];
                $insert_details->amount                      = $request->amount[$r];
                $insert_details->save();

                $this->recordActivity(
                    1,
                    'Updated Salary Increment Details',
                    $insert_details->getChanges(),
                    $insert_details->id,
                    'hrm_salary_increment_details'
                );
            }

            DB::commit();

            $this->recordActivity(
                1,
                'Updated Salary Increment',
                $insert->getChanges(),
                $insert->id,
                'hrm_salary_increment'
            );
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }

        $request->session()->flash('alert-success', 'Successfully Updated!');
        return Redirect::to('salaryincrement');
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Master info + total employee count
        $increment = DB::select("SELECT
                    a.id,
                    a.note,
                    a.effective_date,
                    a.apply_for,
                    a.status,
                    e.location_name,
                    COUNT(c.id) AS total_employee
                FROM hrm_salary_increment a
                JOIN hrm_location e ON a.hrm_location_id = e.id
                LEFT JOIN hrm_salary_increment_details c ON c.hrm_salary_increment_id = a.id
                WHERE a.id = ?
                GROUP BY a.id", [$id]);

        // Row-wise employee details for the modal table
        $details = DB::select("SELECT
                    CONCAT_WS(' | ', emp.employee_name, job.employee_code, es.employment_status) AS employee_name,
                    hej.joining_date,
                    des.designation_name,
                    c.salary_amount,
                    c.increase_amount,
                    c.new_salary_amount,
                    sg.grade_name,
                    c.notes
                FROM hrm_salary_increment_details c
                JOIN hrm_employee_job_info job ON job.id = c.hrm_employee_job_info_id
                JOIN hrm_employee emp ON emp.id = job.hrm_employee_id
                JOIN hrm_employment_status es ON es.id = job.hrm_employment_status_id
                JOIN hrm_designation des ON des.id = c.hrm_designation_id
                LEFT JOIN hrm_employee_joining hej ON hej.hrm_employee_id = emp.id
                LEFT JOIN hrm_salary_grade sg ON sg.id = c.hrm_salary_grade_id
                WHERE c.hrm_salary_increment_id = ?
                ORDER BY des.priority ASC
            ", [$id]);

        return view('salary_increment.salary_increment_show', [
            'increment'      => $increment[0] ?? null,
            'details'        => $details,
            'total_employee' => count($details),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $check   = DB::table('hrm_salary_increment')->where('id', '=', $id)->where('status', '=', 2)->first();

        if (!empty($check)) {
            session()->flash('alert-danger', 'Sorry Already Salary Generated With New Increment Salary !!');
            return Redirect()->back();
        }

        $edit_data = DB::SELECT("SELECT
                                        a.id,
                                        a.note,
                                        a.effective_date,
                                        h.location_name,
                                        CONCAT(e.employee_name, ' || ', d.employee_code) AS employee_name,
                                        f.designation_name,
                                        f.id as hrm_designation_id,
                                        g.depertment_name,
                                        c.salary_amount,
                                        c.increase_amount as increase,
                                        c.amount_type,
                                        c.new_salary_amount,
                                        c.id as details_id,
                                        h.id as location_id,
                                        hej.joining_date,
                                        c.increase_amount AS increase_amount,
                                        IF(a.apply_for = 1, 'Increment', 'Promotion') AS apply_for,
                                        a.apply_for as apply_for_id,
                                        a.increment_amount,
                                        k.grade_name,
                                        k.id as hrm_salary_grade_id,

                                        (
                                            SELECT
                                                CONCAT(
                                                ' Date ', IFNULL(DATE_FORMAT(si.effective_date, '%d-%m-%Y'), ''),
                                                    ' | Amount: ',
                                                    IFNULL((sidt.new_salary_amount - sidt.salary_amount), 0)
                                                )
                                            FROM hrm_salary_increment si
                                            JOIN hrm_salary_increment_details sidt
                                                ON si.id = sidt.hrm_salary_increment_id
                                            JOIN hrm_employee_job_info ji
                                                ON sidt.hrm_employee_job_info_id = ji.id
                                            WHERE ji.hrm_employee_id = e.id
                                            AND si.status = 2
                                            AND si.effective_date < CURDATE()
                                            ORDER BY si.effective_date DESC
                                            LIMIT 1
                                        ) AS last_increment_note

                                    FROM
                                        hrm_salary_increment a
                                            JOIN
                                        hrm_month b ON MONTH(a.effective_date) = b.id
                                            AND a.status = 1 AND a.id = $id
                                            JOIN
                                        hrm_salary_increment_details c ON a.id = c.hrm_salary_increment_id
                                            JOIN
                                        hrm_employee_job_info d ON c.hrm_employee_job_info_id = d.id
                                            AND d.employee_activity = 1
                                            JOIN
                                        hrm_employee e ON d.hrm_employee_id = e.id
                                            JOIN
                                        hrm_employee_joining hej ON e.id = hej.hrm_employee_id
                                            JOIN
                                        hrm_designation f ON c.hrm_designation_id = f.id
                                            JOIN
                                        hrm_depertment g ON d.hrm_depertment_id = g.id
                                            JOIN
                                        hrm_location h ON a.hrm_location_id = h.id

                                            JOIN
                                        hrm_employee_salary i ON i.hrm_employee_job_info_id = d.id
                                            LEFT JOIN
                                        hrm_salary_grade_master j ON j.id = i.hrm_salary_grade_master_id
                                            LEFT JOIN
                                        hrm_salary_grade k ON
                                            (c.hrm_salary_grade_id IS NOT NULL AND k.id = c.hrm_salary_grade_id)
                                            OR
                                            (c.hrm_salary_grade_id IS NULL AND k.id = j.hrm_salary_grade_id)

                                        ORDER BY
                                            f.priority ASC
                                        ");


        $user_id = Auth::user()->id;
        $user_location = DB::select("SELECT a.id,a.location_name,b.default_location FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");


        return view('salary_increment.edit_salary_increment')
            ->with('edit_data', $edit_data)
            ->with('user_location',  $user_location);
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
    public function destroy(Request $request, $id)
    {



        $check   = DB::table('hrm_salary_increment')->where('id', '=', $id)->where('status', '=', 2)->first();

        if (!empty($check)) {
            session()->flash('alert-danger', 'Sorry Already Generated With Salary !!');
            return Redirect()->back();
        }

        DB::table('hrm_salary_increment_details')->where('hrm_salary_increment_id', '=', $id)->delete();
        DB::table('hrm_salary_increment')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'Successfully Deleted!');
        return Redirect::to('salaryincrement');
    }


    public function delete(Request $request, $id)
    {

        DB::table('hrm_salary_increment_details')->where('id', '=', $id)->delete();

        $this->recordActivity(
            1,
            'Deleted Salary Increment From Edit Form',
            null,
            $id,
            'hrm_salary_increment_details'
        );

        return Response::json(array('massages' => true));
    }

    public function incrementupdatedetails(Request $request, $id)
    {

        $amount = $request->amount;
        $hrm_salary_grade_id = $request->hrm_salary_grade_id;
        $hrm_designation_id = $request->hrm_designation_id;

        $insert_details = HrmSalaryIncrementDetails::where('id', $id)->first();
        $insert_details->increase_amount =  $amount - $insert_details->salary_amount;
        $insert_details->new_salary_amount = $amount;
        $insert_details->hrm_salary_grade_id = (int) $hrm_salary_grade_id;
        $insert_details->hrm_designation_id = (int) $hrm_designation_id;
        // dd($insert_details);
        $insert_details->save();

        $this->recordActivity(
            1,
            'Update Salary Increment From Edit Form',
            $insert_details,
            $id,
            'hrm_salary_increment_details'
        );

        return Response::json(array('massages' => true));
    }




    public function updatedetails(Request $request, $id, $amount)
    {

        dd($amount);
        // dd($request->all());
        // $check   = DB::table('hrm_salary_extra_feature')->where('id','=',$id)->where('generate_type','=',2)->first();

        // if (!empty($check)){
        //     session()->flash('alert-danger', 'Sorry Already Generated With Salary !!');
        //     return Redirect()->back();
        // }
        DB::UPDATE("Update hrm_salary_extra_feature_details SET amount =$amount WHERE id = $id ");
        // DB::table('hrm_salary_extra_feature_details')->where('id', '=', $id)->delete();
        // DB::table('hrm_salary_extra_feature')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'Successfully Updated!');
        return Redirect()->back();
    }

    /*/*

        Salary Increments Approved
    */
    public function incrementApproved(Request $request, $id, $hrm_location_id)
    {


        $hasZeroIncrease = DB::table('hrm_salary_increment_details')
            ->where('hrm_salary_increment_id', $id)
            ->where('increase_amount', 0)
            ->exists();

        if ($hasZeroIncrease) {
            return redirect()->back()->with(
                'alert-danger',
                'Approval failed! One or more employees have an increase amount of 0. Please update the increment amount before approving.'
            );
        }

        $find_data = DB::SELECT(
            "SELECT
            a.id, a.effective_date, b.hrm_employee_job_info_id, b.new_salary_amount, a.apply_for, b.hrm_designation_id, b.hrm_salary_grade_id, b.increase_amount
         FROM
            hrm_salary_increment a
                JOIN
            hrm_salary_increment_details b ON a.id = b.hrm_salary_increment_id
                AND a.status = 1 AND a.hrm_location_id = $hrm_location_id
                AND a.id = $id
                JOIN
            hrm_employee_job_info c ON b.hrm_employee_job_info_id = c.id
                AND c.hrm_location_id = a.hrm_location_id AND c.employee_activity = 1"
        );

        $count_row = count($find_data);


        if (!empty($find_data)) {
            DB::beginTransaction();

            try {
                foreach ($find_data as $keys) {

                    $xmasDay = new DateTime($keys->effective_date . '- 1 day');
                    $end_date = $xmasDay->format('Y-m-d');

                    $ldate = date('Y-m-d');
                    $cxmasDay = new DateTime($ldate . '- 1 day');
                    $cend_date = $cxmasDay->format('Y-m-d');

                    $jobid = $keys->hrm_employee_job_info_id;
                    $newsalary = $keys->new_salary_amount;
                    $new_hrm_designation_id = $keys->hrm_designation_id;
                    $new_hrm_salary_grade_id = $keys->hrm_salary_grade_id;

                    $check_data = DB::select("SELECT start_date FROM hrm_employee_activity WHERE hrm_employee_job_info_id = $jobid AND end_date IS NULL");
                    $date_from = $keys->effective_date;

                    $final_check = DB::select(
                        "SELECT start_date FROM hrm_employee_activity WHERE hrm_employee_job_info_id IN (
                        SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id IN (
                            SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $jobid))
                        AND start_date = '$date_from'"
                    );

                    if (!empty($final_check)) {
                        $employee_name = DB::SELECT("SELECT
                                                    CONCAT(b.employee_name,' | ' ,a.employee_code) AS employee_name
                                                FROM
                                                    hrm_employee_job_info a
                                                        JOIN
                                                    hrm_employee b ON a.hrm_employee_id = b.id
                                                WHERE
                                                    a.id = $jobid")[0]->employee_name;


                        $request->session()->flash('alert-danger', 'Sorry this employees information missmatch, please correction then approve!' . $employee_name);

                        return Redirect::to('employeetransfer');
                    }


                    if ($date_from < $check_data[0]->start_date) {

                        continue;
                    } else {

                        $check_two = DB::select(
                            "SELECT start_date FROM hrm_employee_activity WHERE hrm_employee_job_info_id IN (
                            SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id IN (
                                SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $jobid))
                                AND start_date = '$date_from'"
                        );

                        if (!empty($check_two)) {
                            continue;
                        } else {


                            $oldinfo = HrmEmployeeJobInfo::find($jobid);
                            $insert = new HrmEmployeeJobInfo;
                            $insert->hrm_employee_id = $oldinfo->hrm_employee_id;
                            $insert->employee_code = $oldinfo->employee_code;
                            $insert->hrm_depertment_id = $oldinfo->hrm_depertment_id;
                            $insert->hrm_designation_id = $new_hrm_designation_id;
                            $insert->hrm_category_id = $oldinfo->hrm_category_id;
                            $insert->hrm_employment_status_id = $oldinfo->hrm_employment_status_id;
                            $insert->hrm_manage_by_id = $oldinfo->hrm_manage_by_id;
                            $insert->employee_shift_status = 1;
                            $insert->overtime_status = $oldinfo->overtime_status;
                            $insert->hrm_plant_id = $oldinfo->hrm_plant_id;
                            $insert->employee_activity = 1;
                            $insert->basic_salary = $newsalary;
                            $insert->hrm_location_id = $oldinfo->hrm_location_id;
                            $insert->hrm_section_id = $oldinfo->hrm_section_id;
                            $insert->insurance = $oldinfo->insurance;
                            $insert->users_id = Auth::user()->id;
                            $insert->save();

                            $oldcard = DB::SELECT("SELECT * FROM hrm_employee_card_code WHERE hrm_employee_job_info_id = $jobid");

                            if (!empty($oldcard)) {
                                $insert_card = new HrmEmployeeCardCode;
                                $insert_card->card_code = $oldcard[0]->card_code;
                                $insert_card->device_id = $oldcard[0]->device_id;
                                $insert_card->hrm_employee_job_info_id = $insert->id;
                                $insert_card->save();
                            } else {
                                $insert_card = new HrmEmployeeCardCode;
                                $insert_card->card_code = 'Empty' . $oldinfo->hrm_employee_id;
                                $insert_card->device_id = 1;
                                $insert_card->hrm_employee_job_info_id = $insert->id;
                                $insert_card->save();
                            }

                            $oldshift = DB::SELECT(
                                "SELECT * FROM hrm_employee_shift WHERE hrm_employee_job_info_id = $jobid AND id IN (
                                SELECT MAX(id) AS id FROM hrm_employee_shift WHERE hrm_employee_job_info_id = $jobid)"
                            );
                            // dd($oldshift[0]);
                            $end_shift_date = $oldshift[0]->start_date;
                            $start_shift_date = $date_from;
                            $user_id = auth()->id();
                            $old_shift_id = $oldshift[0]->hrm_shift_id;



                            // Need to work in future

                            if ($date_from < $end_shift_date) {


                                    DB::update("UPDATE hrm_employee_shift
                                    SET hrm_employee_job_info_id = ?
                                    WHERE id IN (
                                        SELECT id
                                        FROM (
                                            SELECT id
                                            FROM hrm_employee_shift
                                            WHERE start_date >= ?
                                              AND valid = 1
                                              AND hrm_employee_job_info_id IN (
                                                  SELECT id
                                                  FROM hrm_employee_job_info
                                                  WHERE hrm_employee_id = ?
                                              )
                                        ) AS temp
                                    )
                                ", [$insert->id, $date_from, $oldinfo->hrm_employee_id]);



                                    $max_id = DB::table('hrm_employee_shift')
                                        ->where('valid', 1)
                                        ->where('start_date', '<', $date_from)
                                        ->whereIn('hrm_employee_job_info_id', function ($query) use ($oldinfo) {
                                            $query->select('id')
                                                ->from('hrm_employee_job_info')
                                                ->where('hrm_employee_id', $oldinfo->hrm_employee_id);
                                        })
                                        ->max('id');

                                    $pre_date = date('Y-m-d', strtotime($date_from . ' -1 day'));

                                        // dd($pre_date,$max_id);
                                    DB::table('hrm_employee_shift')
                                    ->where('id', $max_id)
                                    ->update(['end_date' => $pre_date,'comment' =>'Update From Salary Increment']);



                                    // $ ->end_date  = $max_pre_date;
                                    // $insert_shift->save();

                                    $min_id = DB::table('hrm_employee_shift')
                                        ->where('valid', 1)
                                        ->where('start_date', '>', $date_from)
                                        ->whereIn('hrm_employee_job_info_id', function ($query) use ($oldinfo) {
                                            $query->select('id')
                                                ->from('hrm_employee_job_info')
                                                ->where('hrm_employee_id', $oldinfo->hrm_employee_id);
                                        })
                                        ->min('id');

                                        DB::table('hrm_employee_shift')
                                        ->where('id', $min_id)
                                        ->update(['start_date' => $date_from,'comment' =>'Update From Salary Increment 2']);

                                }else{

                                     $pre_date = date('Y-m-d', strtotime($date_from . ' -1 day'));

                                      DB::update("UPDATE hrm_employee_shift
                                                    SET end_date= '$pre_date',
                                                    comment='from employee Salary Increment',
                                                    users_id= $user_id,
                                                    updated_at='$ldate'
                                                WHERE  end_date is null
                                                    AND hrm_employee_job_info_id = $oldinfo->id  ");
                                                    // dd($oldinfo->hrm_shift_id);

                                        $insert_shift  = new HrmEmployeeShift;
                                        $insert_shift->hrm_employee_job_info_id = $insert->id;
                                        $insert_shift->hrm_shift_id             = $old_shift_id;
                                        $insert_shift->start_date               = $date_from;
                                        $insert_shift->valid                    = 1;
                                        $insert_shift->comment                  = 'Salary Increment';
                                        $insert_shift->users_id                 = Auth::user()->id;
                                        $insert_shift->save();

                                }



                            $oldsalary = DB::SELECT("SELECT * FROM hrm_employee_salary WHERE hrm_employee_job_info_id = $jobid");

                            if (Config::get('module_config.payroll_module') == 1) {
                                $hrm_employee_job_info_id = $insert->id;

                                if($new_hrm_salary_grade_id){
                                  $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id',$new_hrm_salary_grade_id)->first()->id;
                                }else{
                                  $hrm_salary_grade_master_id =  $oldsalary[0]->hrm_salary_grade_master_id;
                                }


                                $gross_salary = $insert->basic_salary;
                                $hrm_designation_id = $insert->hrm_designation_id;
                                $old_hrm_employee_job_info_id = $jobid;

                                $getFunction = new CommonController();
                                $getFunction->insert_salary_config(
                                    $hrm_employee_job_info_id,
                                    $hrm_salary_grade_master_id,
                                    $gross_salary,
                                    $hrm_designation_id,
                                    $old_hrm_employee_job_info_id
                                );
                            }

                            $insert_employee_activity = new HrmEmployeeActivity;
                            $insert_employee_activity->hrm_employee_job_info_id = $insert->id;
                            $insert_employee_activity->activity_date = $ldate;
                            $insert_employee_activity->comment = 'Salary Increment';
                            $insert_employee_activity->users_id = Auth::user()->id;
                            $insert_employee_activity->activity = 1;

                            if ($keys->apply_for == 2) {
                                $insert_employee_activity->hrm_employee_activity_status_id = 3;
                            } else {
                                $insert_employee_activity->hrm_employee_activity_status_id = 4;
                            }

                            $insert_employee_activity->start_date = $keys->effective_date;
                            $insert_employee_activity->old_hrm_employee_job_info_id = $jobid;
                            $insert_employee_activity->save();

                            if ($keys->apply_for == 2) {
                                $old_emp_id = $oldinfo->hrm_employee_id;
                                $effect_date = $keys->effective_date;

                                $find_prom_data = DB::SELECT("SELECT id FROM hrm_last_promotion WHERE hrm_employee_id = $old_emp_id AND promotion_date = '$keys->effective_date'");

                                if (empty($find_prom_data)) {
                                    $insert_employee_activity = new HrmLastPromotion;
                                    $insert_employee_activity->hrm_employee_id = $oldinfo->hrm_employee_id;
                                    $insert_employee_activity->promotion_date = $keys->effective_date;
                                    $insert_employee_activity->users_id = Auth::user()->id;
                                    $insert_employee_activity->save();
                                }
                            }

                            DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date' WHERE end_date IS NULL AND hrm_employee_job_info_id = $jobid");
                            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0 WHERE id = $jobid");


                        }
                    }
                }

                DB::update(
                    "UPDATE hrm_salary_increment SET status = 2 WHERE id IN (
                    SELECT id FROM (
                        SELECT a.id FROM hrm_salary_increment a JOIN hrm_salary_increment_details b ON a.id = b.hrm_salary_increment_id AND a.status = 1 AND a.hrm_location_id = $hrm_location_id AND a.id = $id GROUP BY a.id
                    ) a
                )"
                );

                DB::commit();
                $this->recordActivity(1, 'Salary Increment', 'Total Employees: ' . $count_row, $id, 'hrm_salary_increment');
                return back();
            } catch (\Exception $e) {
                DB::rollback();
                dd($e->getMessage());
                return back();
            }
        } else {

            $request->session()->flash('alert-danger', 'Sorry Already Implemented.!');
            return Redirect()->back();
        }
    }

    public function export($id)
    {
        // dd($id);
        $data   = DB::select("SELECT
                a.id,
                a.note,
                CONCAT(b.month_name,
                        ' - ',
                        YEAR(a.effective_date)) AS effective_month,
                h.location_name,
                If(a.status = 1,'Processing','Completed') as status,
                e.employee_name,
                d.employee_code,
                hej.joining_date,
                e.id as employee_id,
                f.alis AS designation_name,
                (SELECT  designation_name
            from hrm_designation  where c.hrm_designation_id = id limit 1) as new_designation_name,
                g.depertment_name AS depertment_name,
                c.salary_amount AS salary_amount,
                c.new_salary_amount - c.salary_amount AS increase_amount,
                c.new_salary_amount AS new_salary_amount,
                IF(a.apply_for = 1, 'Increment', 'Promotion') AS apply_for,
                i.category_name,
                j.section_name,
                c.notes
            FROM
                hrm_salary_increment a
                    JOIN
                hrm_month b ON MONTH(a.effective_date) = b.id
                    JOIN
                hrm_salary_increment_details c ON a.id = c.hrm_salary_increment_id
                    JOIN
                hrm_employee_job_info d ON c.hrm_employee_job_info_id = d.id
                    JOIN
                hrm_employee e ON d.hrm_employee_id = e.id
                    JOIN
                hrm_employee_joining hej ON e.id = hej.hrm_employee_id
                    JOIN
                hrm_designation f ON d.hrm_designation_id = f.id
                    JOIN
                hrm_depertment g ON d.hrm_depertment_id = g.id
                    JOIN
                hrm_location h ON a.hrm_location_id = h.id
                    join
                hrm_category i ON d.hrm_category_id = i.id
                    join
                hrm_section j on d.hrm_section_id = j.id

            WHERE
                a.id = $id
                group by e.id
                Order by a.id desc
            ");

        return Excel::download(
            new IncrementPromotionReportExport($data),
            'increment_promotion_report.xlsx'
        );
    }


    public function approved_delete($hrm_salary_increment_details_id){
        
        dd("Need to test");
        

        $user_id = Auth::user()->id;
        

        $id= DB::SELECT("SELECT hrm_employee_job_info_id from  hrm_salary_increment_details Where id = $hrm_salary_increment_details_id")[0]->hrm_employee_job_info_id;

        // dd($id);

        DB::beginTransaction();
            try {


           $getPreJobId= DB::SELECT("select * from  hrm_employee_activity Where hrm_employee_job_info_id = $id")[0]->old_hrm_employee_job_info_id;
           
  
           if(!empty($getPreData)){

                DB::DELETE("Delete From hrm_employee_activity Where hrm_employee_job_info_id = $id");
                DB::update("update  hrm_employee_activity SET end_date=null  Where hrm_employee_job_info_id = $getPreJobId");
                DB::DELETE("delete from hrm_employee_salary_details Where hrm_employee_salary_id in (Select id from hrm_employee_salary Where hrm_employee_job_info_id=$id)");
                DB::DELETE("delete from hrm_employee_salary Where hrm_employee_job_info_id=$id");
                DB::DELETE("delete from hrm_employee_shift Where hrm_employee_job_info_id=$id");

                DB::update("
                        UPDATE hrm_employee_shift
                        SET end_date = NULL
                        WHERE id = (
                            SELECT MAX(id)
                            FROM hrm_employee_shift
                            WHERE hrm_employee_job_info_id = $getPreJobId
                        )
                    ");
                DB::update("update  hrm_salary_increment_details SET is_active=0,note='DeletedBY'.$user_id  Where hrm_salary_increment_details_id = $hrm_salary_increment_details_id");



           }else{
            dd("No data found");
           }

            DB::commit();
                return back();
            } catch (\Exception $e) {
                DB::rollback();
                dd($e->getMessage());
                return back();
            }

// Alter table hrm_salary_increment_details add is_active int default 1 ;
// Alter table hrm_salary_increment_details add note nvarchar(100) ;

    }




}
