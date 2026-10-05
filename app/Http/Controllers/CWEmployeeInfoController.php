<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Auth;
use DB;
use Crypt;
use Validator;

use App\Models\HrmMonth;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmBank;

class CWEmployeeInfoController extends Controller
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


        //$ldate = date('Y-m-d H:i:s');
        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear  = date('Y');



        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

          return view('cw_employeeinfo.cw_employeeinfo_list')
              -> with('default_user_location',  $default_user_location)
              -> with('cmonth',  $cmonth)
              -> with('cyear',  $cyear) ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function cw_employeesalarylistdata(Request $request){

// dd($request->all());
        $condition='';

        if($request->location==null){
            $location = '';
        }else{
            $location = " AND b.hrm_location_id=".$request->location;
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



        $date_of_month  = $request->year.'-'.$request->month.'-01';
        $year_month     = $request->year.'-'.$request->month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = Auth::user()->id;

        $user_id    = Auth::user()->id;
        $data       = DB::select("SELECT
                                    a.id,
                                         concat(c.employee_name,' | ',b.employee_code,' | ',d.depertment_name,' | ',f.designation_name) as employee_name,
                                    c.id as employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    f.priority,
                                    e.location_name,
                                    a.salary_amount,
                                    a.account_no,
                                    a.accounts_code,
                                    IF(a.payment_mode = 1, 'Cash', g.short_name) AS payment_mode,
                                    c.Images,
                                    ff.plant_name


                                FROM
                                    hrm_employee_salary a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        $location
                                        AND b.id in
                                        (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5))
                                        $condition
                                        JOIN
                                    hrm_employment_status bb ON b.hrm_employment_status_id=bb.id and bb.status=2
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        JOIN
                                    hrm_plant ff ON b.hrm_plant_id=ff.id 
                                       LEFT JOIN
                                    hrm_bank g ON a.hrm_bank_id = g.id
                                        JOIN
                                    user_location j ON b.hrm_location_id = j.hrm_location_id AND j.users_id = $user_id
                                    GROUP BY a.id,c.employee_name,d.depertment_name,f.designation_name,e.location_name,a.salary_amount,c.id,b.employee_code,a.account_no,a.payment_mode,a.accounts_code,g.short_name,f.priority,c.Images");


        return datatables()->of($data)
        ->addColumn('Link', function ($data) {
           return
           ' <a href="'. url('/cwemployeeinfo') . '/' .
           Crypt::encrypt($data->id) .
           '/edit' .'"' .
           'class="btn  btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
         })
        ->editColumn('id', '{{$id}}')
        ->setRowId('id')
        ->rawColumns(['Link'])
        ->make(true);

    }


    public function store(Request $request)
    {


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

     $id = Crypt::decrypt($id);


     $master_data=DB::SELECT("SELECT
                                a.id,
                                -- c.employee_name,
                                concat(c.employee_name,' | ',b.employee_code) as employee_name,
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
                                hrm_bank g ON g.id=a.hrm_bank_id ");

                          return view('cw_employeeinfo.edit_cwemployeesalary')
                               ->with('master_data',$master_data)
                               ->with('bank_name',HrmBank::all());
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
            'payment_mode'        => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('cwemployeeinfo')
                        ->withErrors($validator)
                        ->withInput();
        }


        DB::beginTransaction();
            try{

                    $insert_salary_master  = HrmEmployeeSalary::find($id);
                    $insert_salary_master->salary_amount              = 0;
                    $insert_salary_master->account_no                 = $request->account_no;
                    $insert_salary_master->accounts_code              = $request->accounts_code;
                    $insert_salary_master->payment_mode               = $request->payment_mode;
                    $insert_salary_master->users_id                   = Auth::user()->id;
                    $insert_salary_master->by_bank_percent            = $request->by_bank_percent;

                    if($request->payment_mode==1){
                      $insert_salary_master->hrm_bank_id              = null;
                    } else{
                      $insert_salary_master->hrm_bank_id              = $request->bank_name;
                    }
                    $insert_salary_master->save();


               DB::commit();

               $this->recordActivity(
                     1,
                     'Updated CW Employee Info',
                     $insert_salary_master->getChanges(),
                     $insert_salary_master->id,
                     'hrm_employee_salary'
                );

        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }


       $request->session()->flash('alert-success', 'data has been successfully Updated!');
       return Redirect::to('cwemployeeinfo');

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

        $cancel = HrmEmployeeResign::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Application !!');
            return Redirect()->back();
        }

        DB::table('hrm_employee_resignation')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeresign');

    }





}
