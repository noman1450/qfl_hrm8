<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\HrmBank;
use Illuminate\Http\Request;

use App\Models\HrmLoanLedger;
use Jaspersoft\Client\Client;
use App\Models\HrmPayRegister;
use App\Models\HrmDeletedDataLog;
use App\Exports\PayRegisterExport;
use Illuminate\Support\Facades\DB;
use Jaspersoft\Service\jobService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HrmPayRegisterDetails;
use App\Models\HrmSalaryHead;
use Jaspersoft\Service\ReportService;
use App\Models\HrmLoanPayRegisterDetails;
use Illuminate\Support\Facades\Validator;

use Auth;

class PayRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = auth()->id();
        $running_month_year = DB::SELECT("SELECT
                                                a.year_id,
                                                a.hrm_month_id,
                                                b.month_name,
                                                c.id AS master_id,
                                                c.declaration_date,
                                                e.id as hrm_location_id,
                                                e.location_name
                                            FROM
                                                `pay_register` a
                                                    JOIN
                                                hrm_month b ON a.hrm_month_id = b.id
                                                    AND a.`salary_genarate_type` <> 0
                                                    AND a.apply_for = 1
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id
                                                    AND d.`users_id`= $user_id
                                                    JOIN
                                                hrm_location e ON d.hrm_location_id = e.id
                                            ORDER BY a.id DESC
                                            LIMIT 0 , 1");


        if (empty($running_month_year)) {
            $running_month_year = DB::SELECT("SELECT
                                                    b.id AS hrm_location_id,
                                                    b.location_name,
                                                    0 AS year_id,
                                                    0 AS hrm_month_id,
                                                    0 AS month_name,
                                                    0 AS master_id,
                                                    0 As declaration_date
                                                FROM
                                                    `user_location` a
                                                        JOIN
                                                    hrm_location b ON a.`hrm_location_id` = b.id
                                                        AND a.`users_id` = $user_id
                                                        AND a.`default_location` = 1");
        }

        return view('pay_register.payregister_list', compact('running_month_year'));
    }


    public function payregisterlistdata(Request $request)
    {
        // dd($request->all());

        $condition='';

        if($request->location==null){
            $location = 0;
        }else{
            $location = $request->location;
        }

        if($request->department==null){
        }else{
            $condition = ' AND b.hrm_depertment_id='.$request->department;
        }

        if($request->section==null){
        }else{
            $condition = $condition.' AND b.hrm_section_id='.$request->section;

        }
        if($request->category==null){
        }else{
            $condition = $condition.' AND b.hrm_category_id ='.$request->category;

        }

        if($request->hrm_salary_generate_master_id==null){
            $hrm_salary_generate_master_id = 0;
        }else{
            $hrm_salary_generate_master_id = $request->hrm_salary_generate_master_id;
        }



        $data   = DB::select("SELECT
                                    a.id,
                                    concat(c.employee_name,' | ',ifnull(b.employee_code,''),' | ',f.designation_name,' | ',d.depertment_name) as employee_name,
                                    c.id AS employee_id,
                                    c.employee_name as title_emp_name,
                                    d.depertment_name,
                                    f.designation_name,
                                    f.priority,
                                    e.location_name,
                                    a.amount as salary_amount,
                                    a.year_id,
                                    k.month_name,
                                    a.hrm_month_id,
                                    a.day_of_month,
                                    a.total_present,
                                    a.accounts_code,
                                    IF(a.payment_mode = 1, 'Cash', j.short_name) AS payment_mode,
                                    a.account_no,
                                    GROUP_CONCAT(CONCAT(g.amount)
                                        SEPARATOR '<br>') AS amount,
                                    GROUP_CONCAT(CONCAT(IF(g.amount_type = 1, '%', 'TK'))
                                        SEPARATOR '<br>') AS amount_type,
                                    GROUP_CONCAT(CONCAT(h.salary_head)
                                        SEPARATOR '<br>') AS salary_head,
                                    GROUP_CONCAT(CONCAT(IF(g.amount_type = 1,
                                                    (a.amount * g.amount / 100),
                                                    g.amount))
                                        SEPARATOR '<br>') AS salary_head_amount,
                                    GROUP_CONCAT(CONCAT(IF(i.generate_type = 1,
                                                    'Addition',
                                                    'Deduction'))
                                        SEPARATOR '<br>') AS Status,
                                    GROUP_CONCAT(CONCAT(i.group_name)
                                        SEPARATOR '<br>') AS group_name
                                FROM
                                    pay_register a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.hrm_location_id=$location AND a.salary_genarate_type<>0
                                        AND a.year_id=$request->year AND a.hrm_month_id=$request->month
                                        AND a.hrm_salary_generate_master_id= $hrm_salary_generate_master_id
                                        AND a.apply_for=1
                                        $condition
                                        JOIN
                                    hrm_month k ON a.hrm_month_id=k.id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                         JOIN
                                    pay_register_details g ON a.id = g.pay_register_id
                                         JOIN
                                    hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                         JOIN
                                    hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id
                                        LEFT JOIN
                                    hrm_bank j ON a.hrm_bank_id = j.id
                                    GROUP BY a.id , c.employee_name , d.depertment_name , f.designation_name , e.location_name , a.amount , c.id, a.year_id,a.hrm_month_id,a.day_of_month,a.total_present,b.employee_code,a.accounts_code,a.payment_mode,a.account_no,j.short_name,f.priority");





        // return json_encode(array('data' => $data));
        return datatables()->of($data)
            ->addColumn('Link', function ($data) {
                $emp = "<span style='color:#059669'>$data->title_emp_name</span> <span style='font-size:17px;color:#ea580c'>[ Year - $data->year_id and Month - $data->month_name ]</span>";
                return
                ' <a href="'.url('/payregister').'/'.encrypt($data->id).'/edit'.'" data-title="'.$emp.'" data-modal-size="xl" footer-none class="modalLink btn btn-sm block btn-flat">
                    <i class="glyphicon glyphicon-edit"></i> Edit
                </a>


                <form class="dynamicFormSubmit2" data-table-name="#payRegisterDatatable" action="'.route('payregister.destroy', encrypt($data->id)).'" method="POST" style="display:inline;"  >
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <button type="submit" style="background: none; border: none; color: red; cursor: pointer; padding: 0;" >
                        <i class="glyphicon glyphicon-trash"></i> Delete
                    </button>
                </form>

                <a href="'.url('/payregister').'/'.encrypt($data->id).'/print'.'" target="_blank" class="btn btn-sm block btn-flat">
                    <i class="glyphicon glyphicon-print" id="customer-confrimed"></i> Print
                </a>



                ';
            })
            ->rawColumns(['Link','amount','amount_type','salary_head','salary_head_amount','Status','group_name'])
            ->make(true);


                // <form class="dynamicFormSubmit" data-table-name="#payRegisterDatatable" action="'.route('payregister.destroy', encrypt($data->id)).'" method="POST" style="display:inline;"  >
                //     '.csrf_field().'
                //     '.method_field('DELETE').'
                //     <button type="submit" style="background: none; border: none; color: red; cursor: pointer; padding: 0;" >
                //         <i class="glyphicon glyphicon-trash"></i> Delete
                //     </button>
                // </form>

    }

    public function show($id)
    {

        $id = decrypt($id);

        // DO NOT DELETE
        // $master_data=DB::SELECT("SELECT
        //                  c.employee_name,
        //                  d.depertment_name,
        //                  f.designation_name,
        //                  e.location_name,
        //                  a.amount AS salary_amount,
        //                  a.year_id,
        //                  a.hrm_month_id,
        //                  a.day_of_month,
        //                  a.total_present,
        //                  g.month_name,
        //                  h.section_name,
        //                  i.plant_name,
        //                  b.employee_code,
        //                  j.joining_date,
        //                  c.Images,
        //                  ((SELECT SUM(aa.actual_amount) FROM pay_register_details aa JOIN hrm_salary_head bb ON aa.hrm_salary_head_id = bb.id AND aa.pay_register_id =  $id  JOIN hrm_salary_head_group cc ON bb.hrm_salary_head_group_id = cc.id AND cc.generate_type = 1 ) -
        //                  (SELECT SUM(aa.actual_amount) FROM pay_register_details aa JOIN hrm_salary_head bb ON aa.hrm_salary_head_id = bb.id AND aa.pay_register_id =  $id  JOIN hrm_salary_head_group cc ON bb.hrm_salary_head_group_id = cc.id AND cc.generate_type = 2 )) as net_salary
        //              FROM
        //                  pay_register a
        //                      JOIN
        //                  hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
        //                      AND a.id = $id
        //                      JOIN
        //                  hrm_employee c ON b.hrm_employee_id = c.id
        //                      JOIN
        //                  hrm_depertment d ON b.hrm_depertment_id = d.id
        //                      JOIN
        //                  hrm_location e ON b.hrm_location_id = e.id
        //                      JOIN
        //                  hrm_designation f ON b.hrm_designation_id = f.id
        //                      JOIN
        //                  hrm_month g ON a.hrm_month_id = g.id
        //                      JOIN
        //                  hrm_section h ON b.hrm_section_id = h.id
        //                      JOIN
        //                  hrm_plant i ON b.hrm_plant_id = i.id
        //                      JOIN
        //                  hrm_employee_joining j ON j.hrm_employee_id = b.hrm_employee_id");


        //  $addition = DB::SELECT("SELECT
        //                              c.group_name,
        //                              b.salary_head,
        //                              a.amount,
        //                              IF((a.amount_type=1),'%','Tk.') as  amount_type,
        //                              a.actual_amount

        //                          FROM
        //                              pay_register_details a
        //                                  JOIN
        //                              hrm_salary_head b ON a.hrm_salary_head_id = b.id
        //                                  AND a.pay_register_id = $id
        //                                  JOIN
        //                              hrm_salary_head_group c ON b.hrm_salary_head_group_id = c.id AND c.generate_type=1");

        //  $deduction = DB::SELECT("SELECT
        //                              c.group_name,
        //                              b.salary_head,
        //                              a.amount,
        //                              IF((a.amount_type=1),'%','Tk.') as  amount_type,
        //                              a.actual_amount

        //                          FROM
        //                              pay_register_details a
        //                                  JOIN
        //                              hrm_salary_head b ON a.hrm_salary_head_id = b.id
        //                                  AND a.pay_register_id = $id
        //                                  JOIN
        //                              hrm_salary_head_group c ON b.hrm_salary_head_group_id = c.id AND c.generate_type=2");

        //  return view('pay_register.print_preview', compact('master_data','addition','deduction'));
        // END DO NOT DELETE

        $jasper_server = new Client(
            config('configaration.jasperjasper_url'),
            config('configaration.jasper_user'),
            config('configaration.jasper_password')
        );


        $controls = array(
            'company_name'          => config('configaration.company_name'),
            'address'               => config('configaration.company_address'),
            'title'                 => "Pay Register",
            'id'                    => $id,
        );

        $report_path = config('configaration.report_path').'hrm_payregister';
        $exporttype  = "pdf";
        $report      = $jasper_server->reportService()->runReport($report_path, $exporttype,null,null,$controls);

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
            dd("No data found");
        }
    }

    public function additional_deduction_add(Request $request)
    {
        $query = DB::SELECT("select a.* from pay_register a join hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.year_id=$request->years And a.hrm_month_id=$request->month_names and b.hrm_designation_id=$request->designation");

        foreach ($query as $key ){
            $insert_salary_details  = new HrmPayRegisterDetails;
            $insert_salary_details->pay_register_id            = $key->id;
            $insert_salary_details->hrm_salary_head_id         = $request->deduction_head;
            $insert_salary_details->amount                     = $request->deduction_amount;
            $insert_salary_details->amount_type                = 2;
            $insert_salary_details->actual_amount              = $request->deduction_amount;
            $insert_salary_details->save();
        }
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $check_data = DB::select("SELECT id FROM pay_register WHERE id = $id AND salary_genarate_type = 2");

        if (!empty($check_data)) {
            dump('Can not edit.! This salary already generated.!');
            return;
        }

        $data['edit_data'] = DB::SELECT("SELECT
                a.id,
                c.employee_name,
                a.amount as salary_amount,
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
                    (a.amount * g.amount / 100),
                    g.amount) AS salary_head_amount,
                g.actual_amount,
                h.editable

            FROM
                pay_register a
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
                pay_register_details g ON a.id = g.pay_register_id
                   Right JOIN
                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                    JOIN
                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=1 AND h.apply_for=1
        ");


        $data['edit_data_deduction'] = DB::SELECT("SELECT
                a.id,
                c.employee_name,
                a.amount as salary_amount,
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
                    (a.amount * g.amount / 100),
                    g.amount) AS salary_head_amount,
                g.actual_amount,
                h.editable,
                j.hrm_loan_application_id
            FROM
                pay_register a
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
                pay_register_details g ON a.id = g.pay_register_id
                   Right JOIN
                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                    JOIN
                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id
                    LEFT JOIN
                hrm_loan_payregister_details j ON g.id = j.pay_register_details_id
                 WHERE i.generate_type=2  AND h.apply_for=1
        ");

        $data['master_data'] = DB::SELECT("SELECT
                a.id,
                c.employee_name,
                c.id AS employee_id,
                d.depertment_name,
                f.designation_name,
                e.location_name,
                a.amount as salary_amount,
                a.year_id,
                a.hrm_month_id,
                a.total_present,
                a.day_of_month,
                g.month_name,
                b.id as hrm_job_info_id,
                a.account_no,
                a.payment_mode,
                a.by_bank_percent,
                h.id as hrm_bank_id,
                h.bank_name,
                a.hrm_salary_generate_master_id,
                a.note,
                a.accounts_code

            FROM
                pay_register a
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
                hrm_month g ON a.hrm_month_id=g.id
                    LEFT JOIN
                hrm_bank h ON h.id = a.hrm_bank_id
        ")[0];

        $data['bank_name'] = HrmBank::all();

        return view('pay_register.edit_payregister', $data);
    }

    public function employee_wise_salary_process(){
        $user_id = auth()->id();
        $running_month_year = DB::SELECT("SELECT
                                                a.year_id,
                                                a.hrm_month_id,
                                                b.month_name,
                                                c.id AS master_id,
                                                c.declaration_date,
                                                e.id as hrm_location_id,
                                                e.location_name
                                            FROM
                                                `pay_register` a
                                                    JOIN
                                                hrm_month b ON a.hrm_month_id = b.id
                                                    AND a.`salary_genarate_type` <> 0
                                                    AND a.apply_for = 1
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id
                                                    AND d.`users_id`= $user_id
                                                    JOIN
                                                hrm_location e ON d.hrm_location_id = e.id
                                            ORDER BY a.id DESC
                                            LIMIT 0 , 1");


        if (empty($running_month_year)) {
            $running_month_year = DB::SELECT("SELECT
                                                    b.id AS hrm_location_id,
                                                    b.location_name,
                                                    0 AS year_id,
                                                    0 AS hrm_month_id,
                                                    0 AS month_name,
                                                    0 AS master_id,
                                                    0 As declaration_date
                                                FROM
                                                    `user_location` a
                                                        JOIN
                                                    hrm_location b ON a.`hrm_location_id` = b.id
                                                        AND a.`users_id` = $user_id
                                                        AND a.`default_location` = 1");
        }

        return view('pay_register.employee_wise_salary_process', compact('running_month_year'));

    }

    public function employee_wise_search(Request $request){
        $validator = Validator::make($request->all(), [
            'location'              => 'required',
            'year'              => 'required',
            'month_name'        => 'required',
            'salary_master'        => 'required',
            'employee'        => 'required',
        ]);


        if( $validator->fails() ){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ]);
        }

        $hrm_employee_job_info_id = DB::table('hrm_employee_job_info')
        ->where('hrm_employee_id',$request->employee)
        ->where('employee_activity',1)
        ->first()

        ->id ?? null;

        // dd($hrm_employee_job_info_id);

       $exists =  DB::table('pay_register')
        ->where('salary_genarate_type','!=',0)
        ->where('hrm_employee_job_info_id',$hrm_employee_job_info_id)
        ->where('year_id',$request->year)
        ->where('hrm_month_id',$request->month_name)
        ->first();

        // dd($exists);

        if(!isset( $exists)){
            return response()->json([
                'success'   => true,
                'message'    => ''
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'message'    => 'Already salary has this month'
            ]);
        }
        // dd($request->all());


    }

    public function employee_salary_form($id){

        $_data = explode('_',$id);
        $id = $_data[0];
        $data['year_id'] = $_data[1];
        $data['month_id'] = $_data[2];
        $data['location'] = $_data[3];
        $data['month_name'] = DB::table('hrm_month')
                    ->where('id',$_data[2])->first()->month_name;
        $_id = DB::table('hrm_employee_salary as a')
                ->select('a.id','a.hrm_employee_job_info_id')
                ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                ->where('employee_activity',1)
                ->where('b.hrm_employee_id',$id)
                ->first()->id;

                // dd( $_id);

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
                    AND a.id = $_id
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
                    AND a.id = $_id
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
                a.accounts_code,
                a.hrm_employee_job_info_id

            FROM
                hrm_employee_salary a
                    JOIN
                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                    AND a.id = $_id
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
        // dd($data);
            return view('pay_register.employee_salary_create', $data);
    }

    public function salary_create(Request $request){
        $status = false;
        DB::beginTransaction();
        try{
            // dd($request->all());
            $exists = DB::table('hrm_salary_generate_master')
                ->where('year_id',$request->year)
                ->where('hrm_month_id',$request->month_name)
                ->where('hrm_location_id',$request->location_id)
                ->where('valid',1)
                ->first();
            if(!isset( $exists)){
                return response()->json([
                    'status' => false,
                    'message' => 'This month salary generate not yet',
                    'error' => $error ?? '',
                ]);
            }
            // dd( $exists);

            $lock =  DB::table('pay_register')
            ->where('salary_genarate_type','=',2)
            ->where('year_id',$request->year)
            ->where('hrm_month_id',$request->month_name)
            ->first();

            if(isset($lock)){
                return response()->json([
                    'status' => false,
                    'message' => 'This month salary locked',
                    'error' => $error ?? '',
                ]);
            }



            $pay_exists =  DB::table('pay_register')
            ->where('salary_genarate_type','!=',0)
            ->where('hrm_employee_job_info_id',$request->hrm_employee_job_info_id)
            ->where('year_id',$request->year)
            ->where('hrm_month_id',$request->month_name)
            ->first();

            // dd( $pay_exists);


            if(isset( $pay_exists)){
                return response()->json([
                    'status'   => false,
                    'message' => 'Already salay has this month',
                    'error' => $error ?? '',
                ]);
            }

            $date_of_month  = $request->year.'-'.$request->month_name.'-01';
            $lastDay = DB::select("SELECT RIGHT(LAST_DAY('$date_of_month'), 2) as day")[0]->day;
            $lastdate       = date("Y-m-t", strtotime($date_of_month));
                // dd($request->hrm_employee_job_info_id);
                $employee_job_info = DB::select(" SELECT b.id as hrm_employee_salary_id,b.salary_amount,b.hrm_bank_id, a.*
                        FROM hrm_employee_job_info AS a
                        JOIN hrm_employee_salary b
                            ON a.id = b.hrm_employee_job_info_id
                            AND a.employee_activity = 1
                              AND a.hrm_location_id = {$request->location_id}
                        JOIN hrm_employee_joining d
                            ON a.hrm_employee_id = d.hrm_employee_id
                            -- WHERE d.joining_date < '$lastdate'
                           AND a.hrm_employee_id = {$request->employee_name}
                    ")[0];
                        // dd($employee_job_info);
            ;
            // year_id,hrm_month_id,hrm_employee_job_info_id,
            //             day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_salary_generate_master_id,hrm_location_id,accounts_code


           $last_id =  DB::table('pay_register')
            ->insertGetId([
                'year_id' => $request->year,
                'hrm_month_id' => $request->month_name,
                'hrm_employee_job_info_id' => $request->hrm_employee_job_info_id,
                'day_of_month' => $lastDay,
                'total_present' => $request->total_present,
                'salary_genarate_type' => 1,
                'users_id' => auth()->id(),
                'hrm_employee_salary_id' => $employee_job_info->hrm_employee_salary_id ,
                'amount' => $request->salary_amount ,
                'apply_for' => 1 ,
                'payment_mode' => $request->payment_mode ,
                'account_no' => $request->account_no ,
                'by_bank_percent' => $request->by_bank_percent ,
                'hrm_bank_id' => $employee_job_info->hrm_bank_id ,
                'hrm_salary_generate_master_id' => $exists->id ,
                'hrm_location_id' => $request->location_id ,
                'accounts_code' => $request->accounts_code ,
                'note' => $request->note ,
            ]);

            $count_row_addition  = count($request->amountaddition);
            $count_row_deduction = count($request->amountdeduction);
            $user_id             = auth()->id();
            $user_name           = auth()->user()->name;

            for($r = 0; $r <$count_row_addition; $r++) {

                $check= $request->addition_actual_amount[$r];
                // addition side
                if($check>0){
                    $insert_salary_details  = new HrmPayRegisterDetails;
                    $insert_salary_details->pay_register_id            = $last_id;
                    $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
                    $insert_salary_details->amount                     = $request->amountaddition[$r];
                    $insert_salary_details->amount_type                = $request->typeaddition[$r];
                    $insert_salary_details->actual_amount              = $request->addition_actual_amount[$r];
                    $insert_salary_details->save();

                    $this->recordActivity(
                         1,
                         'Created Pay Register Addition',
                         $insert_salary_details,
                         $insert_salary_details->id,
                         'pay_register_details'
                    );
                }
            }

            for($r = 0; $r <$count_row_deduction; $r++) {

                $check= $request->deduction_actual_amount[$r];

                if($check>0){

                    $insert_salary_details  = new HrmPayRegisterDetails;
                    $insert_salary_details->pay_register_id            = $last_id;
                    $insert_salary_details->hrm_salary_head_id         = $request->deduction_id[$r];
                    $insert_salary_details->amount                     = $request->amountdeduction[$r];
                    $insert_salary_details->amount_type                = $request->typededuction[$r];
                    $insert_salary_details->actual_amount              = $request->deduction_actual_amount[$r];
                    $insert_salary_details->save();


                    $this->recordActivity(
                         1,
                         'Created Pay Register Deduction',
                         $insert_salary_details,
                         $insert_salary_details->id,
                         'pay_register_details'
                    );
                }
            }



            DB::commit();
            $status = true;
            $message = 'Salary generate successfully';


        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $message = $e->getMessage();
        }


        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }


    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_job_info_id'     => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            $count_row_addition  = count($request->amountaddition);
            $count_row_deduction = count($request->amountdeduction);
            $user_id             = auth()->id();
            $user_name           = auth()->user()->name;


            DB::beginTransaction();
            try{

                // $find_data = HrmPayRegister;
                // $find_data->salary_genarate_type = 0;
                // $find_data->save();

                // dd($find_data);

                $deleted_data_store = new HrmDeletedDataLog;
                $deleted_data_store->user_name       = $user_name;
                $deleted_data_store->delete_from     = 'Payregister Data Delete';
                $deleted_data_store->users_id        = $user_id;
                $deleted_data_store->master_table_id = $id;
                $deleted_data_store->save();



                $insert = HrmPayRegister::find($id);
                // $insert->year_id                  = $find_data->year_id;
                // $insert->hrm_month_id             = $find_data->hrm_month_id;
                // $insert->hrm_employee_job_info_id = $find_data->hrm_employee_job_info_id;

                $insert->day_of_month             = $insert->day_of_month;
                // $insert->total_present            = $insert->total_present;
                $insert->salary_genarate_type     = 1;
                $insert->users_id                 = $user_id;
                // $insert->hrm_employee_salary_id   = $find_data->hrm_employee_salary_id;
                // $insert->amount                   = $insert->amount;
                $insert->apply_for                = 1;
                $insert->payment_mode             = $request->payment_mode;
                $insert->account_no               = $request->account_no;
                $insert->hrm_bank_id              = $request->hrm_bank_id;
                $insert->by_bank_percent          = $request->by_bank_percent;
                $insert->hrm_salary_generate_master_id  = $request->hrm_salary_generate_master_id;
                // $insert->hrm_location_id          = $find_data->hrm_location_id;
                $insert->note                     = $request->note;
                $insert->accounts_code            = $request->accounts_code;
                $insert->save();

                DB::UPDATE("UPDATE hrm_loan_ledger
                            JOIN(SELECT
                                        a.id
                                    FROM
                                        hrm_loan_ledger a
                                            JOIN
                                        hrm_loan_payregister_details b ON a.id = b.hrm_loan_ledger_id
                                            JOIN
                                        pay_register_details c ON b.pay_register_details_id = c.id
                                            JOIN
                                        pay_register d ON c.pay_register_id = d.id AND d.id= $id
                                            ) aa ON
                            hrm_loan_ledger.id = aa.id
                            SET hrm_loan_ledger.valid = 0,hrm_loan_ledger.users_id = $user_id,hrm_loan_ledger.narration='Deleted From PayRegister' ");


                DB::Delete("DELETE FROM hrm_loan_payregister_details WHERE pay_register_details_id in (SELECT id from pay_register_details WHERE pay_register_id=$id)");

                // DB::DELETE("DELETE FROM hrm_loan_ledger WHERE id in(SELECT
                //                         a.id
                //                     FROM
                //                         hrm_loan_ledger a
                //                             JOIN
                //                         hrm_loan_payregister_details b ON a.id = b.hrm_loan_ledger_id
                //                             JOIN
                //                         pay_register_details c ON b.pay_register_details_id = c.id
                //                             JOIN
                //                         pay_register d ON c.pay_register_id = d.id AND d.id= $id) ");

                $existing_details = DB::select("SELECT g.id, g.hrm_salary_head_id, g.actual_amount, g.amount, g.amount_type
                                                FROM pay_register_details g
                                                JOIN hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                                WHERE g.pay_register_id = $id AND h.apply_for = 1");

                $existing_map = [];
                foreach ($existing_details as $ed) {
                    $existing_map[$ed->hrm_salary_head_id] = $ed;
                }

                DB::Delete("DELETE FROM pay_register_details WHERE pay_register_id=$id  and hrm_salary_head_id in (SELECT id from hrm_salary_head WHERE apply_for=1)");


                for($r = 0; $r <$count_row_addition; $r++) {

                    $addition_editable = isset($existing_map[$request->addition_id[$r]]) && HrmSalaryHead::find($request->addition_id[$r])->editable;
                    $addition_actual = $addition_editable
                        ? $request->addition_actual_amount[$r]
                        : ($existing_map[$request->addition_id[$r]]->actual_amount ?? $request->addition_actual_amount[$r]);

                    $check= $addition_actual;
                    // addition side
                    if($check>0){
                        $insert_salary_details  = new HrmPayRegisterDetails;
                        $insert_salary_details->pay_register_id            = $insert ->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
                        $insert_salary_details->amount                     = $request->amountaddition[$r];
                        $insert_salary_details->amount_type                = $request->typeaddition[$r];
                        $insert_salary_details->actual_amount              = $addition_actual;
                        $insert_salary_details->save();

                        $this->recordActivity(
                             1,
                             'Updated Pay Register Addition',
                             $insert_salary_details,
                             $insert_salary_details->id,
                             'pay_register_details'
                        );
                    }
                }


                for($r = 0; $r <$count_row_deduction; $r++) {

                    $deduction_editable = isset($existing_map[$request->deduction_id[$r]]) && HrmSalaryHead::find($request->deduction_id[$r])->editable;
                    $deduction_actual = $deduction_editable
                        ? $request->deduction_actual_amount[$r]
                        : ($existing_map[$request->deduction_id[$r]]->actual_amount ?? $request->deduction_actual_amount[$r]);

                    $check= $deduction_actual;

                    if($check>0){

                        $insert_salary_details  = new HrmPayRegisterDetails;
                        $insert_salary_details->pay_register_id            = $insert->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->deduction_id[$r];
                        $insert_salary_details->amount                     = $request->amountdeduction[$r];
                        $insert_salary_details->amount_type                = $request->typededuction[$r];
                        $insert_salary_details->actual_amount              = $deduction_actual;
                        $insert_salary_details->save();

                        // if($request->deduction_id[$r] == )

                        if($request->hrm_loan_application_id[$r] > 0){

                            $insert_loan_ledger  = new HrmLoanLedger;
                            $insert_loan_ledger->hrm_loan_application_id = $request->hrm_loan_application_id[$r];
                            $insert_loan_ledger->debit                   = $deduction_actual;
                            $insert_loan_ledger->credit                  = 0;
                            $insert_loan_ledger->hrm_month_id            = $insert->hrm_month_id;
                            $insert_loan_ledger->year_id                 = $insert->year_id;
                            $insert_loan_ledger->valid                   = 1;
                            $insert_loan_ledger->narration               = 'Loan Adjusted';
                            $insert_loan_ledger->interest_amount         = 0;
                            $insert_loan_ledger->users_id                = $user_id;
                            $insert_loan_ledger->entry_status            = 1;
                            $insert_loan_ledger->save();

                            $insert_tag  = new HrmLoanPayRegisterDetails;
                            $insert_tag->hrm_loan_application_id     = $request->hrm_loan_application_id[$r];
                            $insert_tag->pay_register_details_id     = $insert_salary_details->id;
                            $insert_tag->hrm_loan_ledger_id          = $insert_loan_ledger->id;
                            $insert_tag->save();
                        }

                        $this->recordActivity(
                             1,
                             'Updated Pay Register Deduction',
                             $insert_salary_details,
                             $insert_salary_details->id,
                             'pay_register_details'
                        );
                    }
                }

                // // Loan Amount Change er Effect Loan Ledger a Porbe

                DB::commit();

                $this->recordActivity(
                     1,
                     'Updated Pay Register',
                     $insert->getChanges(),
                     $insert->id,
                     'pay_register'
                );

                $status = true;
                $message = 'Pay Register has been updated..!';
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

    public function destroy($id)
    {
        $status = false;
        $id = decrypt($id);
        DB::beginTransaction();
        try{
            $pay_register = DB::table('pay_register as a')
                            ->select('a.*','b.hrm_employee_id')
                            ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                            ->where('a.id',$id)
                            ->first();

                            // dd($id);

           $hrm_loan_ledger_ids = DB::table('hrm_loan_ledger as a')
           ->select('a.id')
            ->where('a.year_id',$pay_register->year_id)
            ->where('a.hrm_month_id',$pay_register->hrm_month_id)
            ->where('a.valid',1)
            ->join('hrm_loan_application as b','b.id','=','a.hrm_loan_application_id')
            ->where('b.hrm_employee_id',$pay_register->hrm_employee_id)
            ->get()
            ->pluck('id')
            ->toArray();
            // dd($hrm_loan_ledger_ids);

            DB::table('hrm_loan_payregister_details')
            ->whereIn('hrm_loan_ledger_id', $hrm_loan_ledger_ids)
            ->delete();



             DB::table('hrm_loan_ledger as a')
            ->where('a.year_id',$pay_register->year_id)
            ->where('a.hrm_month_id',$pay_register->hrm_month_id)
            ->join('hrm_loan_application as b','b.id','=','a.hrm_loan_application_id')
            ->where('hrm_employee_id',$pay_register->hrm_employee_id)
            ->update([
                'a.valid' => 0
            ]);


            DB::update("UPDATE hrm_salary_deduction SET is_active = 1
            WHERE id in(SELECT id FROM (SELECT
            a.id
                FROM
            hrm_salary_deduction a
                JOIN
            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                AND a.is_active = 2
                AND a.hrm_employee_id = $pay_register->hrm_employee_id
                AND YEAR(a.month_from)=$pay_register->year_id
                AND MONTH(a.month_from) = $pay_register->hrm_month_id
                AND b.hrm_location_id = $pay_register->hrm_location_id) a )");

             DB::table('pay_register')->where('id', $id)
            ->update([
                'salary_genarate_type' => 0
            ]);


            DB::table('pay_register_details')
            ->where('pay_register_id',$id)
            ->delete();


            DB::commit();


            $this->recordActivity(
                1,
                'Deleted Pay Register',
                $pay_register,
                $pay_register->id,
                'pay_register'
            );

            $status = true;
            $message = 'Pay Register data has been Deleted..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);

    }

    public function payregisterDetails(){



        $date = date("Y-m");
        $monthYear = explode('-', $date);
        $year = (int) $monthYear[0];
        $month = (int) $monthYear[1];


        $exists = DB::select("SELECT * FROM pay_register WHERE year_id = $year AND hrm_month_id = $month");
        $user_id = Auth::user()->id;
        $filters = " AND a.hrm_location_id in (SELECT a.hrm_location_id FROM user_location a JOIN users b ON a.users_id = b.id AND b.id =$user_id )";

        $dataset = [];

        if($exists){
            $dataset = DB::select("CALL get_register_filter($year,$month,'$filters')");
        }

        return view('employee_salary.dynamic_list') -> with('dataset',  $dataset);
    }

    public function searchPayregisterDetails(Request $request){

        (int)$year = 0;
        (int)$month = 0;
        if($request->month_year){
            $monthYear = explode('-', $request->month_year);
            $year = (int) $monthYear[0];
            $month = (int) $monthYear[1];
        }

        $exists = DB::select("SELECT * FROM pay_register WHERE year_id = $year AND hrm_month_id = $month");

        $dataset = [];

        $employee_id = $request->employee_id;
        $dept_id = $request->department_id;
        $location_id = $request->location_id;
        $category_id = $request->category_id;
        $section_id = $request->section_id;
        $payment_mode = $request->payment_mode;
        $bank_id = $request->bank_id;

        // Constructing the dynamic condition
        $filters = '';
        if ($employee_id){
            $filters .= " AND a.hrm_employee_id = $employee_id";
        }

        if ($dept_id) {
            $filters .= " AND a.hrm_depertment_id = $dept_id";
        }
        if ($location_id) {
            $filters .= " AND a.hrm_location_id = $location_id";
        }
        if ($category_id) {
            $filters .= " AND a.hrm_category_id = $category_id";
        }
        if ($section_id) {
            $filters .= " AND a.hrm_section_id = $section_id";
        }

        if ($payment_mode) {
            $filters .= " AND a.payment_mode = $payment_mode";
        }

        if ($bank_id) {
            $filters .= " AND a.hrm_bank_id = $bank_id";
        }



        if($exists){
            $dataset = DB::select("CALL get_register_filter($year,$month, '$filters')");
        }

        return view('employee_salary.__dynamic_list') -> with('dataset',  $dataset);
    }



}
