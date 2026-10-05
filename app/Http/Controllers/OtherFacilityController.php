<?php

namespace App\Http\Controllers;

use Config;
use Session;
use App\User;
use Redirect;
use Response;
use DataTables;
use App\Models\HrmBank;
use App\Models\HrmMonth;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeJobInfo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\HrmFringeBenefitsConfig;
use App\Models\HrmEmployeeSalaryDetails;
use Illuminate\Support\Facades\Validator;
use App\Models\HrmFringeBenefitsConfigDetails;

class OtherFacilityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('permission:OtherFacility,OtherFacilityProcess');
        // $this->middleware('permission:OtherFacilityProcess');
    }

    public function index()
    {
        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear = date('Y');

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('other_facility.otherfacility_list')
              -> with('default_user_location',  $default_user_location)
              -> with('cmonth',  $cmonth)
              -> with('cyear',  $cyear) ;
    }

    public function otherfacilityconfiq()
    {

        $user_id = Auth::user()->id;
        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");
        $designation = DB::select("SELECT b.id,b.designation_name FROM hrm_fringe_benefits_config a JOIN hrm_designation b ON b.id=a.hrm_designation_id");

          return view('other_facility.otherfacility_config_list')
              -> with('location',  $location)
              -> with('designation',  $designation) ;

    }

    public function otherfacilityconfiqcreate()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('other_facility.otherfacility_config_create')
            -> with('default_user_location',  $default_user_location) ;
    }

    public function otherfacilityconfiqsubmit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'salary_type'          => 'required',
            'designation'         => 'required|unique:hrm_fringe_benefits_config,hrm_designation_id',

        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'This Designation Salary Grade Already Exist!');
            return redirect('otherfacilityconfiq')
                        ->withErrors($validator)
                        ->withInput();
        }

        $count_row_addition  = count($request->amountaddition);
        $count_row_deduction = count($request->amountdeduction);


        DB::beginTransaction();
                    try{

                            //insert_master
                            $insert_master= new HrmFringeBenefitsConfig;
                            $insert_master->hrm_designation_id   =$request->designation;
                            $insert_master->users_id             =Auth::user()->id;
                            $insert_master->save();

                            //insert_Details
                            // foreach ($request->amountaddition as $keys ) {
                            for($r = 0; $r <$count_row_addition; $r++) {
                                $insert_details     = new HrmFringeBenefitsConfigDetails;
                                $insert_details->hrm_fringe_benefits_config_id  = $insert_master->id;
                                $insert_details->hrm_salary_head_id             = $request->addition_id[$r];
                                if ($request->amountaddition[$r]>0){
                                    $insert_details->amount                     = $request->amountaddition[$r];
                                    $insert_details->amount_type                = $request->typeaddition[$r];
                                }else{
                                    $insert_details->amount                     = 0;
                                    $insert_details->amount_type                = $request->typeaddition[$r];
                                }

                                $insert_details->save();

                                $this->recordActivity(
                                     1,
                                     'Created Designation Wise Fringe Benefit',
                                     $insert_details,
                                     $insert_details->id,
                                     'hrm_fringe_benefits_config_details'
                                );
                            }


                            for($r = 0; $r <$count_row_deduction; $r++) {
                                $insert_details_data     = new HrmFringeBenefitsConfigDetails;
                                $insert_details_data->hrm_fringe_benefits_config_id = $insert_master->id;
                                $insert_details_data->hrm_salary_head_id            = $request->deduction_id[$r];
                                if ($request->amountdeduction[$r]>0){
                                    $insert_details_data->amount                    = $request->amountdeduction[$r];
                                    $insert_details_data->amount_type               = $request->typededuction[$r];
                                }else{
                                    $insert_details_data->amount                    = 0;
                                    $insert_details_data->amount_type               = $request->typededuction[$r];
                                }

                                $insert_details_data->save();

                                $this->recordActivity(
                                     1,
                                     'Created Designation Wise Fringe Benefit',
                                     $insert_details_data,
                                     $insert_details_data->id,
                                     'hrm_fringe_benefits_config_details'
                                );
                            }



                DB::commit();

                $this->recordActivity(
                     1,
                     'Created Designation Wise Fringe Benefit',
                     $insert_master,
                     $insert_master->id,
                     'hrm_fringe_benefits_config'
                );

        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }


       $request->session()->flash('alert-success', 'data has been successfully added!');
       return Redirect::to('otherfacilityconfiq');
    }

    public function otherfacilityconfiqeditsubmit(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            // 'salary_type'          => 'required',
            'designation'         => 'required',

        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'This Designation Salary Grade Already Exist!');
            return redirect('otherfacilityconfiq')
                        ->withErrors($validator)
                        ->withInput();
        }

        $id =$request->id;
        DB::table('hrm_fringe_benefits_config_details')->where('hrm_fringe_benefits_config_id', '=', $id)->delete();
        DB::table('hrm_fringe_benefits_config')->where('id', '=', $id)->delete();

        $count_row_addition  = count($request->amountaddition);
        $count_row_deduction = count($request->amountdeduction);


        DB::beginTransaction();
                    try{

                            //insert_master
                            $insert_master= new HrmFringeBenefitsConfig;
                            $insert_master->hrm_designation_id   =$request->designation;
                            $insert_master->users_id             =Auth::user()->id;
                            $insert_master->save();

                            //insert_Details
                            for($r = 0; $r <$count_row_addition; $r++) {
                                $insert_details     = new HrmFringeBenefitsConfigDetails;
                                $insert_details->hrm_fringe_benefits_config_id  = $insert_master->id;
                                $insert_details->hrm_salary_head_id             = $request->addition_id[$r];
                                if ($request->amountaddition[$r]>0){
                                    $insert_details->amount                     = $request->amountaddition[$r];
                                    $insert_details->amount_type                = $request->typeaddition[$r];
                                }else{
                                    $insert_details->amount                     = 0;
                                    $insert_details->amount_type                = $request->typeaddition[$r];
                                }

                                $insert_details->save();

                                $this->recordActivity(
                                     1,
                                     'Updated Designation Wise Fringe Benefit Addition',
                                     $insert_details,
                                     $insert_details->id,
                                     'hrm_fringe_benefits_config_details'
                                );
                            }


                            for($r = 0; $r <$count_row_deduction; $r++) {
                                $insert_details_data     = new HrmFringeBenefitsConfigDetails;
                                $insert_details_data->hrm_fringe_benefits_config_id = $insert_master->id;
                                $insert_details_data->hrm_salary_head_id            = $request->deduction_id[$r];
                                if ($request->amountdeduction[$r]>0){
                                    $insert_details_data->amount                    = $request->amountdeduction[$r];
                                    $insert_details_data->amount_type               = $request->typededuction[$r];
                                }else{
                                    $insert_details_data->amount                    = 0;
                                    $insert_details_data->amount_type               = $request->typededuction[$r];
                                }

                                $insert_details_data->save();

                                $this->recordActivity(
                                     1,
                                     'Updated Designation Wise Fringe Benefit Deduction',
                                     $insert_details_data,
                                     $insert_details_data->id,
                                     'hrm_fringe_benefits_config_details'
                                );
                            }



                         DB::commit();

                         $this->recordActivity(
                             1,
                             'Updated Designation Wise Fringe Benefit',
                             $insert_master,
                             $insert_master->id,
                             'hrm_fringe_benefits_config'
                        );



        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }


       $request->session()->flash('alert-success', 'data has been successfully added!');
       return Redirect::to('otherfacilityconfiq');



    }

    public function otherfacilityconfiglistdata(Request $request)
    {

        // $user_id    = Auth::user()->id;


        $data       = DB::select("SELECT
                                    a.id,
                                    c.designation_name,
                                    GROUP_CONCAT(CONCAT(b.amount)
                                        SEPARATOR '<br>') AS amount,
                                    GROUP_CONCAT(CONCAT(IF(b.amount_type = 1, '%', 'TK'))
                                        SEPARATOR '<br>') AS amount_type,
                                    GROUP_CONCAT(CONCAT(d.salary_head)
                                        SEPARATOR '<br>') AS salary_head,
                                    GROUP_CONCAT(CONCAT(IF(e.generate_type = 1,
                                                    'Addition',
                                                    'Deduction'))
                                    SEPARATOR '<br>') AS Status,
                                    GROUP_CONCAT(CONCAT(e.group_name)
                                        SEPARATOR '<br>') AS group_name
                                FROM
                                    hrm_fringe_benefits_config a
                                        JOIN
                                    hrm_fringe_benefits_config_details b ON a.id = b.hrm_fringe_benefits_config_id
                                        JOIN
                                    hrm_designation c ON a.hrm_designation_id = c.id
                                        JOIN
                                    hrm_salary_head d ON b.hrm_salary_head_id = d.id
                                        JOIN
                                    hrm_salary_head_group e ON e.id = d.hrm_salary_head_group_id
                                    GROUP BY a.id,c.designation_name");

        return datatables()->of($data)
        ->addColumn('Link', function ($data) {
           return
           ' <a href="'. url('/otherfacilityconfiq') . '/' .
           ($data->id) .
           '/edit' .'"' .
           'class="btn  btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
         })
        ->editColumn('id', '{{$id}}')
        ->setRowId('id')
        ->rawColumns(['Link','Status','salary_head','amount','amount_type','group_name'])
        ->make(true);
    }

    public function otherfacilitylistdata(Request $request)
    {
        $condition ='';

        if($request->location==null){

         }else{
            $condition = ' AND b.hrm_location_id ='.$request->location;
        }

        if(!empty($request->employeestatus)){

            $condition = $condition.' AND b.hrm_employment_status_id= '.$request->employeestatus;
        }


        $date_of_month  = $request->year.'-'.$request->month.'-01';
        $year_month     = $request->year.'-'.$request->month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = Auth::user()->id;
        $user_id        = Auth::user()->id;


        $data       = DB::select("SELECT
                                    a.id,
                                    concat(c.employee_name,' | ',b.employee_code,' | ',f.designation_name,' | ',d.depertment_name) as employee_name,
                                    c.id as employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    e.location_name,
                                    a.salary_amount,
                                    a.accounts_code,
                                    IF(a.payment_mode_fb = 1, 'Cash', k.short_name) AS payment_mode,
                                    a.account_no_fb as account_no,
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
                                    SEPARATOR '<br>') AS Status,
                                    GROUP_CONCAT(CONCAT(i.group_name)
                                        SEPARATOR '<br>') AS group_name
                                FROM
                                    hrm_employee_salary a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND b.id in
                                        (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        -- WHERE end_date BETWEEN '$date_of_month' AND '$lastdate'  AND end_date IS NOT NULL
                                        WHERE
                                         '$lastdate' BETWEEN start_date AND end_date
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$date_of_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        )
                                        $condition
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
                                         JOIN
                                    hrm_salary_head h ON g.hrm_salary_head_id = h.id  AND h.apply_for=2
                                         JOIN
                                    hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id
                                        JOIN
                                    user_location j ON b.hrm_location_id = j.hrm_location_id AND j.users_id = $user_id
                                       LEFT JOIN
                                    hrm_bank k ON a.hrm_bank_id_fb = k.id

                                    GROUP BY a.id,c.employee_name,d.depertment_name,f.designation_name,e.location_name,a.salary_amount,c.id,b.employee_code,a.accounts_code,a.payment_mode_fb,a.account_no_fb,k.short_name");

        // return json_encode(array('data' => $data));
        return datatables()->of($data)
            ->addColumn('Link', function ($data) {
                return
                '<a href="'.url('/otherfacility').'/'.Crypt::encrypt($data->id).'/edit" class="modalLink btn btn-sm block btn-flat" data-title="Edit Employee Fringe Benefit" data-modal-size="xl" modal-center footer-none>
                    <i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit
                </a>

                <a onclick="return confirm(\'Do you want to Finally Delete?\');" href="'. url('/otherfacility').'/'.Crypt::encrypt($data->id).'/cancel" class="btn  btn-sm block btn-flat">
                    <i class="glyphicon glyphicon-trust" id="customer-confrimed"></i>
                    Delete
                </a>';
            })
            ->rawColumns(['Link','amount','amount_type','salary_head','salary_head_amount','Status','group_name'])
            ->make(true);
    }

    public function otherfacilityconfiqstore(Request $request)
    {
        //dd($request->all());

        $list      = implode(',',$request->employeestatus);
        $condition = " AND a.hrm_employment_status_id in ($list) ";


        if (isset($request->employee_name)){
            $condition  =  $condition . " AND a.hrm_employee_id = ".$request->employee_name;
        }

        if($request->location==0){

        }else{

            if($request->location=='999'){
                $condition =$condition.' AND bb.location_type=3 ';
            }else{
                $condition =$condition.' AND a.hrm_location_id='.$request->location;

            }
        }


        if ($request->month_name>9){
            $month = $request->month_name;
        }else{
            $month = '0'.$request->month_name;
        }

        $date_of_month  = $request->year.'-'.$month.'-01';
        $year_month     = $request->year.'-'.$month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = Auth::user()->id;


        if($request->designation==0){
        }else{
            $condition = $condition.' AND a.hrm_designation_id='.$request->designation;
        }


        $query= DB::DELETE("DELETE FROM hrm_employee_salary_details where id in(
                                SELECT id FROM
                                (SELECT
                                    c.id
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id
                                    AND a.id in

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
                                    )

                                        JOIN
                                    hrm_location bb ON a.hrm_location_id=bb.id
                                    $condition
                                        JOIN
                                    hrm_employee_salary_details c ON b.id = c.hrm_employee_salary_id
                                        JOIN
                                    hrm_salary_head d ON c.hrm_salary_head_id=d.id and d.apply_for=2) aa ) ");


               DB::insert("INSERT INTO hrm_employee_salary_details(hrm_salary_head_id,amount,amount_type,actual_amount,hrm_employee_salary_id)
                    SELECT
                        d.hrm_salary_head_id,d.amount,d.amount_type,d.amount as actual_amount,b.id as hrm_employee_salary_id
                    FROM
                        hrm_employee_job_info a
                            JOIN
                        hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id
                        AND a.id in
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
                        )
                        -- AND a.hrm_employment_status_id NOT IN (1)
                            JOIN
                        hrm_location bb ON a.hrm_location_id = bb.id
                         $condition
                            JOIN
                        hrm_fringe_benefits_config c ON a.hrm_designation_id = c.hrm_designation_id
                            JOIN
                        hrm_fringe_benefits_config_details d ON c.id = d.hrm_fringe_benefits_config_id");

                $this->recordActivity(
                      1,
                     'Fringe Benefits Config Apply',
                      null,
                      null,
                      $condition,
                );

          $request->session()->flash('alert-success', 'Successfully Generated!');
          return Redirect::to('otherfacilityconfiq');

    }

    public function otherfacilityconfiqedit($id)
    {

        $edit_data = DB::select("SELECT
                                        a.id,
                                        c.designation_name,
                                        c.id AS designation_id,
                                        b.amount,
                                        -- CONCAT(IF(b.amount_type = 1, '%', 'TK')) AS amount_type,
                                        b.amount_type,
                                        d.salary_head,
                                        d.id AS salary_head_id,
                                        e.id AS group_id,
                                        e.group_name,
                                        CONCAT(IF(e.generate_type = 1,
                                                    'Addition',
                                                    'Deduction')) AS Status,
                                        e.generate_type
                                    FROM
                                        hrm_fringe_benefits_config a
                                            JOIN
                                        hrm_fringe_benefits_config_details b ON a.id = b.hrm_fringe_benefits_config_id
                                            AND a.id = $id
                                            JOIN
                                        hrm_designation c ON a.hrm_designation_id = c.id
                                            JOIN
                                        hrm_salary_head d ON b.hrm_salary_head_id = d.id
                                            JOIN
                                        hrm_salary_head_group e ON e.id = d.hrm_salary_head_group_id
                                        AND e.generate_type = 1
                                    GROUP BY a.id , c.designation_name , c.id , b.amount , b.amount_type , d.salary_head , e.group_name , e.generate_type , d.id , e.id");

        $edit_data_deduction = DB::select("SELECT
                                        a.id,
                                        c.designation_name,
                                        c.id AS designation_id,
                                        b.amount,
                                        -- CONCAT(IF(b.amount_type = 1, '%', 'TK')) AS amount_type,
                                        b.amount_type,
                                        d.salary_head,
                                        d.id AS salary_head_id,
                                        e.id AS group_id,
                                        e.group_name,
                                        CONCAT(IF(e.generate_type = 1,
                                                    'Addition',
                                                    'Deduction')) AS Status,
                                        e.generate_type
                                    FROM
                                        hrm_fringe_benefits_config a
                                            JOIN
                                        hrm_fringe_benefits_config_details b ON a.id = b.hrm_fringe_benefits_config_id
                                            AND a.id = $id
                                            JOIN
                                        hrm_designation c ON a.hrm_designation_id = c.id
                                            JOIN
                                        hrm_salary_head d ON b.hrm_salary_head_id = d.id
                                            JOIN
                                        hrm_salary_head_group e ON e.id = d.hrm_salary_head_group_id
                                        AND e.generate_type = 2
                                    GROUP BY a.id , c.designation_name , c.id , b.amount , b.amount_type , d.salary_head , e.group_name , e.generate_type , d.id , e.id");


                                  return view('other_facility.edit_otherfacility_config')
                                       ->with('edit_data',$edit_data)
                                       ->with('edit_data_deduction',$edit_data_deduction);
    }

    public function cancel($id)
    {

        // dd("okk");

        $id = Crypt::decrypt($id);

        if(!empty($id)) {
            DB::Delete("DELETE FROM hrm_employee_salary_details WHERE hrm_employee_salary_id=$id and hrm_salary_head_id in (SELECT id from hrm_salary_head WHERE apply_for=2)");
        }

        $this->recordActivity(
             1,
             'Deleted Employee Fringe Benefits',
             null,
             $id,
             'hrm_employee_salary_details'
        );

        return back()->with('success','Insert Record successfully.');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
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
                                g.actual_amount
                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
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
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=1 AND h.apply_for=2");


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
                                g.actual_amount
                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
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
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=2 AND h.is_delete=1  AND h.apply_for=2");

        $data['master_data'] = DB::SELECT("SELECT
                                a.id,
                                c.employee_name,
                                c.id AS employee_id,
                                d.depertment_name,
                                f.designation_name,
                                e.location_name,
                                a.salary_amount,
                                a.account_no_fb as account_no,
                                a.payment_mode_fb as payment_mode,
                                a.by_bank_percent,
                                g.id as hrm_bank_id,
                                g.bank_name

                            FROM
                                hrm_employee_salary a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
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
                                hrm_bank g ON g.id=a.hrm_bank_id_fb ");

        $data['bank_name'] = HrmBank::all();

        return view('other_facility.edit_otherfacility', $data);
    }

    public function update(Request $request, $id)
    {

        $status = false;

        $validator = Validator::make($request->all(), [
            // 'salary_amount'              => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            $count_row_deduction = count($request->amountdeduction);
            $count_row_addition  = count($request->amountaddition);

            DB::Delete("DELETE FROM hrm_employee_salary_details WHERE hrm_employee_salary_id=$id and hrm_salary_head_id in (SELECT id from hrm_salary_head WHERE apply_for=2)");

            DB::beginTransaction();
            try{
                $insert_salary_master  = HrmEmployeeSalary::find($id);
                $insert_salary_master->account_no_fb     = $request->account_no;
                $insert_salary_master->payment_mode_fb   = $request->payment_mode;
                $insert_salary_master->hrm_bank_id_fb    = $request->bank_name;
                $insert_salary_master->users_id          = Auth::user()->id;
                $insert_salary_master->save();


                for($r = 0; $r <$count_row_addition; $r++) {
                 $check= $request->addition_actual_amount[$r];

                  if($check>0){

                    $insert_salary_details  = new HrmEmployeeSalaryDetails;
                    $insert_salary_details->hrm_employee_salary_id     = $id;
                    $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
                    $insert_salary_details->amount                     = $request->amountaddition[$r];
                    $insert_salary_details->amount_type                = $request->typeaddition[$r];
                    $insert_salary_details->actual_amount              = $request->addition_actual_amount[$r];
                    $insert_salary_details->save();

                    $this->recordActivity(
                         1,
                         'Updated Employee Fringe Benefit Addition',
                         $insert_salary_details,
                         $insert_salary_details->id,
                         'hrm_employee_salary_details'
                    );

                   }
                }

                for($r = 0; $r <$count_row_deduction; $r++) {
                 $check= $request->deduction_actual_amount[$r];

                  if($check>0){

                    $insert_salary_details  = new HrmEmployeeSalaryDetails;
                    $insert_salary_details->hrm_employee_salary_id     = $id;
                    $insert_salary_details->hrm_salary_head_id         = $request->deduction_id[$r];
                    $insert_salary_details->amount                     = $request->amountdeduction[$r];
                    $insert_salary_details->amount_type                = $request->typededuction[$r];
                    $insert_salary_details->actual_amount              = $request->deduction_actual_amount[$r];
                    $insert_salary_details->save();

                    $this->recordActivity(
                         1,
                         'Updated Employee Fringe Benefit Deduction',
                         $insert_salary_details,
                         $insert_salary_details->id,
                         'hrm_employee_salary_details'
                    );

                   }
                }

                DB::commit();

                $this->recordActivity(
                     1,
                     'Updated Employee Fringe Benefit',
                     $insert_salary_master,
                     $insert_salary_master->id,
                     'hrm_employee_salary'
                );


                $status = true;
                $message = 'Employee Fringe Benefit has been updated..!';
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
}
