<?php

namespace App\Http\Controllers;

use App\Models\HrmBank;
use App\Models\HrmMonth;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeResign;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeSalaryDetails;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Validator;

class EmployeeSalaryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear = date('Y');

        $user_id = auth()->id();
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_salary.employeesalary_list')
            -> with('default_user_location',  $default_user_location)
            -> with('cmonth',  $cmonth)
            -> with('cyear',  $cyear) ;
    }

    public function employeesalarylistdata(Request $request)
    {
        $user_id = auth()->id();
        $condition ='';
        $subcondition ='';

        if ($request->location == null) {

            $condition =  " AND b.hrm_location_id in (SELECT hrm_location_id FROM user_location where users_id= $user_id ) ";
            $subcondition =  " AND bbb.hrm_location_id in (SELECT hrm_location_id FROM user_location where users_id= $user_id ) ";

        } else {
            $condition =  ' AND b.hrm_location_id='.$request->location;
            $subcondition =  ' AND bbb.hrm_location_id='.$request->location;
        }

        if ($request->department == null) {
        } else {
            $condition = $condition.' AND b.hrm_depertment_id='.$request->department;
        }

        if ($request->section == null) {
        } else {
            $condition = $condition.' AND b.hrm_section_id='.$request->section;
        }

        if ($request->category == null) {
        } else {
            $condition = $condition.' AND b.hrm_category_id ='.$request->category;
        }


        if (isset($request->employee_type)){
            $list = implode(',',$request->employee_type);
            $condition  =  $condition . " AND b.hrm_employment_status_id in ($list)" ;
        }

        // if (isset($request->employee_type)){

        //     $condition  =  $condition . " AND b.hrm_employment_status_id =".$request->employee_type ;
        // }




        $date_of_month  = $request->year.'-'.$request->month.'-01';
        $year_month     = $request->year.'-'.$request->month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = auth()->id();

        $user_id    = auth()->id();
        $data       = DB::select("SELECT
                                    a.id,
                                    concat(c.employee_name,' | ', ifnull(b.employee_code, ' '), ' | ',d.depertment_name,' | ',f.designation_name ,' | ', bb.employment_status,' | Joining : ', DATE_FORMAT(cc.joining_date, '%d-%M-%Y') ) as employee_name,
                                    c.id as employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    f.priority,
                                    e.location_name,
                                    a.salary_amount,
                                    a.account_no,
                                    a.accounts_code,
                                    IF(a.payment_mode = 1, 'Cash', k.short_name) AS payment_mode,
                                    GROUP_CONCAT(CONCAT(g.amount)
                                        SEPARATOR '<br>') AS amount,
                                    GROUP_CONCAT(CONCAT(IF(g.amount_type = 1, '%', 'TK'))
                                        SEPARATOR '<br>') AS amount_type,
                                    GROUP_CONCAT(CONCAT(h.salary_head)
                                        SEPARATOR '<br>') AS salary_head,
                                    GROUP_CONCAT(CONCAT(g.actual_amount) SEPARATOR '<br>') AS salary_head_amount,
                                    GROUP_CONCAT(CONCAT(IF(i.generate_type = 1,
                                                    'Addition',
                                                    'Deduction'))
                                    SEPARATOR '<br>') AS Status

                                FROM
                                    hrm_employee_salary a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        $condition
                                        AND b.id in
                                        (SELECT Max(aa.hrm_employee_job_info_id) as id
                                        FROM
                                        (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                            '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7,5)
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                            AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                            end_date BETWEEN '$date_of_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7,5)
                                            UNION ALL
                                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                    WHERE
                                                     end_date BETWEEN '$date_of_month' AND '$lastdate'
                                                    AND end_date IS NOT NULL
                                                    AND hrm_employee_activity_status_id NOT IN (7,5)
                                            )  aa JOIN hrm_employee_job_info bbb ON aa.hrm_employee_job_info_id=bbb.id   $subcondition
                                            GROUP BY bbb.hrm_employee_id)
                                        JOIN
                                    hrm_employment_status bb ON b.hrm_employment_status_id=bb.id and bb.status<>2
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_employee_joining cc ON c.id = cc.hrm_employee_id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        JOIN
                                    hrm_employee_salary_details g ON a.id = g.hrm_employee_salary_id
                                        JOIN
                                    hrm_salary_head h ON g.hrm_salary_head_id = h.id AND h.apply_for = 1
                                        JOIN
                                    hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id
                                        JOIN
                                    user_location j ON b.hrm_location_id = j.hrm_location_id AND j.users_id = $user_id
                                        LEFT JOIN
                                    hrm_bank k ON a.hrm_bank_id = k.id
                                    GROUP BY a.id,c.employee_name,d.depertment_name,f.designation_name,e.location_name,a.salary_amount,c.id,b.employee_code,a.account_no,a.payment_mode,a.accounts_code,k.short_name,f.priority");



        $data = collect($data);

        return datatables()->of($data)
            ->addColumn('Link', function ($data) {
                return '
                <a href="'.url('/employeesalary').'/'.encrypt($data->id).'/edit'.'" data-title="Edit Employee Salary" data-modal-size="xl" modal-center footer-none class="modalLink btn btn-sm block btn-flat">
                    <i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit
                </a>';
            })
            ->rawColumns(['Link', 'Status', 'salary_head', 'amount', 'amount_type', 'salary_head_amount'])
            ->make(true);
    }

    public function bankname_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeesalary')
                ->withErrors($validator)
                ->withInput();
        }

        $insert     = new HrmBank;
        $insert->bank_name       = $request->bank_name;
        $insert->short_name      = $request->short_name;
        $insert->save();

       return back()->with('success','Successfully New File Type Added.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        // dd($id);
        $data['edit_data'] = DB::SELECT("SELECT
                                a.id,
                                c.employee_name,
                                a.salary_amount,
                                h.id as salary_head_id,
                                h.salary_head,
                                    IF(i.generate_type = 1,
                                    'Addition',
                                    'Deduction') AS Status,
                                i.group_name AS group_name,
                                    g.amount,
                                    g.amount_type,
                                IF(g.amount_type = 1, '%', 'TK') As type ,
                                IF(g.amount_type = 1,
                                    (a.salary_amount * g.amount / 100),
                                    g.amount) AS salary_head_amount,
                                g.actual_amount,
                                (SELECT
                                    aa.head_name
                                FROM
                                    hrm_default_head aa
                                JOIN
                                    hrm_set_default_head bb ON aa.id = bb.hrm_default_head_id
                                WHERE
                                    aa.status = 1 AND bb.hrm_salary_head_id=h.id)  as fixed_salary_head


                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    AND a.id = $id
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_depertment d ON b.hrm_depertment_id = d.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_employee_salary_details g ON a.id = g.hrm_employee_salary_id
                                   Right JOIN
                                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                    JOIN
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type = 1 AND h.apply_for=1");


        $data['edit_data_deduction'] = DB::SELECT("SELECT
                                a.id,
                                c.employee_name,
                                a.salary_amount,
                                h.id as salary_head_id,
                                h.salary_head,
                                    IF(i.generate_type = 1,
                                    'Addition',
                                    'Deduction') AS Status,
                                i.group_name AS group_name,
                                    g.amount,
                                    g.amount_type,
                                IF(g.amount_type = 1, '%', 'TK') As type ,
                                IF(g.amount_type = 1,
                                    (a.salary_amount * g.amount / 100),
                                    g.amount) AS salary_head_amount,
                                g.actual_amount,
                                (SELECT
                                    aa.head_name
                                FROM
                                    hrm_default_head aa
                                JOIN
                                    hrm_set_default_head bb ON aa.id = bb.hrm_default_head_id
                                WHERE
                                    aa.status = 1 AND bb.hrm_salary_head_id=h.id)  as fixed_salary_head
                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    AND a.id = $id
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_depertment d ON b.hrm_depertment_id = d.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_employee_salary_details g ON a.id = g.hrm_employee_salary_id
                                   Right JOIN
                                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                    JOIN
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=2   AND h.apply_for=1");

        $data['master_data'] = DB::SELECT("SELECT
                                a.id,
                                -- c.employee_name,
                                concat(c.employee_name,' | ',ifnull(b.employee_code, ' ')) as employee_name,
                                c.id AS employee_id,
                                d.depertment_name,
                                f.designation_name,
                                e.location_name,
                                a.salary_amount,
                                a.account_no,
                                a.payment_mode,
                                a.by_bank_percent,
                                g.id as hrm_bank_id,
                                g.bank_name,
                                a.accounts_code

                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    AND a.id = $id
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_depertment d ON b.hrm_depertment_id = d.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                   LEFT JOIN
                                hrm_bank g ON g.id=a.hrm_bank_id ")[0];




        $data['fixrd_hrm_salary_head'] = DB::SELECT("SELECT
                                    bb.hrm_salary_head_id as fixrd_hrm_salary_head_id
                                    ,aa.head_name
                                FROM
                                    hrm_default_head aa
                                        JOIN
                                    hrm_set_default_head bb ON aa.id = bb.hrm_default_head_id
                                WHERE
                                    aa.status = 1 ");

        $data['bank_name'] = DB::select("
            select * from hrm_bank
        ");

        return view('employee_salary.edit_employeesalary', $data);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $status = false;

        $validator = Validator::make($request->all(), [
            'salary_amount' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            $count_row_addition  = count($request->amountaddition);
            $count_row_deduction = count($request->amountdeduction);

            DB::beginTransaction();

            try {


                $insert_salary_master  = HrmEmployeeSalary::find($id);
                $insert_salary_master->salary_amount              = $request->salary_amount;
                $insert_salary_master->account_no                 = $request->account_no;
                $insert_salary_master->accounts_code              = $request->accounts_code;
                $insert_salary_master->payment_mode               = $request->payment_mode;
                $insert_salary_master->users_id                   = auth()->id();
                $insert_salary_master->by_bank_percent            = $request->by_bank_percent;
                if($request->payment_mode==1){
                    $insert_salary_master->hrm_bank_id              = null;
                } else{
                    $insert_salary_master->hrm_bank_id              = $request->bank_name;
                }
                $insert_salary_master->save();



                $employee_jobinfo = HrmEmployeeJobInfo::where('id', $insert_salary_master->hrm_employee_job_info_id)->first();
                $employee_jobinfo->basic_salary     = $request->salary_amount;
                $employee_jobinfo->users_id                   = auth()->id();
                $employee_jobinfo->save();

                DB::Delete("DELETE FROM hrm_employee_salary_details WHERE hrm_employee_salary_id=$id  and hrm_salary_head_id in (SELECT id from hrm_salary_head WHERE apply_for=1)");


                for ($r = 0; $r < $count_row_addition; $r++) {

                    $check= $request->addition_actual_amount[$r];

                    if ($check > 0) {
                        $insert_salary_details  = new HrmEmployeeSalaryDetails;
                        $insert_salary_details->hrm_employee_salary_id     = $insert_salary_master->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
                        $insert_salary_details->amount                     = $request->amountaddition[$r];
                        $insert_salary_details->amount_type                = $request->typeaddition[$r];
                        $insert_salary_details->actual_amount              = $request->addition_actual_amount[$r];
                        $insert_salary_details->save();

                        $this->recordActivity(
                             1,
                             'Updated Employee Salary Addition.',
                             $insert_salary_details,
                             $insert_salary_details->id,
                             'hrm_employee_salary_details'
                        );


                    }
                }



                for ($r = 0; $r <$count_row_deduction; $r++) {
                    $check= $request->deduction_actual_amount[$r];

                    if ($check > 0) {
                        $insert_salary_details  = new HrmEmployeeSalaryDetails;
                        $insert_salary_details->hrm_employee_salary_id     = $insert_salary_master->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->deduction_id[$r];
                        $insert_salary_details->amount                     = $request->amountdeduction[$r];
                        $insert_salary_details->amount_type                = $request->typededuction[$r];
                        $insert_salary_details->actual_amount              = $request->deduction_actual_amount[$r];
                        $insert_salary_details->save();


                        $this->recordActivity(
                             1,
                             'Updated Employee Salary Deduction.',
                             $insert_salary_details,
                             $insert_salary_details->id,
                             'hrm_employee_salary_details'
                        );
                    }
                }

                DB::commit();

                $this->recordActivity(
                     1,
                     'Updated Employee Salary',
                     $insert_salary_master->getChanges(),
                     $insert_salary_master->id,
                     'hrm_employee_salary'
                );

                $status = true;
                $message = 'Employee Salary has been updated..!';
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function cancel(Request $request,$id){

        $cancel = HrmEmployeeResign::find($id);

        if (empty($cancel)) {
            session()->flash('alert-danger', 'Invalid Application !!');
            return Redirect()->back();
        }

        DB::table('hrm_employee_resignation')->where('id', $id)->delete();

        return redirect()->to('employeeresign')->with('alert-success', 'successfully deleted !');
    }

    public function dynamicTable(Request $request){

       $dataset =  DB::select("SELECT * FROM `hrm_depertment` ");

       $view = request()->ajax() ? 'employee_salary.dynamic_list' :  'employee_salary.dynamic_list';
        return view($view)
            -> with('dataset',  $dataset);
    }

    public function enrollmentIndex()
    {
        if (request()->ajax()) {

            $user_id = auth()->id();

            $condition = '';

            if (request()->location) {
                $condition =  ' AND a.hrm_location_id='.request()->location;

            }

            if (request()->department) {
                $condition = ' AND a.hrm_depertment_id='.request()->department;
            }

            $data = DB::select("SELECT
                                    a.id,
                                    a.employee_code,
                                    b.id as employee_id,
                                    b.employee_name,
                                    bb.depertment_name,
                                    bbb.designation_name,
                                    cc.location_name,
                                    b.Images
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee b ON a.hrm_employee_id = b.id
                                        JOIN
                                    hrm_depertment bb ON a.hrm_depertment_id = bb.id
                                        JOIN
                                    hrm_designation bbb ON a.hrm_designation_id = bbb.id
                                        JOIN
                                    hrm_location cc ON a.hrm_location_id = cc.id
                                        JOIN
                                    hrm_employment_status d ON a.hrm_employment_status_id = d.id
                                        LEFT JOIN
                                    hrm_employee_salary c ON a.id = c.hrm_employee_job_info_id
                                WHERE
                                    a.employee_activity = 1
                                    AND c.id is null
                                    AND d.status != 2
                                    $condition
                                ");

            return datatables()->of($data)
                ->addColumn('EmployeeName', function($data) {
                    return $data->employee_name;
                })
                ->addColumn('Action', function($data) {
                    return '<a href="'.route('employee.enrollment.action', encrypt($data->id)) .'" class="btn btn-sm btn-success  canvasLink" data-drawer-width="full" data-title="Waiting For Enrollment">Make Enrollment</a>';
                })
                ->rawColumns(['Action', 'EmployeeName'])
                ->make(true);
        }


        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear = date('Y');

        $user_id = auth()->id();

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM user_location a JOIN hrm_location b ON a.hrm_location_id = b.id AND a.users_id = $user_id AND a.default_location = 1")[0];

        return view('employee_salary.new_enroll', compact('cmonth', 'cyear', 'default_user_location'));

    }

    public function enrollmentAction($id)
    {
        $job_id =  decrypt($id);
        $user_id = auth()->id();

        $employee_data = DB::select("SELECT
                                    a.id,
                                    a.Images,
                                    b.id as hrm_employee_job_info_id,
                                    a.employee_name,
                                    b.hrm_designation_id,
                                    IFNULL(b.employee_code, ' ') as employee_code,
                                    LPAD(a.id, 5, '0') Unique_Code,
                                    IFNULL(b.official_contact_no,'-') official_contact_no ,
                                    IFNULL(b.official_email,'N/A') official_email ,
                                    b.basic_salary gross_salary,
                                    (SELECT
                                            depertment_name
                                        FROM
                                            hrm_depertment
                                        WHERE
                                            id = b.hrm_depertment_id) department_name,

                                    (SELECT
                                            designation_name
                                        FROM
                                            hrm_designation
                                        WHERE
                                            id = b.hrm_designation_id) designation_name,

                                    (SELECT
                                            location_name
                                        FROM
                                            hrm_location
                                        WHERE
                                            id = b.hrm_location_id) location_name,
                                    DATE_FORMAT(f.joining_date, '%d %b, %Y') joining_date,
                                    DATE_FORMAT(DATE_ADD(f.joining_date, INTERVAL (
                                        SELECT period
                                        FROM hrm_probation_period
                                        WHERE id = (
                                            SELECT hrm_probation_period_id
                                            FROM hrm_employee_probation
                                            WHERE hrm_employee_id = a.id
                                        )
                                    ) MONTH), '%d-%m-%Y') AS probable_confirmation_date
                                FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                    AND b.employee_activity = 1
                                    JOIN
                                hrm_location c ON b.hrm_location_id = c.id

                                    JOIN
                                hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                    AND b.id = $job_id
                            GROUP BY a.id")[0];


        $bank_info = DB::table('hrm_employee_bank_info as a')
                        ->where('a.hrm_employee_id',$employee_data->id )
                        ->where('a.is_salary_transfer',1)
                        ->join('hrm_bank as b','b.id','=','a.hrm_bank_id')
                        ->select('a.*','b.bank_name')
                        ->first();

        return view('employee_salary.enrollment_form', compact('employee_data', 'bank_info'));
    }

    public function findSalaryHeads()
    {
        if (request()->ajax()) {
            $type = request()->generate_type;

            $edit_data = '';
            $edit_clause = '';
            $edit_cols = '';

            if ($master_id = request()->master_id) {
                $edit_cols = " c.amount_type, c.amount, c.hrm_salary_head_id,";

                $edit_data = " hrm_salary_grade_master a
                                JOIN
                            hrm_salary_grade b ON a.hrm_salary_grade_id = b.id
                                AND a.id = $master_id
                                JOIN
                            hrm_salary_grade_details c ON a.id = c.hrm_salary_grade_master_id
                                RIGHT JOIN";
                $edit_clause = " ON c.hrm_salary_head_id = d.id";
            }

            $generate_type = "WHERE e.generate_type = {$type} AND active_status = 1 ";

            $data = DB::select("SELECT
                    d.id,
                    $edit_cols
                    d.salary_head
                FROM
                    $edit_data
                    hrm_salary_head d $edit_clause
                        JOIN
                    hrm_salary_head_group as e ON d.hrm_salary_head_group_id = e.id
                    $generate_type
            ");

            return datatables()->of($data)
                ->addColumn('Amount', function($data) {
                    $amount = '';

                    if (isset($data->amount)) {
                        $amount = $data->amount;
                    }

                    return '<input name="amount['.$data->id.']" step="any" style="height:25px" class="input-amount did-floating-input field_show_hide" value="'.$amount.'" type="number">';
                })
                ->addColumn('Type', function($data) {
                    [$percent, $tk] = [
                        isset($data->amount_type) ? ($data->amount_type == 1 ? "selected" : "") : null,
                        isset($data->amount_type) ? ($data->amount_type == 2 ? "selected" : "") : null
                    ];

                    return '<select name="amount_type['.$data->id.']" class="amount_type did-floating-select field_show_hide" style="width: 100%;height:25px">
                        <option value="1" '.$percent.'>%</option>
                        <option value="2" '.$tk.'>Tk.</option>
                    </select>';
                })
                ->addColumn('Select', function($data) {
                    $checked = '';

                    if (isset($data->hrm_salary_head_id)) {
                        $checked = $data->hrm_salary_head_id == $data->id ? 'checked' : '';
                    }

                   return '<input type="checkbox" class="select-head" name="salary_head_id['.$data->id.']" value="'.$data->id.'" '.$checked.'>';
                })
                ->rawColumns(['Select','Amount','Type'])
                ->make(true);
        }
    }

    public function enrollmentConfirmation(Request $request)
    {
        // return $request;
        // die();
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'new_salary'        => 'required',
            'salary_grade'    => 'required',
        ]);
        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ));
        }

        $status = false;

        $findInfo = DB::SELECT("select id from hrm_employee_salary Where hrm_employee_job_info_id= $request->hrm_employee_job_info_id");

        if(!empty($findInfo[0]->id)){
            return response()->json([
                'status' => false,
                'message' => ' Sorry, Already Enrolled !',
                'error' => ''
            ]);
        }

        DB::beginTransaction();
        try {


            (new CommonController)->modify_insert_SalaryConfig($request);

            DB::commit();

            $status = true;
            $message = 'Enrollment Approved.';
        } catch (\Exception $e) {
            DB::rollBack();

            $message = 'Something went wrong.!';
            $error = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'error' => $error ?? ''
        ]);

    }
}
