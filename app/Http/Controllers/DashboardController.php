<?php

namespace App\Http\Controllers;

use Response;
use App\Models\HrmLocation;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeShift;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeJoining;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeProbation;
use App\Models\HrmSalaryGradeMaster;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function provision_employeelist()
    {
        $user_id = auth()->id();

        $currentemployee = DB::select("SELECT
                                            a.id,
                                            CONCAT(a.employee_name, ' | ', b.employee_code) AS employee_name,
                                            c.location_name,
                                            d.depertment_name,
                                            e.alis AS designation_name,
                                            a.contact_number,
                                            f.joining_date,
                                            a.Images,
                                            b.id AS hrm_employee_job_info_id,
                                            DATE_ADD(f.joining_date,
                                                INTERVAL h.period MONTH) AS probable_date
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                AND a.active_status = 1
                                                AND b.employee_activity = 1
                                                AND hrm_employment_status_id = 1
                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                                JOIN
                                            hrm_employee_probation g ON g.hrm_employee_id = a.id
                                                JOIN
                                            hrm_probation_period h ON h.id = g.hrm_probation_period_id
                                                JOIN
                                            user_location i ON b.hrm_location_id = i.hrm_location_id
                                                AND i.users_id = $user_id
                                                AND DATE_ADD(DATE_ADD(f.joining_date,
                                                    INTERVAL h.period MONTH),
                                                INTERVAL - 30 DAY) < CURRENT_DATE");

        return json_encode(array('data' => $currentemployee));
    }

    public function departmentwise_present()
    {
        $user_id = auth()->id();

        $currentemployee = DB::select("SELECT aa.location_name,aa.depertment_name,Sum(Total_Employee) As total_employee,Sum(Present) As Present,Sum(Absent) As Absent,Sum(Leave_s) As Leave_s
            FROM
                (SELECT
                    e.location_name,
                    c.depertment_name,
                    COUNT(a.hrm_employee_id) AS Total_Employee,
                    0 AS Present,
                    0 As Absent,
                    0 As Leave_s

                FROM
                    hrm_attendance a
                        JOIN
                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        JOIN
                    hrm_depertment c ON b.hrm_depertment_id = c.id
                        JOIN
                    hrm_designation d ON b.hrm_designation_id = d.id
                        JOIN
                    hrm_location e ON e.id=b.hrm_location_id
                    WHERE b.employee_activity=1
                    AND (a.punche_date) = '2018-07-25'
                GROUP BY e.location_name,c.depertment_name
                UNION
                SELECT
                    e.location_name,
                    c.depertment_name,
                    0 AS Total_Employee,
                    COUNT(a.hrm_employee_id) AS Present,
                    0 As Absent,
                    0 As Leave_s
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        AND a.attendance_status IN (2 , 5, 6)
                        JOIN
                    hrm_depertment c ON b.hrm_depertment_id = c.id
                        JOIN
                    hrm_designation d ON b.hrm_designation_id = d.id
                        JOIN
                    hrm_location e ON e.id=b.hrm_location_id
                    WHERE b.employee_activity=1
                    AND (a.punche_date) = '2018-07-25'
                GROUP BY  e.location_name,
                    c.depertment_name
                UNION
                SELECT
                    e.location_name,
                    c.depertment_name,
                    0 AS Total_Employee,
                    0 As Present,
                    COUNT(a.hrm_employee_id) AS Absent,
                    0 As Leave_s
                FROM
                    hrm_attendance a
                        JOIN
                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        AND a.attendance_status IN (1)
                        JOIN
                    hrm_depertment c ON b.hrm_depertment_id = c.id
                        JOIN
                    hrm_designation d ON b.hrm_designation_id = d.id
                        JOIN
                    hrm_location e ON e.id=b.hrm_location_id
                    WHERE b.employee_activity=1
                     AND (a.punche_date) = '2018-07-25'
                GROUP BY  e.location_name,
                    c.depertment_name
                UNION
                SELECT
                    e.location_name,
                    c.depertment_name,
                    0 AS Total_Employee,
                    0 As Present,
                    0 As Absent,
                    COUNT(a.hrm_employee_id) AS Leave_s

                FROM
                    hrm_attendance a
                        JOIN
                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                        AND a.attendance_status IN (3,4)
                        JOIN
                    hrm_depertment c ON b.hrm_depertment_id = c.id
                        JOIN
                    hrm_designation d ON b.hrm_designation_id = d.id
                        JOIN
                    hrm_location e ON e.id=b.hrm_location_id
                    WHERE b.employee_activity=1
                    AND (a.punche_date) = '2018-07-25'
                GROUP BY  e.location_name,
                    c.depertment_name
                ) aa

                GROUP BY aa.depertment_name,aa.location_name");

        return json_encode(array('data' => $currentemployee));
    }

    public function probation_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'       => 'required',
            'new_salary'        => 'required',
            // 'salary_grade'      => 'required',
            'confirmation_date' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $jobid = $request->hrm_employee_job_info_id;
        $confirmation_date    = date('Y-m-d', strtotime(str_replace('/', '-', $request->confirmation_date)));

        $xmasDay              = new \DateTime($confirmation_date . '- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');
        $user_id = auth()->id();
        $ldate   = date('Y-m-d H:i:s');


        $check_data      = HrmEmployeeJoining::where('hrm_employee_id', $request->employee_id)->first();

        if (!empty($check_data->confirmation_date)) {
            $request->session()->flash('alert-danger', 'Sorry This employee already confirmed , please check!');
            return Redirect::to('probation_employee');
        }


        $check_datas = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id = $request->employee_id) AND start_date='$confirmation_date' ");


        if (!empty($check_datas)) {
            $request->session()->flash('alert-danger', 'Sorry This date has been already used, Please Check!');
            return Redirect::to('probation_employee');
        }

        $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id = $request->employee_id) AND end_date is null");


        if (empty($check_data[0]->start_date)) {
            $request->session()->flash('alert-danger', 'Sorry This date has been already used, Please Check!');
            return Redirect::to('probation_employee');
        }


        if ($confirmation_date < $check_data[0]->start_date) {
            $request->session()->flash('alert-danger', 'Sorry you can not give back date employee activity!');
            return Redirect::to('probation_employee');
        }


        $find_data = DB::SELECT("SELECT id FROM hrm_employee_activity WHERE hrm_employee_activity_status_id=8
        AND hrm_employee_job_info_id in (SELECT id from hrm_employee_job_info WHERE hrm_employee_id=$request->employee_id)");
        // dd($find_data);

        if (!empty($find_data)) {
            $request->session()->flash('alert-danger', 'Sorry this employee already confirm previous time. please check!');
            return Redirect::to('probation_employee');
        }


        DB::beginTransaction();
        try {

            $employeeinfo = HrmEmployeeProbation::where('hrm_employee_id', $request->employee_id)->first();
            $update_probation                = HrmEmployeeProbation::find($employeeinfo->id);
            $update_probation->note          = '$request->note';
            $update_probation->users_id      = auth()->id();
            $update_probation->save();


            $insert_joining      = HrmEmployeeJoining::where('hrm_employee_id', $request->employee_id)->first();
            $insert_joining->confirmation_date  = $confirmation_date;
            $insert_joining->save();


            $oldinfo    = HrmEmployeeJobInfo::find($jobid);

            $insert     = new HrmEmployeeJobInfo;
            $insert->hrm_employee_id            = $oldinfo->hrm_employee_id;
            $insert->employee_code              = $oldinfo->employee_code;
            $insert->hrm_depertment_id          = $oldinfo->hrm_depertment_id;
            $insert->hrm_designation_id         = $oldinfo->hrm_designation_id;
            $insert->hrm_category_id            = $oldinfo->hrm_category_id;
            $insert->hrm_employment_status_id   = $request->employeestatus;
            $insert->hrm_manage_by_id           = $oldinfo->hrm_manage_by_id;
            $insert->employee_shift_status      = 1;
            $insert->overtime_status            = $oldinfo->overtime_status;
            $insert->hrm_plant_id               = $oldinfo->hrm_plant_id;
            $insert->employee_activity          = 1;
            $insert->basic_salary               = $request->new_salary;
            $insert->hrm_location_id            = $oldinfo->hrm_location_id;
            $insert->hrm_section_id             = $oldinfo->hrm_section_id;
            $insert->insurance                  = $oldinfo->insurance;
            $insert->users_id                   = auth()->id();
            $insert->save();


            $oldcard = DB::SELECT("SELECT * FROM hrm_employee_card_code Where hrm_employee_job_info_id = $jobid");

            if (!empty($oldcard)) {
                $insert_card     = new HrmEmployeeCardCode;
                $insert_card->card_code                = $oldcard[0]->card_code;
                $insert_card->device_id                = $oldcard[0]->device_id;
                $insert_card->hrm_employee_job_info_id = $insert->id;
                $insert_card->save();
            }

            // dd("sdsd");

            // $oldshift = DB::SELECT("SELECT * FROM hrm_employee_shift Where hrm_employee_job_info_id = $jobid AND id in (SELECT max(id) as id FROM hrm_employee_shift Where hrm_employee_job_info_id = $jobid) ");

            // // dd($oldshift);

            // $end_shift_date   = $oldshift[0]->start_date;




            // // if($confirmation_date>$end_shift_date){
            // //     $end_shift_date = date('Y-m-d', strtotime($confirmation_date." -1 days"));
            // // }


            // $insert_shift  = new HrmEmployeeShift;
            // $insert_shift->hrm_employee_job_info_id = $insert->id;
            // $insert_shift->hrm_shift_id             = $oldshift[0]->hrm_shift_id;
            // $insert_shift->start_date               = $confirmation_date;
            // $insert_shift->comment                  = 'from probation to confirm2.';
            // $insert_shift->users_id                 = auth()->id();
            // $insert_shift->valid                    = 1;
            // $insert_shift->save();
            // // dd($oldshift[0]->hrm_shift_id);
            // // Need to work in future

            // if($confirmation_date < $end_shift_date){

            //     DB::update("UPDATE hrm_employee_shift
            //     SET hrm_employee_job_info_id = ?
            //     WHERE id IN (
            //         SELECT id
            //         FROM (
            //             SELECT id
            //             FROM hrm_employee_shift
            //             WHERE start_date >= ?
            //               AND valid = 1
            //               AND hrm_employee_job_info_id IN (
            //                   SELECT id
            //                   FROM hrm_employee_job_info
            //                   WHERE hrm_employee_id = ?
            //               )
            //         ) AS temp
            //     )
            // ", [$insert->id, $confirmation_date, $request->employee_name]);



            //     $max_id = DB::table('hrm_employee_shift')
            //         ->where('valid', 1)
            //         ->where('start_date', '<', $confirmation_date)
            //         ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
            //             $query->select('id')
            //                 ->from('hrm_employee_job_info')
            //                 ->where('hrm_employee_id', $request->employee_name);
            //         })
            //         ->max('id');

            //     $pre_date = date('Y-m-d', strtotime($confirmation_date . ' -1 day'));

            //         // dd($pre_date,$max_id);
            //     DB::table('hrm_employee_shift')
            //     ->where('id', $max_id)
            //     ->update(['end_date' => $pre_date,'comment' =>'Update From Probation Confirm']);



            //     // $ ->end_date  = $max_pre_date;
            //     // $insert_shift->save();

            //     $min_id = DB::table('hrm_employee_shift')
            //         ->where('valid', 1)
            //         ->where('start_date', '>', $confirmation_date)
            //         ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
            //             $query->select('id')
            //                 ->from('hrm_employee_job_info')
            //                 ->where('hrm_employee_id', $request->employee_name);
            //         })
            //         ->min('id');

            //         DB::table('hrm_employee_shift')
            //         ->where('id', $min_id)
            //         ->update(['start_date' => $confirmation_date,'comment' =>'Update From Probation Confirm 2']);

            // }else{
            //     // dd("dsds");
            //      $pre_date = date('Y-m-d', strtotime($confirmation_date . ' -1 day'));
            //       DB::update("UPDATE hrm_employee_shift
            //                     SET end_date= '$pre_date',
            //                     comment='Update From Probation',
            //                     users_id= $user_id,
            //                     updated_at='$ldate'
            //                 WHERE  end_date is null
            //                     AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");

            //         $insert_shift  = new HrmEmployeeShift;
            //         $insert_shift->hrm_employee_job_info_id = $insert->id;
            //         $insert_shift->hrm_shift_id             = $request->working_shift;
            //         $insert_shift->start_date               = $confirmation_date;
            //         $insert_shift->valid                    = 1;
            //         $insert_shift->comment                  = 'Probation Confirm';
            //         $insert_shift->users_id                 = Auth::user()->id;
            //         $insert_shift->save();
            // }


                        $oldshift = DB::SELECT(
                                "SELECT * FROM hrm_employee_shift WHERE hrm_employee_job_info_id = $jobid AND id IN (
                                SELECT MAX(id) AS id FROM hrm_employee_shift WHERE hrm_employee_job_info_id = $jobid)"
                            );

                            // dd($oldshift);

                            $end_shift_date = $oldshift[0]->start_date;
                            $start_shift_date = $confirmation_date;
                            $user_id = auth()->id();


                        //    dd($insert->hrm_employee_id );
                            // Need to work in future
                            if ($confirmation_date < $end_shift_date) {


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
                                ", [$insert->id, $confirmation_date, $request->employee_id]);



                                    $max_id = DB::table('hrm_employee_shift')
                                        ->where('valid', 1)
                                        ->where('start_date', '<', $confirmation_date)
                                        ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                            $query->select('id')
                                                ->from('hrm_employee_job_info')
                                                ->where('hrm_employee_id', $request->employee_id);
                                        })
                                        ->max('id');

                                        // dd($max_id);

                                    $pre_date = date('Y-m-d', strtotime($confirmation_date . ' -1 day'));


                                    DB::table('hrm_employee_shift')
                                    ->where('id', $max_id)
                                    ->update(['end_date' => $pre_date,'comment' =>'Update From Probation Confirm']);



                                    // $ ->end_date  = $max_pre_date;
                                    // $insert_shift->save();

                                    $min_id = DB::table('hrm_employee_shift')
                                        ->where('valid', 1)
                                        ->where('start_date', '>', $confirmation_date)
                                        ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                            $query->select('id')
                                                ->from('hrm_employee_job_info')
                                                ->where('hrm_employee_id', $request->employee_id);
                                        })
                                        ->min('id');

                                        DB::table('hrm_employee_shift')
                                        ->where('id', $min_id)
                                        ->update(['start_date' => $confirmation_date,'comment' =>'Update From Probation Confirm 2']);

                            }else{


                                    $pre_date = date('Y-m-d', strtotime($confirmation_date . ' -1 day'));
                                    DB::update("UPDATE hrm_employee_shift
                                                SET end_date= '$pre_date',
                                                comment='from employee Probation Confirm',
                                                users_id= $user_id,
                                                updated_at='$ldate'
                                                 WHERE  end_date is null
                                                AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");

                                    $insert_shift  = new HrmEmployeeShift;
                                    $insert_shift->hrm_employee_job_info_id = $insert->id;
                                    $insert_shift->hrm_shift_id             = $oldshift[0]->hrm_shift_id;
                                    $insert_shift->start_date               = $confirmation_date;
                                    $insert_shift->valid                    = 1;
                                    $insert_shift->comment                  = 'Probation Confirm';
                                    $insert_shift->users_id                 = Auth::user()->id;
                                    $insert_shift->save();
                            }






            $location_info = HrmLocation::where('id', $oldinfo->hrm_location_id)->first();

            if ($location_info->shifting_rules == 2) {

                DB::table('hrm_employee_shift_auto')
                    ->insert([
                        'hrm_shift_id' => $oldshift[0]->hrm_shift_id,
                        'start_date' => '2025-07-01',
                        'valid' => 1,
                        'hrm_employee_job_info_id' => $insert->id,
                        'users_id' => Auth::user()->id,
                        'created_at' => now()->toDateTimeString()
                    ]);
            }




            //Start Salary Related Information Insert

            if (config('module_config.payroll_module') == 1) {

                $hrm_employee_job_info_id     = $insert->id;
                $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first()->id;
                $gross_salary                 = $request->new_salary;
                $hrm_designation_id           = $insert->hrm_designation_id;
                $old_hrm_employee_job_info_id = $jobid;


                $getFunction = new CommonController();
                $getFunction->insert_salary_config($hrm_employee_job_info_id, $hrm_salary_grade_master_id, $gross_salary, $hrm_designation_id, $old_hrm_employee_job_info_id);
            }


            $insert_employee_activity  = new HrmEmployeeActivity;
            $insert_employee_activity->hrm_employee_job_info_id        = $insert->id;
            $insert_employee_activity->activity_date                   = $ldate;
            $insert_employee_activity->comment                         = 'Probation to Confirmation';
            $insert_employee_activity->users_id                        = auth()->id();
            $insert_employee_activity->activity                        = 1;
            $insert_employee_activity->hrm_employee_activity_status_id = 8;
            $insert_employee_activity->start_date                      = $confirmation_date;
            $insert_employee_activity->old_hrm_employee_job_info_id    = $jobid;
            $insert_employee_activity->save();



            DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                            WHERE end_date is null AND hrm_employee_job_info_id = $jobid");

            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0
                            WHERE id = $request->hrm_employee_job_info_id ");




            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }

        $request->session()->flash('alert-success', 'successfully updated!');
        return Redirect::to('probation_employee');
    }

    public function cost_to_the_company()
    {
        return view('dashboard.cost_to_the_company');
    }

    public function cost_to_the_company_location()
    {
        return view('dashboard.cost_to_the_company_location');
    }

    public function cost_to_the_company_category()
    {
        return view('dashboard.cost_to_the_company_category');
    }

    public function cost_to_the_companysummary()
    {
        return view('dashboard.cost_to_the_companysummary');
    }


    public function cost_to_the_company_data(Request $request)
    {
        $exgratia_bonus   = 0;
        $festival_bonus   = 0;
        $increment_amount = 0;
        $pf = 0;
        $hrm_location_id = $request->hrm_location_id;

        $dateto     = date('Y-m-d H:i:s');
        $month_from = date('Ym', strtotime(str_replace('/', '-', $request->month_from)));
        $month_to   = date('Ym', strtotime(str_replace('/', '-', $request->month_to)));

        $duration_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from)));
        $duration_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_to)));


        $condition = '';

        if (!empty($request->hrm_location_id)) {
            $condition = ' AND ap.hrm_location_id=' . $request->hrm_location_id;
        }

        if (!empty($request->hrm_category_id)) {
            $condition = $condition . ' AND a.hrm_category_id=' . $request->hrm_category_id;
        }

        $filter_condition = '';

        if (!empty($request->filter_name_id)) {

            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id', $request->filter_name_id)->first();

            $refIds = "SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $request->filter_name_id";

            switch ($getTableName->table_name) {
                // case 'hrm_religion':
                //     $filter_condition = " AND a.hrm_religion_id IN ($refIds)";
                //     break;
                // case 'hrm_blood_group':
                //     $filter_condition = " AND a.hrm_blood_group_id IN ($refIds)";
                //     break;
                // case 'hrm_marital_status':
                //     $filter_condition = " AND a.hrm_marital_status_id IN ($refIds)";
                //     break;
                case 'hrm_depertment':
                    $filter_condition = " AND a.hrm_depertment_id IN ($refIds)";
                    break;
                case 'hrm_designation':
                    $filter_condition = " AND a.hrm_designation_id IN ($refIds)";
                    break;
                case 'hrm_category':
                    $filter_condition = " AND a.hrm_category_id IN ($refIds)";
                    break;
                case 'hrm_plant':
                    $filter_condition = " AND a.hrm_plant_id IN ($refIds)";
                    break;
                case 'hrm_section':
                    $filter_condition = " AND a.hrm_section_id IN ($refIds)";
                    break;
                case 'hrm_shift':
                    $filter_condition = " AND a.id IN (SELECT
                                        aa.hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_shift aa
                                        WHERE aa.hrm_shift_id IN ($refIds) AND aa.hrm_employee_job_info_id = b.id
                                        AND id IN (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) ";
                    break;
            }
        }



        $cost_data = DB::SELECT("SELECT
                                        yyy.*,
                                        SUM( ifnull(yyy.gross_salary,0) + ifnull(yyy.two_festival_bonus,0)  + ifnull(yyy.pf_contribution,0)  + ifnull(yyy.transport_outOfPocket,0)  + ifnull(yyy.houseRent_allowance,0)  + ifnull(yyy.fixed_allowance,0) ) AS existing_cost
                                    FROM
                                        (SELECT
                                                aa.id,
                                                aa.employee_name,
                                                aa.tin,
                                                SUM(aa.TDS) as TDS,
                                                aa.designation_name,
                                                aa.depertment_name,
                                                aa.education_name,
                                                aa.joining_date,
                                                aa.confirmation_date,
                                                aa.section_name,
                                                aa.category_name,
                                                aa.priority,
                                                aa.location_name,
                                                ( SELECT
                                                        CONCAT(TIMESTAMPDIFF(YEAR, MAX(promotion_date), CURDATE()), '.', MOD(TIMESTAMPDIFF(MONTH, MAX(promotion_date), CURDATE()), 12), '')
                                                    FROM
                                                        hrm_last_promotion
                                                    WHERE
                                                        hrm_last_promotion.hrm_employee_id = aa.hrm_employee_id
                                                            AND hrm_last_promotion.promotion_date < '$dateto') last_promotion,
                                                    (SELECT
                                                       MAX(promotion_date)
                                                    FROM
                                                        hrm_last_promotion
                                                    WHERE
                                                        hrm_last_promotion.hrm_employee_id = aa.hrm_employee_id
                                                            AND hrm_last_promotion.promotion_date < '$dateto') last_promotion_date,
                                                CONCAT(TIMESTAMPDIFF(YEAR, aa.confirmation_date, CURDATE()), 'Y ', MOD(TIMESTAMPDIFF(MONTH, aa.confirmation_date, CURDATE()), 12), 'm') AS jobduration,
                                                SUM(aa.PF) AS pf_contribution,
                                                SUM( ifnull(aa.basic_salary,0) + ifnull(aa.medical_allowance,0) + ifnull(aa.house_rent,0) ) as gross_salary,
                                                (SUM(aa.bonusCalculate)*(TIMESTAMPDIFF(MONTH, '$duration_from', '$duration_to')+1)) AS two_festival_bonus,
                                                sum(aa.houseRent_allowance) as houseRent_allowance,
                                                sum(aa.fixed_allowance) as fixed_allowance,
                                                sum(aa.transport_outOfPocket) as transport_outOfPocket
                                        FROM
                                            (SELECT
                                                e.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                                IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                                IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                                IF(c.id = 15, b.actual_amount, 0) AS others_addition,
                                                IF(c.id = 3, b.actual_amount, 0) AS attendance_deduction,
                                                IF(c.id = 6, b.actual_amount, 0) AS PF,
                                                IF(c.id = 7, b.actual_amount, 0) AS TDS,
                                                IF(c.id = 16, b.actual_amount, 0) AS others_deduction,
                                                IF(c.id = 4, b.actual_amount, 0) AS loan_advance,
                                                IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                                IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                                IF(c.id in (11,8) , b.actual_amount, 0) AS transport_outOfPocket,
                                                Round(IF(c.id = 1, b.actual_amount, 0)*2/12,0) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name

                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_details b ON ap.id = b.pay_register_id
                                        JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                        JOIN hrm_salary_head_group d ON c.hrm_salary_head_group_id = d.id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id

                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for in(1,2)
                                        GROUP BY b.id
                                        UNION ALL
                                        SELECT
                                                e.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                0 As basic_salary,
                                                0 AS medical_allowance,
                                                (((ifnull(ap.amount,0) * ifnull(ap.total_present,0) )+ ifnull(b.due_adjust,0) )- ifnull(b.adv_adjust,0) ) AS house_rent,
                                                0 AS others_addition,
                                                0 AS attendance_deduction,
                                                0 AS PF,
                                                0 AS TDS,
                                                0 AS others_deduction,
                                                0 AS loan_advance,
                                                0 AS houseRent_allowance,
                                                0 AS fixed_allowance,
                                                0 AS transport_outOfPocket,
                                                -- Round(((ifnull(ap.day_of_month,0)* ifnull(ap.amount,0))*2*.30/12)) as bonusCalculate,
                                                round((a.basic_salary*2*.60/12)) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for = 3
                                        GROUP BY a.id
                                        ) aa
                                        GROUP BY aa.id) yyy
                                    GROUP BY yyy.id
                                    ORDER BY yyy.priority ASC,yyy.confirmation_date ASC");





        // // Current Data
        //  $cost_data = DB::SELECT("SELECT
        //                                  *,
        //                                  (aa.proposed_salary + aa.ExGratia_Bonus + aa.Revised_Structure + aa.two_festival_bonus + aa.pf + aa.transport_outOfPocket + aa.houseRent_allowance + aa.fixed_allowance + aa.insurance) AS Proposed_Cost_Company,
        //                                  (aa.proposed_salary + aa.ExGratia_Bonus + aa.Revised_Structure + aa.two_festival_bonus + aa.pf + aa.transport_outOfPocket + aa.houseRent_allowance + aa.fixed_allowance + aa.insurance) AS Existing_Cost_Company,
        //                                  ((proposed_salary - gross_salary) / gross_salary * 100) AS Percentage_On_Gross,
        //                                  0 AS Percentage_Cost_Company,
        //                                  '' AS Remarks
        //                              FROM
        //                                  (SELECT
        //                                      a.employee_name,
        //                                          e.alis as designation_name,
        //                                          p.education_name,
        //                                          f.joining_date,
        //                                          f.confirmation_date,
        //                                          ( SELECT
        //                                                  CONCAT(TIMESTAMPDIFF(YEAR, MAX(promotion_date), CURDATE()), '.', MOD(TIMESTAMPDIFF(MONTH, MAX(promotion_date), CURDATE()), 12), '')
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion,
        //                                              (SELECT
        //                                                 MAX(promotion_date)
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion_date,
        //                                          h.category_name as section_name,
        //                                          i.section_name as category_name,
        //                                          e.priority,
        //                                          CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Y(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' M(s) ') AS jobduration,
        //                                          b.basic_salary AS gross_salary,
        //                                          (b.basic_salary * $increment_amount) AS increment_salary,
        //                                          (b.basic_salary + (b.basic_salary * $increment_amount)) AS proposed_salary,
        //                                          (((b.basic_salary + (b.basic_salary * $increment_amount)) * 0.60 * $exgratia_bonus)/ 12) AS ExGratia_Bonus,
        //                                          0 AS Revised_Structure,
        //                                          (((b.basic_salary + (b.basic_salary * $increment_amount)) * $festival_bonus * 2) / 12) AS two_festival_bonus,
        //                                          ((b.basic_salary + (b.basic_salary * $increment_amount)) * 0.60 * $pf) AS pf,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (11 , 8)),0) AS transport_outOfPocket,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (9)),0) AS houseRent_allowance,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (12)),0) AS fixed_allowance,
        //                                          0 AS insurance
        //                                  FROM
        //                                      hrm_employee a
        //                                  JOIN hrm_employee_job_info b ON a.id = b.hrm_employee_id
        //                                      AND b.employee_activity = 1 AND b.hrm_employment_status_id NOT IN (3)
        //                                      AND b.hrm_location_id = 1
        //                                  JOIN hrm_location c ON b.hrm_location_id = c.id
        //                                  JOIN hrm_depertment d ON b.hrm_depertment_id = d.id
        //                                  JOIN hrm_designation e ON b.hrm_designation_id = e.id
        //                                  JOIN hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
        //                                  JOIN hrm_employment_status g ON b.hrm_employment_status_id = g.id
        //                                  JOIN hrm_category h ON b.hrm_category_id = h.id
        //                                  JOIN hrm_section i ON b.hrm_section_id = i.id
        //                                  JOIN hrm_education p ON p.id = a.hrm_education_id
        //                              UNION
        //                              SELECT
        //                                      a.employee_name,
        //                                          e.alis as designation_name,
        //                                          p.education_name,
        //                                          f.joining_date,
        //                                          f.confirmation_date,
        //                                          ( SELECT
        //                                                  CONCAT(TIMESTAMPDIFF(YEAR, MAX(promotion_date), CURDATE()), '.', MOD(TIMESTAMPDIFF(MONTH, MAX(promotion_date), CURDATE()), 12), '')
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion,
        //                                              (SELECT
        //                                                 MAX(promotion_date)
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion_date,
        //                                          h.category_name as section_name,
        //                                          i.section_name as category_name,
        //                                          e.priority,
        //                                          CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Y(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' M(s) ') AS jobduration,
        //                                          IF(b.hrm_location_id=2,11180,10920) AS gross_salary,
        //                                          0 AS increment_salary,
        //                                          IF(b.hrm_location_id=2,11180,10920) AS proposed_salary,
        //                                          0 AS ExGratia_Bonus,
        //                                          0 AS Revised_Structure,
        //                                          0 AS two_festival_bonus,
        //                                          0 AS pf,
        //                                          0 AS transport_outOfPocket,
        //                                          0 AS houseRent_allowance,
        //                                          0 AS fixed_allowance,
        //                                          0 AS insurance
        //                                  FROM
        //                                      hrm_employee a
        //                                  JOIN hrm_employee_job_info b ON a.id = b.hrm_employee_id
        //                                      AND b.employee_activity = 1 AND b.hrm_employment_status_id =3
        //                                      AND b.hrm_location_id = 1
        //                                  JOIN hrm_location c ON b.hrm_location_id = c.id
        //                                  JOIN hrm_depertment d ON b.hrm_depertment_id = d.id
        //                                  JOIN hrm_designation e ON b.hrm_designation_id = e.id
        //                                  JOIN hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
        //                                  JOIN hrm_employment_status g ON b.hrm_employment_status_id = g.id
        //                                  JOIN hrm_category h ON b.hrm_category_id = h.id
        //                                  JOIN hrm_section i ON b.hrm_section_id = i.id
        //                                  JOIN hrm_education p ON p.id = a.hrm_education_id) aa
        //                              ORDER BY aa.section_name , aa.priority");




        return json_encode(array('data' => $cost_data));
    }

    public function cost_to_the_company_category_data(Request $request)
    {
        $exgratia_bonus   = 0;
        $festival_bonus   = 0;
        $increment_amount = 0;
        $pf = 0;
        $hrm_location_id = $request->hrm_location_id;

        $dateto     = date('Y-m-d H:i:s');
        $month_from = date('Ym', strtotime(str_replace('/', '-', $request->month_from)));
        $month_to   = date('Ym', strtotime(str_replace('/', '-', $request->month_to)));

        $duration_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from)));
        $duration_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_to)));


        $condition = '';

        if (!empty($request->hrm_location_id)) {
            $condition = ' AND ap.hrm_location_id=' . $request->hrm_location_id;
        }

        if (!empty($request->hrm_category_id)) {
            $condition = $condition . ' AND a.hrm_category_id=' . $request->hrm_category_id;
        }


        $filter_condition = '';

        if (!empty($request->filter_name_id)) {

            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id', $request->filter_name_id)->first();

            $refIds = "SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $request->filter_name_id";

            switch ($getTableName->table_name) {
                // case 'hrm_religion':
                //     $filter_condition = " AND a.hrm_religion_id IN ($refIds)";
                //     break;
                // case 'hrm_blood_group':
                //     $filter_condition = " AND a.hrm_blood_group_id IN ($refIds)";
                //     break;
                // case 'hrm_marital_status':
                //     $filter_condition = " AND a.hrm_marital_status_id IN ($refIds)";
                //     break;
                case 'hrm_depertment':
                    $filter_condition = " AND a.hrm_depertment_id IN ($refIds)";
                    break;
                case 'hrm_designation':
                    $filter_condition = " AND a.hrm_designation_id IN ($refIds)";
                    break;
                case 'hrm_category':
                    $filter_condition = " AND a.hrm_category_id IN ($refIds)";
                    break;
                case 'hrm_plant':
                    $filter_condition = " AND a.hrm_plant_id IN ($refIds)";
                    break;
                case 'hrm_section':
                    $filter_condition = " AND a.hrm_section_id IN ($refIds)";
                    break;
                case 'hrm_shift':
                    $filter_condition = " AND a.id IN (SELECT
                                        aa.hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_shift aa
                                        WHERE aa.hrm_shift_id IN ($refIds) AND aa.hrm_employee_job_info_id = b.id
                                        AND id IN (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) ";
                    break;
            }
        }







        $cost_data = DB::SELECT("SELECT
                                        yyy.id,
                                        yyy.category_name,
                                        yyy.location_name,
                                        yyy.hrm_location_id,
                                        SUM(yyy.pf_contribution) as pf_contribution,
                                        SUM(yyy.gross_salary) as gross_salary,
                                        SUM(yyy.two_festival_bonus) as two_festival_bonus,
                                        SUM(yyy.houseRent_allowance) as houseRent_allowance,
                                        SUM(yyy.fixed_allowance) as fixed_allowance,
                                        SUM(yyy.transport_outOfPocket) as transport_outOfPocket,
                                        COUNT(yyy.hrm_employee_id) as NoOfEmployee,
                                        SUM( ifnull(yyy.gross_salary,0) + ifnull(yyy.two_festival_bonus,0)  + ifnull(yyy.pf_contribution,0)  + ifnull(yyy.transport_outOfPocket,0)  + ifnull(yyy.houseRent_allowance,0)  + ifnull(yyy.fixed_allowance,0) ) AS existing_cost
                                    FROM
                                        (SELECT
                                                aa.id,
                                                aa.category_name,
                                                aa.location_name,
                                                aa.hrm_employee_id,
                                                aa.hrm_location_id,
                                                SUM(aa.PF) AS pf_contribution,
                                                SUM( ifnull(aa.basic_salary,0) + ifnull(aa.medical_allowance,0) + ifnull(aa.house_rent,0) ) as gross_salary,
                                                (SUM(aa.bonusCalculate)*(TIMESTAMPDIFF(MONTH, '$duration_from', '$duration_to')+1)) AS two_festival_bonus,
                                                sum(aa.houseRent_allowance) as houseRent_allowance,
                                                sum(aa.fixed_allowance) as fixed_allowance,
                                                sum(aa.transport_outOfPocket) as transport_outOfPocket
                                        FROM
                                            (SELECT
                                                j.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                                IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                                IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                                IF(c.id = 15, b.actual_amount, 0) AS others_addition,
                                                IF(c.id = 3, b.actual_amount, 0) AS attendance_deduction,
                                                IF(c.id = 6, b.actual_amount, 0) AS PF,
                                                IF(c.id = 7, b.actual_amount, 0) AS TDS,
                                                IF(c.id = 16, b.actual_amount, 0) AS others_deduction,
                                                IF(c.id = 4, b.actual_amount, 0) AS loan_advance,
                                                IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                                IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                                IF(c.id in (11,8) , b.actual_amount, 0) AS transport_outOfPocket,
                                                Round(IF(c.id = 1, b.actual_amount, 0)*2/12,0) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name,
                                                l.id hrm_location_id
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_details b ON ap.id = b.pay_register_id
                                        JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                        JOIN hrm_salary_head_group d ON c.hrm_salary_head_group_id = d.id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for in(1,2)
                                        GROUP BY b.id
                                        UNION ALL
                                        SELECT
                                                j.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                0 As basic_salary,
                                                0 AS medical_allowance,
                                                (((ifnull(ap.amount,0) * ifnull(ap.total_present,0) )+ ifnull(b.due_adjust,0) )- ifnull(b.adv_adjust,0) ) AS house_rent,
                                                0 AS others_addition,
                                                0 AS attendance_deduction,
                                                0 AS PF,
                                                0 AS TDS,
                                                0 AS others_deduction,
                                                0 AS loan_advance,
                                                0 AS houseRent_allowance,
                                                0 AS fixed_allowance,
                                                0 AS transport_outOfPocket,
                                                -- Round(((ifnull(ap.day_of_month,0)* ifnull(ap.amount,0))*2*.30/12)) as bonusCalculate,
                                                round((a.basic_salary*2*.60/12)) as bonusCalculate,

                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name,
                                                l.id hrm_location_id
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for = 3
                                        GROUP BY a.id
                                        ) aa
                                        GROUP BY aa.hrm_employee_id) yyy
                                    GROUP BY yyy.location_name,yyy.category_name
                                    ORDER BY yyy.location_name ASC,yyy.category_name ASC");


        return json_encode(array('data' => $cost_data));
    }


    public function cost_to_the_company_location_data(Request $request)
    {
        $exgratia_bonus   = 0;
        $festival_bonus   = 0;
        $increment_amount = 0;
        $pf = 0;
        $hrm_location_id = $request->hrm_location_id;

        $dateto     = date('Y-m-d H:i:s');
        $month_from = date('Ym', strtotime(str_replace('/', '-', $request->month_from)));
        $month_to   = date('Ym', strtotime(str_replace('/', '-', $request->month_to)));

        $duration_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from)));
        $duration_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_to)));


        $condition = '';

        if (!empty($request->hrm_location_id)) {
            if($request->hrm_location_id=='999'){
                $condition  =  " AND ap.hrm_location_id in (SELECT id FROM hrm_location WHERE location_type=3 AND valid = 1) ";
                // $location_name = 'All Depot';
            }else{
                $condition = ' AND ap.hrm_location_id=' . $request->hrm_location_id;
            }
        }

        if (!empty($request->hrm_category_id)) {
            $condition = $condition . ' AND a.hrm_category_id=' . $request->hrm_category_id;
        }


        $filter_condition = '';

        if (!empty($request->filter_name_id)) {

            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id', $request->filter_name_id)->first();

            $refIds = "SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $request->filter_name_id";

            switch ($getTableName->table_name) {
                // case 'hrm_religion':
                //     $filter_condition = " AND a.hrm_religion_id IN ($refIds)";
                //     break;
                // case 'hrm_blood_group':
                //     $filter_condition = " AND a.hrm_blood_group_id IN ($refIds)";
                //     break;
                // case 'hrm_marital_status':
                //     $filter_condition = " AND a.hrm_marital_status_id IN ($refIds)";
                //     break;
                case 'hrm_depertment':
                    $filter_condition = " AND a.hrm_depertment_id IN ($refIds)";
                    break;
                case 'hrm_designation':
                    $filter_condition = " AND a.hrm_designation_id IN ($refIds)";
                    break;
                case 'hrm_category':
                    $filter_condition = " AND a.hrm_category_id IN ($refIds)";
                    break;
                case 'hrm_plant':
                    $filter_condition = " AND a.hrm_plant_id IN ($refIds)";
                    break;
                case 'hrm_section':
                    $filter_condition = " AND a.hrm_section_id IN ($refIds)";
                    break;
                case 'hrm_shift':
                    $filter_condition = " AND a.id IN (SELECT
                                        aa.hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_shift aa
                                        WHERE aa.hrm_shift_id IN ($refIds) AND aa.hrm_employee_job_info_id = b.id
                                        AND id IN (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) ";
                    break;
            }
        }






        $cost_data = DB::SELECT("SELECT
                                        yyy.id as hrm_location_id,
                                        yyy.location_name,
                                        SUM(yyy.pf_contribution) as pf_contribution,
                                        SUM(yyy.gross_salary) as gross_salary,
                                        SUM(yyy.two_festival_bonus) as two_festival_bonus,
                                        SUM(yyy.houseRent_allowance) as houseRent_allowance,
                                        SUM(yyy.fixed_allowance) as fixed_allowance,
                                        SUM(yyy.transport_outOfPocket) as transport_outOfPocket,
                                        COUNT(yyy.hrm_employee_id) as NoOfEmployee,
                                        SUM( ifnull(yyy.gross_salary,0) + ifnull(yyy.two_festival_bonus,0)  + ifnull(yyy.pf_contribution,0)  + ifnull(yyy.transport_outOfPocket,0)  + ifnull(yyy.houseRent_allowance,0)  + ifnull(yyy.fixed_allowance,0) ) AS existing_cost
                                    FROM
                                        (SELECT
                                                aa.id,
                                                aa.category_name,
                                                aa.location_name,
                                                aa.hrm_employee_id,
                                                SUM(aa.PF) AS pf_contribution,
                                                SUM( ifnull(aa.basic_salary,0) + ifnull(aa.medical_allowance,0) + ifnull(aa.house_rent,0) ) as gross_salary,
                                                (SUM(aa.bonusCalculate)*(TIMESTAMPDIFF(MONTH, '$duration_from', '$duration_to')+1)) AS two_festival_bonus,
                                                sum(aa.houseRent_allowance) as houseRent_allowance,
                                                sum(aa.fixed_allowance) as fixed_allowance,
                                                sum(aa.transport_outOfPocket) as transport_outOfPocket
                                        FROM
                                            (SELECT
                                                l.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                                IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                                IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                                IF(c.id = 15, b.actual_amount, 0) AS others_addition,
                                                IF(c.id = 3, b.actual_amount, 0) AS attendance_deduction,
                                                IF(c.id = 6, b.actual_amount, 0) AS PF,
                                                IF(c.id = 7, b.actual_amount, 0) AS TDS,
                                                IF(c.id = 16, b.actual_amount, 0) AS others_deduction,
                                                IF(c.id = 4, b.actual_amount, 0) AS loan_advance,
                                                IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                                IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                                IF(c.id in (11,8) , b.actual_amount, 0) AS transport_outOfPocket,
                                                Round(IF(c.id = 1, b.actual_amount, 0)*2/12,0) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_details b ON ap.id = b.pay_register_id
                                        JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                        JOIN hrm_salary_head_group d ON c.hrm_salary_head_group_id = d.id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for in(1,2)
                                        GROUP BY b.id
                                        UNION ALL
                                        SELECT
                                                l.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                0 As basic_salary,
                                                0 AS medical_allowance,
                                                (((ifnull(ap.amount,0) * ifnull(ap.total_present,0) )+ ifnull(b.due_adjust,0) )- ifnull(b.adv_adjust,0) ) AS house_rent,
                                                0 AS others_addition,
                                                0 AS attendance_deduction,
                                                0 AS PF,
                                                0 AS TDS,
                                                0 AS others_deduction,
                                                0 AS loan_advance,
                                                0 AS houseRent_allowance,
                                                0 AS fixed_allowance,
                                                0 AS transport_outOfPocket,
                                                -- Round(((ifnull(ap.day_of_month,0)* ifnull(ap.amount,0))*2*.30/12)) as bonusCalculate,
                                                round((a.basic_salary*2*.60/12)) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for = 3
                                        GROUP BY a.id
                                        ) aa
                                        GROUP BY aa.hrm_employee_id) yyy
                                    GROUP BY yyy.location_name
                                    ORDER BY yyy.location_name");


        // // Current Data
        //  $cost_data = DB::SELECT("SELECT
        //                                  *,
        //                                  (aa.proposed_salary + aa.ExGratia_Bonus + aa.Revised_Structure + aa.two_festival_bonus + aa.pf + aa.transport_outOfPocket + aa.houseRent_allowance + aa.fixed_allowance + aa.insurance) AS Proposed_Cost_Company,
        //                                  (aa.proposed_salary + aa.ExGratia_Bonus + aa.Revised_Structure + aa.two_festival_bonus + aa.pf + aa.transport_outOfPocket + aa.houseRent_allowance + aa.fixed_allowance + aa.insurance) AS Existing_Cost_Company,
        //                                  ((proposed_salary - gross_salary) / gross_salary * 100) AS Percentage_On_Gross,
        //                                  0 AS Percentage_Cost_Company,
        //                                  '' AS Remarks
        //                              FROM
        //                                  (SELECT
        //                                      a.employee_name,
        //                                          e.alis as designation_name,
        //                                          p.education_name,
        //                                          f.joining_date,
        //                                          f.confirmation_date,
        //                                          ( SELECT
        //                                                  CONCAT(TIMESTAMPDIFF(YEAR, MAX(promotion_date), CURDATE()), '.', MOD(TIMESTAMPDIFF(MONTH, MAX(promotion_date), CURDATE()), 12), '')
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion,
        //                                              (SELECT
        //                                                 MAX(promotion_date)
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion_date,
        //                                          h.category_name as section_name,
        //                                          i.section_name as category_name,
        //                                          e.priority,
        //                                          CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Y(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' M(s) ') AS jobduration,
        //                                          b.basic_salary AS gross_salary,
        //                                          (b.basic_salary * $increment_amount) AS increment_salary,
        //                                          (b.basic_salary + (b.basic_salary * $increment_amount)) AS proposed_salary,
        //                                          (((b.basic_salary + (b.basic_salary * $increment_amount)) * 0.60 * $exgratia_bonus)/ 12) AS ExGratia_Bonus,
        //                                          0 AS Revised_Structure,
        //                                          (((b.basic_salary + (b.basic_salary * $increment_amount)) * $festival_bonus * 2) / 12) AS two_festival_bonus,
        //                                          ((b.basic_salary + (b.basic_salary * $increment_amount)) * 0.60 * $pf) AS pf,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (11 , 8)),0) AS transport_outOfPocket,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (9)),0) AS houseRent_allowance,
        //                                          IFNULL((SELECT
        //                                                  SUM(bb.actual_amount)
        //                                              FROM
        //                                                  hrm_employee_salary aa
        //                                              JOIN hrm_employee_salary_details bb ON aa.id = bb.hrm_employee_salary_id
        //                                              WHERE
        //                                                  aa.hrm_employee_job_info_id = b.id
        //                                                      AND bb.hrm_salary_head_id IN (12)),0) AS fixed_allowance,
        //                                          0 AS insurance
        //                                  FROM
        //                                      hrm_employee a
        //                                  JOIN hrm_employee_job_info b ON a.id = b.hrm_employee_id
        //                                      AND b.employee_activity = 1 AND b.hrm_employment_status_id NOT IN (3)
        //                                      AND b.hrm_location_id = 1
        //                                  JOIN hrm_location c ON b.hrm_location_id = c.id
        //                                  JOIN hrm_depertment d ON b.hrm_depertment_id = d.id
        //                                  JOIN hrm_designation e ON b.hrm_designation_id = e.id
        //                                  JOIN hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
        //                                  JOIN hrm_employment_status g ON b.hrm_employment_status_id = g.id
        //                                  JOIN hrm_category h ON b.hrm_category_id = h.id
        //                                  JOIN hrm_section i ON b.hrm_section_id = i.id
        //                                  JOIN hrm_education p ON p.id = a.hrm_education_id
        //                              UNION
        //                              SELECT
        //                                      a.employee_name,
        //                                          e.alis as designation_name,
        //                                          p.education_name,
        //                                          f.joining_date,
        //                                          f.confirmation_date,
        //                                          ( SELECT
        //                                                  CONCAT(TIMESTAMPDIFF(YEAR, MAX(promotion_date), CURDATE()), '.', MOD(TIMESTAMPDIFF(MONTH, MAX(promotion_date), CURDATE()), 12), '')
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion,
        //                                              (SELECT
        //                                                 MAX(promotion_date)
        //                                              FROM
        //                                                  hrm_last_promotion
        //                                              WHERE
        //                                                  hrm_last_promotion.hrm_employee_id = a.id
        //                                                      AND hrm_last_promotion.promotion_date < '$dateto') last_promotion_date,
        //                                          h.category_name as section_name,
        //                                          i.section_name as category_name,
        //                                          e.priority,
        //                                          CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Y(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' M(s) ') AS jobduration,
        //                                          IF(b.hrm_location_id=2,11180,10920) AS gross_salary,
        //                                          0 AS increment_salary,
        //                                          IF(b.hrm_location_id=2,11180,10920) AS proposed_salary,
        //                                          0 AS ExGratia_Bonus,
        //                                          0 AS Revised_Structure,
        //                                          0 AS two_festival_bonus,
        //                                          0 AS pf,
        //                                          0 AS transport_outOfPocket,
        //                                          0 AS houseRent_allowance,
        //                                          0 AS fixed_allowance,
        //                                          0 AS insurance
        //                                  FROM
        //                                      hrm_employee a
        //                                  JOIN hrm_employee_job_info b ON a.id = b.hrm_employee_id
        //                                      AND b.employee_activity = 1 AND b.hrm_employment_status_id =3
        //                                      AND b.hrm_location_id = 1
        //                                  JOIN hrm_location c ON b.hrm_location_id = c.id
        //                                  JOIN hrm_depertment d ON b.hrm_depertment_id = d.id
        //                                  JOIN hrm_designation e ON b.hrm_designation_id = e.id
        //                                  JOIN hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
        //                                  JOIN hrm_employment_status g ON b.hrm_employment_status_id = g.id
        //                                  JOIN hrm_category h ON b.hrm_category_id = h.id
        //                                  JOIN hrm_section i ON b.hrm_section_id = i.id
        //                                  JOIN hrm_education p ON p.id = a.hrm_education_id) aa
        //                              ORDER BY aa.section_name , aa.priority");




        return json_encode(array('data' => $cost_data));
    }

    public function cost_to_the_company_summary_data(Request $request)
    {
        $exgratia_bonus   = 0;
        $festival_bonus   = 0;
        $increment_amount = 0;
        $pf = 0;
        $hrm_location_id = $request->hrm_location_id;

        $dateto        = date('Y-m-d H:i:s');
        $SelectedMonth2 = date('Y-m-d', strtotime("-3 months", strtotime($dateto)));
        $SelectedMonth1 = date('Y-m-d', strtotime("-1 months", strtotime($dateto)));

        $month_from    = date('Ym', strtotime(str_replace('/', '-', $SelectedMonth2)));
        $month_to      = date('Ym', strtotime(str_replace('/', '-', $SelectedMonth1)));

        $duration_from = date('Y-m-d', strtotime(str_replace('/', '-', $SelectedMonth1)));
        $duration_to   = date('Y-m-d', strtotime(str_replace('/', '-', $SelectedMonth1)));


        $condition = '';

        if (!empty($request->hrm_location_id)) {
            $condition = ' AND ap.hrm_location_id=' . $request->hrm_location_id;
        }

        if (!empty($request->hrm_category_id)) {
            $condition = $condition . ' AND a.hrm_category_id=' . $request->hrm_category_id;
        }


        $filter_condition = '';

        if (!empty($request->filter_name_id)) {

            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id', $request->filter_name_id)->first();

            $refIds = "SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $request->filter_name_id";

            switch ($getTableName->table_name) {
                // case 'hrm_religion':
                //     $filter_condition = " AND a.hrm_religion_id IN ($refIds)";
                //     break;
                // case 'hrm_blood_group':
                //     $filter_condition = " AND a.hrm_blood_group_id IN ($refIds)";
                //     break;
                // case 'hrm_marital_status':
                //     $filter_condition = " AND a.hrm_marital_status_id IN ($refIds)";
                //     break;
                case 'hrm_depertment':
                    $filter_condition = " AND a.hrm_depertment_id IN ($refIds)";
                    break;
                case 'hrm_designation':
                    $filter_condition = " AND a.hrm_designation_id IN ($refIds)";
                    break;
                case 'hrm_category':
                    $filter_condition = " AND a.hrm_category_id IN ($refIds)";
                    break;
                case 'hrm_plant':
                    $filter_condition = " AND a.hrm_plant_id IN ($refIds)";
                    break;
                case 'hrm_section':
                    $filter_condition = " AND a.hrm_section_id IN ($refIds)";
                    break;
                case 'hrm_shift':
                    $filter_condition = " AND a.id IN (SELECT
                                        aa.hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_shift aa
                                        WHERE aa.hrm_shift_id IN ($refIds) AND aa.hrm_employee_job_info_id = b.id
                                        AND id IN (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) ";
                    break;
            }
        }




        $cost_data = DB::SELECT("SELECT
                                        yyy.id as hrm_location_id,
                                        yyy.year_months,
                                        CONCAT(yyy.year_months,' - ', yyy.location_name) as location_name,
                                        COUNT(yyy.hrm_employee_id) as NoOfEmployee,
                                        SUM( ifnull(yyy.gross_salary,0) + ifnull(yyy.two_festival_bonus,0)  + ifnull(yyy.pf_contribution,0)  + ifnull(yyy.transport_outOfPocket,0)  + ifnull(yyy.houseRent_allowance,0)  + ifnull(yyy.fixed_allowance,0) ) AS existing_cost,
                                        SUM(yyy.gross_salary) as gross_salary,
                                        SUM(yyy.pf_contribution+yyy.two_festival_bonus+yyy.houseRent_allowance+yyy.fixed_allowance+yyy.transport_outOfPocket) as additional_payment,
                                        SUM(yyy.pf_contribution) as pf_contribution,
                                        SUM(yyy.two_festival_bonus) as two_festival_bonus,
                                        SUM(yyy.houseRent_allowance) as houseRent_allowance,
                                        SUM(yyy.fixed_allowance) as fixed_allowance,
                                        SUM(yyy.transport_outOfPocket) as transport_outOfPocket,
                                        yyy.YearMonth
                                    FROM
                                        (SELECT
                                                aa.id,
                                                aa.category_name,
                                                'All Location' as location_name,
                                                aa.hrm_employee_id,
                                                SUM(aa.PF) AS pf_contribution,
                                                SUM( ifnull(aa.basic_salary,0) + ifnull(aa.medical_allowance,0) + ifnull(aa.house_rent,0) ) as gross_salary,
                                                (SUM(aa.bonusCalculate)*(TIMESTAMPDIFF(MONTH, '$duration_from', '$duration_to')+1)) AS two_festival_bonus,
                                                sum(aa.houseRent_allowance) as houseRent_allowance,
                                                sum(aa.fixed_allowance) as fixed_allowance,
                                                sum(aa.transport_outOfPocket) as transport_outOfPocket,
                                                aa.year_months,
                                                aa.YearMonth
                                        FROM
                                            (SELECT
                                                l.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                                IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                                IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                                IF(c.id = 15, b.actual_amount, 0) AS others_addition,
                                                IF(c.id = 3, b.actual_amount, 0) AS attendance_deduction,
                                                IF(c.id = 6, b.actual_amount, 0) AS PF,
                                                IF(c.id = 7, b.actual_amount, 0) AS TDS,
                                                IF(c.id = 16, b.actual_amount, 0) AS others_deduction,
                                                IF(c.id = 4, b.actual_amount, 0) AS loan_advance,
                                                IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                                IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                                IF(c.id in (11,8) , b.actual_amount, 0) AS transport_outOfPocket,
                                                Round(IF(c.id = 1, b.actual_amount, 0)*2/12,0) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name,
                                                CONCAT(left(monthname(str_to_date(ap.hrm_month_id,'%m')),3),'-',ap.year_id) as year_months,
                                                CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) as YearMonth
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_details b ON ap.id = b.pay_register_id
                                        JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                        JOIN hrm_salary_head_group d ON c.hrm_salary_head_group_id = d.id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for in(1,2)
                                        GROUP BY b.id,CONCAT(left(monthname(str_to_date(ap.hrm_month_id,'%m')),3),'-',ap.year_id)
                                        UNION ALL
                                        SELECT
                                                l.id,
                                                a.hrm_employee_id,
                                                CONCAT(e.employee_name, ' | ', a.employee_code) AS employee_name,
                                                f.depertment_name,
                                                g.alis AS designation_name,
                                                ap.accounts_code,
                                                e.tin,
                                                h.joining_date,
                                                h.confirmation_date,
                                                ap.day_of_month,
                                                ap.total_present,
                                                (ap.day_of_month - ap.total_present) AS absent,
                                                0 As basic_salary,
                                                0 AS medical_allowance,
                                                (((ifnull(ap.amount,0) * ifnull(ap.total_present,0) )+ ifnull(b.due_adjust,0) )- ifnull(b.adv_adjust,0) ) AS house_rent,
                                                0 AS others_addition,
                                                0 AS attendance_deduction,
                                                0 AS PF,
                                                0 AS TDS,
                                                0 AS others_deduction,
                                                0 AS loan_advance,
                                                0 AS houseRent_allowance,
                                                0 AS fixed_allowance,
                                                0 AS transport_outOfPocket,
                                                -- Round(((ifnull(ap.day_of_month,0)* ifnull(ap.amount,0))*2*.30/12)) as bonusCalculate,
                                                round((a.basic_salary*2*.60/12)) as bonusCalculate,
                                                ap.payment_mode,
                                                ap.account_no,
                                                g.priority,
                                                i.section_name,
                                                i.id AS section_id,
                                                a.employee_code,
                                                j.category_name,
                                                k.education_name,
                                                l.location_name,
                                                CONCAT(left(monthname(str_to_date(ap.hrm_month_id,'%m')),3),'-',ap.year_id) as year_months,
                                                CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) as YearMonth
                                        FROM
                                            pay_register ap
                                        JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                        AND CONCAT(ap.year_id,LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$month_from' AND '$month_to'
                                        $condition
                                        $filter_condition
                                        JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                        JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                        JOIN hrm_depertment f ON a.hrm_depertment_id = f.id
                                        JOIN hrm_designation g ON a.hrm_designation_id = g.id
                                        JOIN hrm_employee_joining h ON a.hrm_employee_id = h.hrm_employee_id
                                        JOIN hrm_section i ON a.hrm_section_id = i.id
                                        JOIN hrm_category j ON a.hrm_category_id = j.id
                                        JOIN hrm_education k ON e.hrm_education_id = k.id
                                        JOIN hrm_location l ON a.hrm_location_id = l.id
                                        WHERE
                                            ap.salary_genarate_type <> 0
                                                AND ap.apply_for = 3
                                        GROUP BY a.id,CONCAT(left(monthname(str_to_date(ap.hrm_month_id,'%m')),3),'-',ap.year_id)
                                        ) aa
                                        GROUP BY aa.hrm_employee_id,aa.year_months) yyy
                                        GROUP BY yyy.year_months
                                        ORDER BY yyy.YearMonth
                                        ");



        return json_encode(array('data' => $cost_data));
    }

    public function common_dashboard(Request $request, $id)
    {
        // dd($request->all());
        $user_id      = auth()->id();
        $location     = $request->location;
        $department  = $request->department;
        $company_name = config('configaration.company_name');
        $address      = config('configaration.company_address');
        $title = '';


        $id = $request->id ?? $id;

        if ($id == 1) $title = "Location Wise Employee List";
        if ($id == 2) $title = "Department Wise Employee List";
        if ($id == 3) $title  = "Probation Employee List";
        if ($id == 4) $title = "Designation Wise Employee List";
        if ($id == 5) $title = "Previous Month Resign List";
        if ($id == 6) $title = "This Month New Join Employees";
        if ($id == 7) $title = "Category wise Employee List";
        if ($id == 8) $title = "Department wise Employee List";
        $data = '';
        if ($request->ajax()) {
            $where = '';

            if (!empty($location)) {
                $where = ' AND a.hrm_location_id = ' . $location;
            }
             if (!empty($department)) {

                $where .= ' AND a.hrm_depertment_id = ' . $department;
            }

            if ($id == 1) {
                $data = DB::SELECT("SELECT
                            b.id,
                            b.location_name AS description,
                            COUNT(a.id) AS count_employee
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_location b ON a.hrm_location_id = b.id
                                JOIN
                            user_location c ON c.hrm_location_id = b.id
                                AND c.users_id = $user_id
                                $where
                        WHERE
                            employee_activity = 1
                        GROUP BY b.id , b.location_name");
            }

            if ($id == 2) {
                $data = DB::SELECT("SELECT
                                cc.id,cc.depertment_name as description, COUNT(a.id) AS count_employee
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_location b ON a.hrm_location_id = b.id
                                    JOIN
                                hrm_depertment cc ON a.hrm_depertment_id=cc.id
                                    JOIN
                                user_location c ON c.hrm_location_id = b.id
                                    AND c.users_id = $user_id
                                    $where
                            WHERE
                                employee_activity = 1
                            GROUP BY cc.id,cc.depertment_name");
            }

            if ($id == 3) {
                $data   = DB::SELECT("SELECT
                                        cc.id,
                                        cc.depertment_name AS description,
                                        COUNT(a.id) AS count_employee
                                    FROM
                                        hrm_employee_job_info a
                                            JOIN
                                        hrm_location b ON a.hrm_location_id = b.id AND a.hrm_employment_status_id=1
                                            JOIN
                                        hrm_depertment cc ON a.hrm_depertment_id = cc.id
                                            JOIN
                                        user_location c ON c.hrm_location_id = b.id
                                            AND c.users_id = $user_id
                                            $where
                                    WHERE
                                        employee_activity = 1
                                    GROUP BY cc.id , cc.depertment_name");
            }

            if ($id == 4) {
                $data  = DB::SELECT("SELECT
                                    b.id,
                                    d.designation_name AS description,
                                    COUNT(a.id) AS count_employee
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id
                                        JOIN
                                    user_location c ON c.hrm_location_id = b.id AND c.users_id = $user_id
                                        JOIN
                                    hrm_designation d ON a.hrm_designation_id = d.id AND d.valid=1
                                    $where
                                WHERE
                                    employee_activity = 1
                                GROUP BY d.id , d.designation_name");
            }

            if ($id == 5) {
                $data  = DB::SELECT("SELECT
                                            d.id,
                                            d.location_name as description,
                                            COUNT(a.id) AS count_employee

                                        FROM
                                            hrm_employee_resignation aa
                                                JOIN
                                            hrm_employee_job_info a ON a.id = aa.hrm_employee_job_info_id
                                                AND aa.resingnation_status IN (2 , 4)
                                                AND aa.valid = 1
                                                AND aa.effective_date between '2023-01-01' AND '2023-01-31'
                                                AND aa.resingnation_status = 2
                                                JOIN
                                            hrm_employee c ON c.id = a.hrm_employee_id
                                                JOIN
                                            hrm_location d ON a.hrm_location_id = d.id
                                                JOIN
                                            hrm_depertment e ON a.hrm_depertment_id = e.id
                                                JOIN
                                            hrm_designation f ON a.hrm_designation_id = f.id
                                                JOIN
                                            user_location g ON a.hrm_location_id = g.hrm_location_id
                                                AND g.users_id = $user_id
                                              $where
                                            GROUP BY d.id , d.location_name");
            }

            if ($id == 6) {
                $data  = DB::SELECT("SELECT
                                            c.id,
                                            c.location_name AS description,
                                            ifnull(COUNT(a.id),0) AS count_employee
                                        FROM
                                            hrm_employee_job_info a
                                                JOIN
                                            hrm_employee_joining b ON a.hrm_employee_id = b.hrm_employee_id
                                                AND b.joining_date BETWEEN '2023-01-01' AND '2023-01-31'
                                                JOIN
                                            hrm_location c ON a.hrm_location_id = c.id
                                                JOIN
                                            user_location g ON a.hrm_location_id = g.hrm_location_id
                                                AND g.users_id =$user_id
                                            GROUP BY c.id");
            }

            if ($id == 7) {
                $data = DB::SELECT("SELECT
                        cc.id,
                        cc.category_name AS description,
                        COUNT(a.id) AS count_employee
                    FROM
                        hrm_employee_job_info a
                            JOIN
                        hrm_location b ON a.hrm_location_id = b.id
                            JOIN
                        hrm_category cc ON a.hrm_category_id = cc.id
                            JOIN
                        user_location c ON c.hrm_location_id = b.id
                            AND c.users_id = $user_id
                            $where
                    WHERE
                        employee_activity = 1
                    GROUP BY cc.id , cc.category_name
                ORder By cc.category_name");
            }

            if ($id == 8) {
                $data  = DB::SELECT("SELECT
                                    b.id,
                                    d.designation_name AS description,
                                    COUNT(a.id) AS count_employee,
                                    e.depertment_name AS department_name
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id
                                        JOIN
                                    user_location c ON c.hrm_location_id = b.id AND c.users_id = $user_id
                                        JOIN
                                    hrm_designation d ON a.hrm_designation_id = d.id AND d.valid=1
                                        JOIN
                                    hrm_depertment e ON a.hrm_depertment_id = e.id AND e.valid=1
                                    $where
                                WHERE
                                    employee_activity = 1
                                GROUP BY d.id , d.designation_name,a.hrm_depertment_id ORDER BY e.depertment_name,d.designation_name ASC");
            }

            // dd($data);
            return datatables()->of($data)
                ->make(true);
        }
        return view('dashboard.common_dashboard')
            ->with('title', $title)
            ->with('requestId', $id)
            ->with('company_name', $company_name)
            ->with('address', $address);
    }

    public function probation($id)
    {
        $user_id = auth()->id();

        $probation_employee = DB::SELECT("SELECT
                                            a.id,
                                            CONCAT(a.employee_name, ' | ', b.employee_code) AS employee_name,
                                            c.location_name,
                                            d.depertment_name,
                                            e.designation_name,
                                            a.contact_number,
                                            f.joining_date,
                                            a.Images,
                                            b.basic_salary,
                                            b.id AS hrm_employee_job_info_id,
                                            DATE_ADD(f.joining_date,
                                                INTERVAL h.period MONTH) AS probable_date
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                AND a.active_status = 1
                                                AND b.employee_activity = 1
                                                AND a.id = $id
                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                                JOIN
                                            hrm_employee_probation g ON a.id = g.hrm_employee_id
                                                JOIN
                                            hrm_probation_period h ON g.hrm_probation_period_id = h.id
                                                JOIN
                                            user_location i ON b.hrm_location_id = i.hrm_location_id
                                                AND i.users_id = $user_id ");

        return view('dashboard.probation_employee')
            ->with('probation_employee', $probation_employee);
    }

    public function attendanceSummaryByLocation()
    {

        $filter_date = date('Y-m-d', strtotime(str_replace('/', '-', request('filter_date'))));
        $user_id     = auth()->id();


        $parameter = " AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            '" . $filter_date . "' BETWEEN a.start_date AND a.end_date
                                                                AND a.end_date IS NOT NULL
                                                                AND a.hrm_employee_activity_status_id not in (7) UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.end_date IS NULL
                                                                AND '" . $filter_date . "' >= a.start_date AND a.hrm_employee_activity_status_id not in (7))";
        $data = DB::select("
                SELECT          aa.BranchName,
                                COUNT(aa.hrm_employee_id)as total_employee,
                                SUM(aa.ontime) ontime,
                                SUM(aa.late) late,
                                SUM(aa.absent) absent,
                                SUM(aa.early_out) early_out,
                                SUM(aa.total_leave) total_leave,
                                SUM(aa.osd) osd,
                                SUM(aa.holiday) holiday,
                                aa.hrm_location_id
                FROM(
                SELECT
                                            g.id as hrm_location_id,
                                            g.location_name as BranchName,
                                            a.hrm_employee_id,
                                            (CASE
                                                WHEN a.attendance_status = 2 THEN 1
                                                ELSE 0
                                            END) AS ontime,
                                            (CASE
                                                WHEN a.attendance_status = 1 THEN 1
                                                ELSE 0
                                            END) AS absent,
                                            (CASE
                                                WHEN a.attendance_status = 6 THEN 1
                                                ELSE 0
                                            END) AS late,
                                            (CASE
                                                WHEN a.attendance_status IN (3 , 4, 9, 10, 11, 12, 13) THEN 1
                                                ELSE 0
                                            END) AS total_leave,
                                            (CASE
                                                WHEN a.attendance_status = 5 THEN 1
                                                ELSE 0
                                            END) AS osd,
                                            (CASE
                                                WHEN a.attendance_status IN (7 , 8) THEN 1
                                                ELSE 0
                                            END) AS holiday,
                                            (CASE
                                                WHEN a.attendance_status = 14 THEN 1
                                                ELSE 0
                                            END) AS early_out
                                        FROM
                                            hrm_attendance a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id

                                                JOIN
                                            hrm_depertment c ON b.hrm_depertment_id = c.id
                                                JOIN
                                            hrm_designation d ON b.hrm_designation_id = d.id
                                                JOIN
                                            hrm_employee e ON a.hrm_employee_id = e.id
                                                JOIN
                                            hrm_attendance_status f ON a.attendance_status = f.id
                                                JOIN
                                            hrm_location g ON b.hrm_location_id = g.id
                                                JOIN
                                            hrm_employee_shift h ON b.id = h.hrm_employee_job_info_id
                                                JOIN
                                            hrm_shift i ON h.hrm_shift_id = i.id
                                                JOIN
                                            hrm_section j ON j.id = b.hrm_section_id
                                                AND (a.punche_date) = '$filter_date'
                                                $parameter
                                                AND h.id IN (SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    '$filter_date' BETWEEN start_date AND end_date
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    end_date IS NULL
                                                        AND '$filter_date' >= start_date)
                                            GROUP BY a.hrm_employee_id) aa
                                        GROUP BY aa.hrm_location_id

                                        ");




        return datatables()->of($data)
            ->addColumn('BranchName', function ($data) {
                return '
                    <a href="' . url('attendance_summary_data_location') . '?location=' . $data->hrm_location_id . '&filter_date=' . request('filter_date') . '">' . $data->BranchName . '</a>
                ';
            })
            ->rawColumns(['BranchName'])
            ->make(true);
    }

    public function attendanceSummaryDataLocation()
    {
        $filter_date = date('Y-m-d', strtotime(str_replace('/', '-', request('filter_date'))));
        $user_id     = auth()->id();

        $data['location'] = DB::table('hrm_location')
            ->select('id', 'location_name')
            ->where('id', request()->location)
            ->first();

        $data['company_name'] = config('configaration.company_name');
        $data['address']  = config('configaration.company_address');
        $data['title'] = '';

        if (request()->ajax()) {

            $location_con = '';

            if (request()->hrm_location_id) {
                $location_con = ' AND b.hrm_location_id=' . request()->hrm_location_id;
            }

            $parameter = " AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            '" . $filter_date . "' BETWEEN a.start_date AND a.end_date
                                                                AND a.end_date IS NOT NULL
                                                                AND a.hrm_employee_activity_status_id not in (7) UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.end_date IS NULL
                                                                AND '" . $filter_date . "' >= a.start_date AND a.hrm_employee_activity_status_id not in (7))";

            $data = DB::select("
                              SELECT          aa.location_name,
                                COUNT(aa.hrm_employee_id)as total_employee,
                                SUM(aa.ontime) ontime,
                                SUM(aa.late) late,
                                SUM(aa.absent) absent,
                                SUM(aa.early_out) early_out,
                                SUM(aa.total_leave) total_leave,
                                SUM(aa.osd) osd,
                                SUM(aa.holiday) holiday,
                                aa.hrm_location_id,
                                aa.department_id,
                                aa.DepartmentName
                FROM(
                SELECT
                                            g.id as hrm_location_id,
                                            g.location_name as location_name,
                                            a.hrm_employee_id,
                                            c.depertment_name as DepartmentName,
                                            c.id as department_id,
                                            (CASE
                                                WHEN a.attendance_status = 2 THEN 1
                                                ELSE 0
                                            END) AS ontime,
                                            (CASE
                                                WHEN a.attendance_status = 1 THEN 1
                                                ELSE 0
                                            END) AS absent,
                                            (CASE
                                                WHEN a.attendance_status = 6 THEN 1
                                                ELSE 0
                                            END) AS late,
                                            (CASE
                                                WHEN a.attendance_status IN (3 , 4, 9, 10, 11, 12, 13) THEN 1
                                                ELSE 0
                                            END) AS total_leave,
                                            (CASE
                                                WHEN a.attendance_status = 5 THEN 1
                                                ELSE 0
                                            END) AS osd,
                                            (CASE
                                                WHEN a.attendance_status IN (7 , 8) THEN 1
                                                ELSE 0
                                            END) AS holiday,
                                            (CASE
                                                WHEN a.attendance_status = 14 THEN 1
                                                ELSE 0
                                            END) AS early_out
                                        FROM
                                            hrm_attendance a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                $location_con
                                                JOIN
                                            hrm_depertment c ON b.hrm_depertment_id = c.id
                                                JOIN
                                            hrm_designation d ON b.hrm_designation_id = d.id
                                                JOIN
                                            hrm_employee e ON a.hrm_employee_id = e.id
                                                JOIN
                                            hrm_attendance_status f ON a.attendance_status = f.id
                                                JOIN
                                            hrm_location g ON b.hrm_location_id = g.id
                                                JOIN
                                            hrm_employee_shift h ON b.id = h.hrm_employee_job_info_id
                                                JOIN
                                            hrm_shift i ON h.hrm_shift_id = i.id
                                                JOIN
                                            hrm_section j ON j.id = b.hrm_section_id
                                                AND (a.punche_date) = '$filter_date'
                                                $parameter
                                                AND h.id IN (SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    '$filter_date' BETWEEN start_date AND end_date
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    end_date IS NULL
                                                        AND '$filter_date' >= start_date)
                                            GROUP BY a.hrm_employee_id) aa
                                        GROUP BY aa.department_id


                            ");



            return datatables()->of($data)
                ->addColumn('DepartmentName', function ($data) {
                    return '
                        <a href="' . url('attendance_summary_data_department') . '?department=' . $data->department_id . '&filter_date=' . request('filter_date') . '&hrm_location_id=' . $data->hrm_location_id . '&location_name=' . $data->location_name . '    ">' . $data->DepartmentName . '</a>
                    ';
                })
                ->rawColumns(['DepartmentName'])
                ->make(true);
        }

        return view('dashboard.attendance_summary_location', $data);
    }

    public function attendanceSummaryDataDepartment()
    {
        $user_id = auth()->id();
        $filter_date = date('Y-m-d', strtotime(str_replace('/', '-', request('filter_date'))));

        $data['department'] = DB::table('hrm_depertment')
            ->select('id', 'depertment_name')
            ->where('id', request()->department)
            ->first();

        $data['company_name'] = config('configaration.company_name');
        $data['address']  = config('configaration.company_address');
        $data['title'] = '';


        if (request()->ajax()) {

            $location_con = '';

            if (request()->hrm_location_id) {
                $location_con = ' AND b.hrm_location_id=' . request()->hrm_location_id;
            }


            $department_con = '';

            if (request()->hrm_depertment_id) {
                $department_con = ' AND b.hrm_depertment_id=' . request()->hrm_depertment_id;
            }


            $parameter = " AND a.hrm_employee_id IN (SELECT
                                                        b.hrm_employee_id
                                                    FROM
                                                        hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                    WHERE
                                                        '" . $filter_date . "' BETWEEN a.start_date AND a.end_date
                                                            AND a.end_date IS NOT NULL
                                                            AND a.hrm_employee_activity_status_id not in (7) UNION ALL SELECT
                                                       b.hrm_employee_id
                                                    FROM
                                                        hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                    WHERE
                                                        a.end_date IS NULL
                                                            AND '" . $filter_date . "' >= a.start_date AND a.hrm_employee_activity_status_id not in (7))";


            $data = DB::select("SELECT
                                            e.employee_name,
                                            b.employee_code,
                                            c.depertment_name,
                                            d.alis AS designation_name,
                                            d.priority,
                                            TIME_FORMAT(a.in_time, '%H:%i') AS in_time,
                                            TIME_FORMAT(a.out_time, '%H:%i') AS out_time,
                                            TIME_FORMAT(a.late_time, '%H:%i') AS late_time,
                                            TIMEDIFF(a.out_time, a.in_time) AS work_hour,
                                            TIME_FORMAT(a.overtime_time, '%H:%i') AS overtime_time,
                                            f.alies AS attendance_status,
                                            g.location_name,
                                            i.shift_name,
                                            j.section_name,
                                            c.depertment_name
                                        FROM
                                            hrm_attendance a
                                                JOIN
                                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id

                                                JOIN
                                            hrm_depertment c ON b.hrm_depertment_id = c.id
                                                JOIN
                                            hrm_designation d ON b.hrm_designation_id = d.id
                                                JOIN
                                            hrm_employee e ON a.hrm_employee_id = e.id
                                                JOIN
                                            hrm_attendance_status f ON a.attendance_status = f.id
                                                JOIN
                                            hrm_location g ON b.hrm_location_id = g.id
                                                JOIN
                                            hrm_employee_shift h ON b.id = h.hrm_employee_job_info_id
                                                JOIN
                                            hrm_shift i ON h.hrm_shift_id = i.id
                                                JOIN
                                            hrm_section j ON j.id = b.hrm_section_id
                                                AND (a.punche_date) = '$filter_date'
                                                $parameter
                                                AND h.id IN (SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    '$filter_date' BETWEEN start_date AND end_date
                                                        AND end_date IS NOT NULL UNION ALL SELECT
                                                    id
                                                FROM
                                                    hrm_employee_shift
                                                WHERE
                                                    end_date IS NULL
                                                        AND '$filter_date' >= start_date)
                                                $location_con
                                                $department_con
            ");





            return datatables()->of($data)
                ->make(true);
        }

        return view('dashboard.attendance_summary_department', $data);
    }
}
