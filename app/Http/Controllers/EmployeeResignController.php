<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeResign;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeShift;

use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResignApprovedExport;
use App\Models\HrmEmployee;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmLastPromotion;
use App\Models\HrmSalaryGradeMaster;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Config;

class EmployeeResignController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");
        // dd($default_user_location);
        return view('employee_resign.employee_resign_list')
                ->with('default_user_location', $default_user_location) ;
    }

    public function create()
    {
        return view('employee_resign.create_employee_resign');
    }


    public function resignapproved()
    {
          $user_id = Auth::user()->id;
          $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");
              return view('employee_resign.resign_approved_list')
              -> with('default_user_location',  $default_user_location) ;
    }

    public function resignApprovedExport()
    {
        request()->validate([
            'date_from'=> 'required|date',
            'date_to'=> 'required|date',
        ]);

        $locationName= '-All Location-';
        $CategoryName = '';
        $list = '';
        $categorylist = '';
        $filter = '';

        if(!empty(request()->location)){

            $list = implode(',',request()->location);


            $locationName = DB::select("
                select
                    group_concat(location_name) as location_name
                from
                    hrm_location
                        where id in ($list)
            ")[0]->location_name;

        }



        if(!empty(request()->category)){

            $categorylist = implode(',',request()->category);


            $CategoryName = DB::select("
                select
                    group_concat(category_name) as category_name
                from
                    hrm_category
                        where id in ($categorylist)
            ")[0]->category_name;

        }

        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', request()->date_to)));

        if(request()->filter==0){
            $filter = " AND a.effective_date between '$date_from' AND '$date_to'";
        }elseif(request()->filter==1){
            $filter = " AND cc.joining_date between '$date_from' AND '$date_to'";
        }elseif(request()->filter==2){
            $filter = " AND cc.confirmation_date between '$date_from' AND '$date_to'";
        }elseif(request()->filter==3){
            $filter = " AND cc.joining_date between '$date_from' AND '$date_to'  AND a.effective_date between '$date_from' AND '$date_to'  ";
        }else{
             $filter = " AND cc.confirmation_date between '$date_from' AND '$date_to'  AND a.effective_date between '$date_from' AND '$date_to'  ";
        }


        if(!empty(request()->hrm_depertment_id)){
            $filter = $filter.' AND b.hrm_depertment_id='.request()->hrm_depertment_id;
        }
         if(!empty(request()->hrm_designation_id)){
            $filter = $filter.' AND b.hrm_designation_id='.request()->hrm_designation_id;
        }


        ob_end_clean();

        ob_start();

        return Excel::download(
            new ResignApprovedExport(
                $locationName.'-('.$CategoryName.')',
                $list,
                request()->date_from,
                request()->date_to,
                $categorylist,
                $filter
            ),

            'resign_approved.xlsx'
        );
    }



    public function employeeresignlist(Request $request){

        // dd('workisdfs');
        if($request->location==null){
            $condition = '';
        }else{
            $condition = 'AND b.hrm_location_id='.$request->location;
        }

        $user_id=Auth::user()->id;
        $resignapplicationlist = DB::select("SELECT
                                                  a.id,
                                                  c.id AS employee_id,
                                                  CONCAT(c.employee_name, ' | ', b.employee_code) AS employee_name,
                                                  e.depertment_name,
                                                  f.designation_name,
                                                  DATE_FORMAT(a.apply_date, '%d-%m-%Y') AS apply_date,
                                                  DATE_FORMAT(a.effective_date, '%d-%m-%Y') AS effective_date,
                                                  a.comment,
                                                  c.Images,
                                                  i.type_name as resignation_type,
                                                  h.name,
                                                  d.location_name,
                                                  (SELECT SUM(al.credit - al.debit)
                                                    FROM hrm_loan_ledger al
                                                        JOIN hrm_loan_application bl ON al.hrm_loan_application_id = bl.id
                                                    WHERE al.valid = 1
                                                        AND bl.approved_status = 2
                                                        AND bl.hrm_employee_id = c.id group by c.id  having SUM(al.credit - al.debit)>0  ) AS remaining_amount
                                              FROM
                                                  hrm_employee_resignation a
                                                      JOIN
                                                  hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                                                      AND a.valid=1
                                                      AND b.hrm_location_id
                                                      $condition
                                                      JOIN
                                                  hrm_employee c ON c.id = b.hrm_employee_id
                                                      JOIN
                                                  hrm_location d ON b.hrm_location_id = d.id
                                                      JOIN
                                                  hrm_depertment e ON b.hrm_depertment_id = e.id
                                                      JOIN
                                                  hrm_designation f ON b.hrm_designation_id = f.id
                                                      JOIN
                                                  user_location g ON b.hrm_location_id = g.hrm_location_id
                                                      AND g.users_id = $user_id
                                                      JOIN
                                                  users h ON a.users_id = h.id
                                                  LEFT JOIN hrm_resignation_type i ON i.id = a.hrm_resignation_type_id AND i.valid = 1
                                              WHERE
                                                  c.active_status = 1
                                                      AND b.employee_activity = 1
                                                      AND a.resingnation_status = 1") ;

        // dd($resignapplicationlist);
        return json_encode(array('data' => $resignapplicationlist));

    }


   public function resignapprovedlist(Request $request)
   {

        // dd($request->all());
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));


        if($request->filter==0){
            $condition = " AND a.effective_date between '$date_from' AND '$date_to'";
        }elseif($request->filter==1){
            $condition = " AND cc.joining_date between '$date_from' AND '$date_to'";
        }elseif($request->filter==2){
            $condition = " AND cc.confirmation_date between '$date_from' AND '$date_to'";
        }elseif($request->filter==3){
            $condition = " AND cc.joining_date between '$date_from' AND '$date_to'  AND a.effective_date between '$date_from' AND '$date_to'  ";
        }else{
             $condition = " AND cc.confirmation_date between '$date_from' AND '$date_to'  AND a.effective_date between '$date_from' AND '$date_to'  ";
        }

        if (isset($request->location)) {
            $list = implode(',',$request->location);
            $condition = $condition.' AND b.hrm_location_id in ('.$list.')' ;
        }


        if (isset($request->category)) {
            $list = implode(',',$request->category);
            $condition = $condition.' AND b.hrm_category_id in ('.$list.')';
        }

        if (isset($request->hrm_depertment_id)) {
            $condition = $condition.' AND b.hrm_depertment_id ='.$request->hrm_depertment_id;
        }

        if (isset($request->hrm_designation_id)) {
            $condition = $condition.' AND b.hrm_designation_id ='.$request->hrm_designation_id;
        }

        if (isset($request->hrm_resignation_type_id)) {
            $condition = $condition.' AND a.hrm_resignation_type_id ='.$request->hrm_resignation_type_id;
        }


        $user_id=Auth::user()->id;

        $resignapplicationlist = DB::select("SELECT
                                                    a.id,
                                                    c.id AS employee_id,
                                                    CONCAT(c.employee_name, ' | ', ifnull(b.employee_code,'') ) AS employee_name,
                                                    e.depertment_name,
                                                    f.designation_name,
                                                    a.apply_date,
                                                    a.effective_date,
                                                    a.approve_comment AS comment,
                                                    c.Images,
                                                    h.name,
                                                    IF(a.resingnation_status = 2,
                                                        'Approved',
                                                        'Pending') AS status,
                                                    d.location_name,
                                                    cc.joining_date,
                                                    i.type_name as resignation_type,
                                                    cc.confirmation_date
                                                FROM
                                                    hrm_employee_resignation a
                                                        JOIN
                                                    hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                                                        AND a.resingnation_status IN (2 , 4)
                                                        AND a.valid=1
                                                        JOIN
                                                    hrm_employee c ON c.id = b.hrm_employee_id
                                                        JOIN
                                                    hrm_employee_joining cc ON c.id = cc.hrm_employee_id
                                                            $condition
                                                        JOIN
                                                    hrm_location d ON b.hrm_location_id = d.id
                                                        JOIN
                                                    hrm_depertment e ON b.hrm_depertment_id = e.id
                                                        JOIN
                                                    hrm_designation f ON b.hrm_designation_id = f.id
                                                        JOIN
                                                    user_location g ON b.hrm_location_id = g.hrm_location_id
                                                        AND g.users_id = $user_id
                                                        JOIN
                                                    users h ON a.users_id = h.id
                                                        LEFT JOIN hrm_resignation_type i ON i.id = a.hrm_resignation_type_id AND i.valid = 1
                                                    ") ;


        return json_encode(array('data' => $resignapplicationlist));

    }




    public function store(Request $request)
    {

        // dd($request->all());


         $validator = Validator::make($request->all(), [
            'employee_name'              => 'required',
            'hrm_resignation_type_id'              => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeeresign/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $applied          = date('Y-m-d', strtotime(str_replace('/', '-', $request->applied))) ;
        $effictive_date   = date('Y-m-d', strtotime(str_replace('/', '-', $request->effictive_date)));


        $hrm_employee_job_info_id=DB::Select("SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id=$request->employee_name and employee_activity=1");
        $hrm_employee_job_info_id = $hrm_employee_job_info_id[0]->id;


        $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id = $request->employee_name) AND end_date is null");

        if(!empty($check_data[0]->start_date)){
          if($effictive_date<$check_data[0]->start_date){

               $request->session()->flash('alert-danger',
                'Sorry back date not accepted!, Already Input : '.$check_data[0]->start_date);
               return Redirect::to('employeeresign/create');

          }
        }


        $check_aaa = DB::SELECT("SELECT id FROM hrm_employee_resignation
            WHERE hrm_employee_job_info_id = $hrm_employee_job_info_id
            AND resingnation_status IN(1,2,4) AND valid=1");


        if(!empty($check_aaa)){
             $request->session()->flash('alert-danger', 'Already Applied!');
             return Redirect::to('employeeresign/create');

        }


        $insert     = new HrmEmployeeResign;
        // $insert->resign_status                = $request->resign_status;
        $insert->hrm_employee_job_info_id     = $hrm_employee_job_info_id;
        $insert->apply_date                   = $applied;
        $insert->effective_date               = $effictive_date;
        $insert->hrm_resignation_type_id      = $request->hrm_resignation_type_id;
        $insert->valid                        = 1;
        $insert->comment                      = $request->comment;
        $insert->resingnation_status          = 1;
        $insert->users_id                     = Auth::user()->id;;
        $insert->save();


        $this->recordActivity(
             1,
             'Created Employee Resignation Application ',
             null,
             $insert->id,
             'hrm_employee_resignation'
        );


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('employeeresign');
    }



    public function resignapprovesubmit(Request $request)
    {

        // dd('sdfs');

        // $validator = Validator::make($request->all(), [
        //     'effective_date'              => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return redirect('employeeresign')
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }

        $update     = HrmEmployeeResign::find($request->id);
        $effective_date = $update->effective_date;
        $user_id        = Auth::user()->id;
        $ldate          = date('Y-m-d');
        // $effective_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->effective_date)));
        $xmasDay        = new DateTime($effective_date.'- 1 day');
        $end_date       = $xmasDay->format('Y-m-d');



        $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id = $request->employee_id) AND end_date is null");

        // dd($check_data);


        if(!empty($check_data[0]->start_date)) {

            $activity_date = $check_data[0]->start_date;

            if($effective_date < $activity_date){
                              $request->session()->flash('alert-danger',
                  'Sorry back date not accepted!, Already Input : '.$activity_date);
                 return Redirect::to('employeeresign');

            }

        }


        if($effective_date < $ldate){
            $status = 1;   // For Current Process
        }else{
            $status = 2;   // For Future Process
        }


        DB::beginTransaction();
            try{

                if($status == 1){
                  $update->resingnation_status        = $request->action;
                }else{
                  $update->resingnation_status        = 4;
                }

                $update->approve_comment            = $request->comment;
                $update->effective_date             = $effective_date;
                $update->users_id                   = Auth::user()->id;;
                $update->save();


                if ($request->action != 3){

                    if($status == 1){

                      $update_job_info =HrmEmployeeJobInfo::find($update->hrm_employee_job_info_id);
                      $update_job_info ->employee_activity  =0;
                      $update_job_info ->save();

                      // dd($update->hrm_employee_job_info_id);

                       $check_data = DB::select("SELECT
                                    *
                                FROM
                                    hrm_employee_shift
                                WHERE
                                    id IN (SELECT
                                            MAX(id) id
                                        FROM
                                            hrm_employee_shift
                                        WHERE
                                            hrm_employee_job_info_id = $update->hrm_employee_job_info_id)")[0];



                         if(!empty($check_data)){

                        // dd($check_data);

                              if($effective_date<$check_data->start_date){
                                   $enddate = $check_data->start_date;
                              }else{
                                   $enddate = $effective_date;
                              }


                              DB::update("UPDATE hrm_employee_shift
                                      SET end_date = '$enddate',
                                          comment  = 'from Employee Resign',
                                          users_id = $user_id,
                                          updated_at = '$ldate'
                                      WHERE hrm_employee_job_info_id = $update->hrm_employee_job_info_id
                                      AND end_date is null");

                         }




                    }

                    $id = DB::SELECT("SELECT id FROM users WHERE hrm_employee_id = $request->employee_id");

                    If(!empty($id)){
                        $id = $id[0]->id;
                        $password = bcrypt('zax!1%$l:)^'.$id);
                        DB::update("UPDATE users SET valid = 0,password='$password' WHERE id = $id");
                        // DB::table('user_location')->where('users_id', '=', $id)->delete();
                        // DB::table('role_user')->where('user_id', '=', $id)->delete();
                        // DB::table('assigned_roles')->where('user_id', '=', $id)->delete();
                        // DB::table('users')->where('id', '=', $id)->delete();
                    }

                    // dd($end_date);

                    DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                                WHERE end_date is null AND hrm_employee_job_info_id = $update->hrm_employee_job_info_id");

                    $insert_employee_activity  = new HrmEmployeeActivity;
                    $insert_employee_activity->hrm_employee_job_info_id  = $update->hrm_employee_job_info_id;
                    $insert_employee_activity->activity_date             = $effective_date;
                    $insert_employee_activity->comment                   = $request->comment;
                    $insert_employee_activity->users_id                  = Auth::user()->id;
                    $insert_employee_activity->activity                  = 1;
                    $insert_employee_activity->hrm_employee_activity_status_id  = 5;
                    $insert_employee_activity->start_date                = $effective_date;
                    $insert_employee_activity->end_date                  = $effective_date;
                    $insert_employee_activity->save();

                }


        DB::commit();

        // $this->recordActivity(
        //      1,
        //      'Employee Resignation Application Approved',
        //      null,
        //      $update_job_info->id,
        //      'hrm_employee_job_info'
        // );

        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }




        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('employeeresign');
    }





    public function shiftdatechange_single(Request $request)
    {
       // dd($request->all());
        $start_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        $shift = HrmEmployeeShift::find($request->hrm_employee_shift_id);
        $shift->start_date = $start_date;
        $shift->save();

        // $start_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        // DB::UPDATE("UPDATE hrm_employee_shift SET start_date='$start_date' WHERE id=$request->hrm_employee_shift_id ");

        $this->recordActivity(
             1,
             'Updated Employee Shift Change',
             $shift->getChanges(),
             $request->hrm_employee_shift_id,
             'hrm_employee_shift'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('employeeshiftlist');


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
        $user_id = Auth::user()->id;
        $cancel  = HrmEmployeeResign::find($id);
        $ldate   = date('Y-m-d');

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        DB::UPDATE("UPDATE hrm_employee_resignation SET valid=0,comment='Deleted By UserId= $user_id',updated_at = '$ldate' WHERE id=$id");


        $this->recordActivity(
             1,
             'Deleted Employee Resignation Application',
             null,
             $id,
             'hrm_employee_resignation'
        );


        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeresign');

    }


    public function resign_rejoin(Request $request,$id){


        // dd($id);

        $user_id = Auth::user()->id;
        $cancel  = HrmEmployeeResign::find($id);
        $ldate   = date('Y-m-d');

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }
        $hrm_employee_job_info_id = $cancel->hrm_employee_job_info_id;


       DB::beginTransaction();

        try {

              DB::UPDATE("UPDATE hrm_employee_resignation SET valid=0,resingnation_status=5,comment='Rejoined By UserId= $user_id',updated_at = '$ldate' WHERE id=$id");

              DB::UPDATE("UPDATE hrm_employee_job_info SET employee_activity  = 1 WHERE id=$hrm_employee_job_info_id ");

              $max_emp_shift_id = DB::SELECT("SELECT max(id) as id from hrm_employee_shift where hrm_employee_job_info_id=$hrm_employee_job_info_id");
              $max_emp_shift_id = $max_emp_shift_id[0]->id;

              DB::update("UPDATE hrm_employee_shift
                      SET end_date = null,
                          comment  = 'from Employee Resign-Rejoin',
                          users_id = $user_id,
                          updated_at = '$ldate'
                      WHERE hrm_employee_job_info_id = $hrm_employee_job_info_id
                      AND id=$max_emp_shift_id");


              DB::DELETE("DELETE FROM hrm_employee_activity WHERE hrm_employee_job_info_id=$hrm_employee_job_info_id AND hrm_employee_activity_status_id=5");

              $max_emp_activ_id = DB::SELECT("SELECT max(id) as id from hrm_employee_activity where hrm_employee_job_info_id=$hrm_employee_job_info_id");
              $max_emp_activ_id = $max_emp_activ_id[0]->id;

              DB::update("UPDATE hrm_employee_activity SET end_date = null
                          WHERE hrm_employee_job_info_id = $hrm_employee_job_info_id AND id= $max_emp_activ_id");

              DB::commit();

              $this->recordActivity(
                     1,
                     'Employee Rejoined',
                     null,
                     $id,
                     'hrm_employee_resignation'
                );

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }


        $request->session()->flash('alert-success', 'Successfully Rejoin !');
        return Redirect::to('resignapproved');

    }

    public function employee_rejoin($id)
    {
        dd('restricted');
        $resignId     = HrmEmployeeResign::find($id);
        $job_info_id  = $resignId->hrm_employee_job_info_id;
        $employee_id  = HrmEmployeeJobInfo::find($job_info_id)->hrm_employee_id;

        $employee_data = DB::select("SELECT a.id,
                                        LPAD(a.id, 5, '0') as unique_Code,

                                        a.employee_name,
                                        a.nickname,
                                        a.gender,
                                        a.tin,
                                        a.hrm_education_id,
                                        b.id as hrm_employee_job_info_id,
                                        c.id as location_id,
                                        c.location_name,
                                        d.id as department_id,
                                        d.depertment_name,
                                        e.id as designation_id,
                                        e.designation_name,
                                        b.basic_salary,
                                        b.employee_code,
                                        a.contact_number,
                                        DATE_FORMAT(joining_date, '%d-%m-%Y') AS joining_date,
                                        DATE_FORMAT(confirmation_date, '%d-%m-%Y') AS confirmation_date,
                                        concat(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Year(s) ' ,MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12),' Month(s) ') as jobduration,
                                        g.id as employeestatus_id,
                                        g.employment_status as employeestatus_name,
                                        h.id as category_id,
                                        h.category_name,
                                        i.id as sectionid,
                                        i.section_name,
                                        b.overtime_status,
                                        j.id as manage_by_id,
                                        j.employee_name as manage_by_name,
                                        l.id as shift_id,
                                        l.shift_name,
                                        m.id as religion_id,
                                        m.religion,
                                        n.id as marital_status_id,
                                        n.marital_status,
                                        o.id as blood_group_id,
                                        o.blood_group,
                                        p.education_name,
                                        q.plant_name,
                                        a.Images,
                                        b.insurance,
                                        b.official_contact_no,
                                        b.official_email,
                                        s.period
                                    from hrm_employee a
                                    JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                                        AND b.id in (SELECT max(id) FROM hrm_employee_job_info WHERE hrm_employee_id=$employee_id )
                                        AND a.id= $employee_id
                                    Join hrm_location c On b.hrm_location_id=c.id
                                    JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                    Join hrm_designation e On b.hrm_designation_id=e.id
                                    Join hrm_employee_joining f on b.hrm_employee_id=f.hrm_employee_id
                                    Join hrm_employment_status g On b.hrm_employment_status_id=g.id
                                    Join hrm_category h on b.hrm_category_id=h.id
                                    Join hrm_section i on b.hrm_section_id=i.id
                                    join hrm_employee j on b.hrm_manage_by_id=j.id
                                    Join hrm_employee_shift k on b.id=k.hrm_employee_job_info_id
                                        AND k.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                                    Join hrm_shift l on k.hrm_shift_id=l.id
                                    Join hrm_religion m on m.id=a.hrm_religion_id
                                    Join hrm_marital_status n on n.id=a.hrm_marital_status_id
                                    Join hrm_blood_group o on o.id=a.hrm_blood_group_id
                                    Join hrm_education p ON p.id=a.hrm_education_id
                                    JOIN hrm_plant q ON q.id=b.hrm_plant_id
                                    LEFT JOIN hrm_employee_probation r ON r.hrm_employee_id = a.id
                                    LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id");

        $employee_salary_grade='';
         if(config('module_config.payroll_module') == 1) {

            $hrm_employee_job_info_id   = $employee_data[0]->hrm_employee_job_info_id;
            $hrm_salary_grade_master_id = HrmEmployeeSalary::where('hrm_employee_job_info_id',$hrm_employee_job_info_id)->first();



            if(!empty($hrm_salary_grade_master_id)){
            $hrm_salary_grade_master_id =$hrm_salary_grade_master_id->hrm_salary_grade_master_id;


                $employee_salary_grade      = DB::SELECT("SELECT
                                                        b.id,
                                                        b.grade_name
                                                    FROM
                                                        hrm_salary_grade_master a
                                                            JOIN
                                                        hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
                                                            AND a.id=$hrm_salary_grade_master_id");
            }else{
                $employee_salary_grade = DB::SELECT("SELECT 0 as id,'' as grade_name FROM hrm_salary_grade_master limit 1");
            }


         }
        return view('employee_resign.employee_rejoin', compact('employee_data', 'employee_salary_grade'));
    }

    public function employeeRejoinSubmit(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'depertment'        => 'required|exists:hrm_depertment,id',
            'designation'       => 'required|exists:hrm_designation,id',
            'category'          => 'required|exists:hrm_category,id',
            'job_location'      => 'required|exists:hrm_location,id',
            'section'           => 'required|exists:hrm_section,id',
            'working_shift'     => 'required|exists:hrm_shift,id',
            'overtime'          => 'required',
            'manage_by'         => 'required|exists:hrm_employee,id',
            'employeestatus'    => 'required|exists:hrm_employment_status,id',
            'basic_salary'      => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldjoining_date)));
        $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldconfirmation_date)));
        $transfer_date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->transfer_date)));

        $xmasDay            = new DateTime($transfer_date.'- 1 day');
        $end_date           = $xmasDay->format('Y-m-d');

        $user_id            = Auth::user()->id;
        $ldate              = date('Y-m-d H:i:s');



       $check_datas = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $request->hrm_employee_job_info_id)) AND start_date='$transfer_date' ");


        if(!empty($check_datas)){
             $request->session()->flash('alert-danger', 'Sorry This date has been already used, Please Check!');
                return redirect()->route('resignapproved');

        }


        $check_data = DB::select("SELECT end_date FROM hrm_employee_activity  WHERE id IN (SELECT MAX(id) AS id FROM hrm_employee_activity WHERE hrm_employee_job_info_id = $request->hrm_employee_job_info_id)");


        if($transfer_date<$check_data[0]->end_date){
             $request->session()->flash('alert-danger', 'Sorry you can not give back date employee activity!');
                return redirect()->route('resignapproved');
        }




            DB::beginTransaction();
            try{


            // $insert_employee_activity  = new HrmEmployeeActivity;
            // $insert_employee_activity->hrm_employee_job_info_id  = $request->hrm_employee_job_info_id;
            // $insert_employee_activity->activity_date             = $transfer_date ;
            // $insert_employee_activity->comment                   = $request->comment;
            // $insert_employee_activity->users_id                  = Auth::user()->id;
            // $insert_employee_activity->status                    = 2;
            // $insert_employee_activity->apply_for                 = 2;
            // $insert_employee_activity->save();

            // DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
            //             WHERE end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id ");

            $insert_employee_activity  = new HrmLastPromotion();
            $insert_employee_activity->hrm_employee_id  = $request->employee_name;
            $insert_employee_activity->promotion_date   = $transfer_date;
            $insert_employee_activity->users_id         = Auth::user()->id;
            $insert_employee_activity->save();



            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0
                        WHERE id = $request->hrm_employee_job_info_id ");

            $oldshift = DB::SELECT("SELECT * FROM hrm_employee_shift Where hrm_employee_job_info_id = $request->hrm_employee_job_info_id and id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=$request->hrm_employee_job_info_id)");

            $end_shift_date   = $oldshift[0]->start_date;
            $start_shift_date =date('Y-m-d', strtotime($end_shift_date." +1 days"));

            // Need to work in future
            // if($transfer_date<$end_shift_date){
            //     $request->session()->flash('alert-danger', 'Sorry backdate shift related issue!');
            //             return redirect('employeepromotion');
            // }

            // if($transfer_date>$end_shift_date){
            //     $end_shift_date = date('Y-m-d', strtotime($transfer_date." -1 days"));
            // }


            //Here
            DB::update("UPDATE hrm_employee_shift SET end_date= '$end_shift_date',comment='from employee promotion',users_id= $user_id,updated_at='$ldate'  WHERE  end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");


                $insert     = new HrmEmployeeJobInfo();
                $insert->employee_code              = $request->employee_code;
                $insert->hrm_employee_id            = $request->employee_name;
                $insert->hrm_depertment_id          = $request->depertment;
                $insert->hrm_designation_id         = $request->designation;
                $insert->hrm_category_id            = $request->category;
                $insert->hrm_employment_status_id   = $request->employeestatus;
                $insert->hrm_manage_by_id           = $request->manage_by;
                $insert->employee_shift_status      = 1;
                $insert->overtime_status            = $request->overtime;
                $insert->hrm_plant_id               = $request->plant_name;
                $insert->employee_activity          = 1;
                $insert->basic_salary               = $request->basic_salary;
                $insert->hrm_location_id            = $request->job_location;
                $insert->hrm_section_id             = $request->section;
                $insert->insurance                  = $request->insurance;
                $insert->users_id                   = Auth::user()->id;
                $insert->save();


                $new_job_id = $insert->id;
                DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                        WHERE end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id ");


                $insert_shift  = new HrmEmployeeShift;
                $insert_shift->hrm_employee_job_info_id = $insert->id;
                $insert_shift->hrm_shift_id             = $request->working_shift;
                $insert_shift->start_date               = $start_shift_date;
                $insert_shift->valid                    = 1;
                $insert_shift->comment                  = 'Promotion';
                $insert_shift->users_id                 = Auth::user()->id;
                $insert_shift->save();







                $oldcard = DB::SELECT("SELECT * FROM hrm_employee_card_code Where hrm_employee_job_info_id = $request->hrm_employee_job_info_id");

                if(empty($oldcard)){

                }else{

                $insert_card     = new HrmEmployeeCardCode();
                $insert_card->card_code                = $oldcard[0]->card_code;
                $insert_card->device_id                = $oldcard[0]->device_id;
                $insert_card->hrm_employee_job_info_id = $insert->id;
                $insert_card->save();


                }


            // dd($new_job_id);



            if(Config::get('module_config.payroll_module') == 1){

                    $hrm_employee_job_info_id     = $insert->id;
                    $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first()->id;
                    $gross_salary                 = $insert->basic_salary;
                    $hrm_designation_id           = $insert->hrm_designation_id;
                    $old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;

                    $getFunction = new CommonController();
                    $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);


            }


            $insert_employee_activity  = new HrmEmployeeActivity();
            $insert_employee_activity->hrm_employee_job_info_id  = $insert->id;
            $insert_employee_activity->activity_date             = $transfer_date;
            $insert_employee_activity->comment                   = $request->comment;
            $insert_employee_activity->users_id                  = Auth::user()->id;
            $insert_employee_activity->activity                  = 1;
            $insert_employee_activity->hrm_employee_activity_status_id  = 6;
            $insert_employee_activity->start_date                   = $transfer_date;
            $insert_employee_activity->old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;
            $insert_employee_activity->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Employee Promotion',
                 $insert_employee_activity,
                 $insert_employee_activity->id,
                 'hrm_employee_activity'
            );

            }catch (\Exception $e) {
                DB::rollback();
                $validator->errors()->add('field', $e->getMessage());
                return response()->json($validator->errors()->all());
            }

            $request->session()->flash('alert-success', 'data has been successfully added!');
            return Redirect::to('resignapproved');
    }
}
