<?php

namespace App\Http\Controllers\API;

use File;
use App\Models\User;
use App\Models\HrmEmployee;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HrmEmployeeProbation;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


use App\Models\HrmRedflag;
use App\Models\HrmReligion;

use App\Models\HrmBloodGroup;
use App\Models\HrmEmployeeShift;
use App\Models\HrmMaritalStatus;
use App\Models\HrmEmployeeJoining;
use App\Models\HrmProbationPeriod;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmUserLocation;
use App\Models\HrmDesignation;


use Auth;



class EmployeeInfoController extends Controller
{
    public function show(Request $request)
    {
        $success = false;

        $id = $request->employee_id;

        $employee = HrmEmployee::query()->find($id);

        if (empty($employee)) {
            $message = 'Employee not found';
            $error_code = 404;
        } else {
            try {
                $data['employee_info'] = DB::select("SELECT a.id,
                                    a.employee_name,
                                    a.nickname,
                                    if(a.gender = 1, 'Male', 'Female') as gender,
                                    a.tin,
                                    a.present_address,
                                    a.permanent_address,
                                    a.email,
                                    b.id as hrm_employee_job_info_id,
                                    c.location_name,
                                    d.depertment_name as department,
                                    e.designation_name as designation,
                                    b.basic_salary,
                                    b.employee_code,
                                    a.contact_number,
                                    f.joining_date,
                                    f.confirmation_date,
                                    concat(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Year(s) ' ,MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12),' Month(s) ') as jobduration,
                                    g.employment_status as employeestatus_name,
                                    h.category_name,
                                    i.section_name,
                                    b.overtime_status,
                                    j.employee_name as manage_by_name,
                                    l.shift_name,
                                    m.religion,
                                    n.marital_status,
                                    o.blood_group,
                                    p.education_name,
                                    q.plant_name,
                                    a.Images,
                                    b.insurance,
                                    s.period
                                    from hrm_employee a
                                    JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                                    AND b.id in (SELECT max(id) FROM hrm_employee_job_info WHERE hrm_employee_id = $id)
                                    AND a.id = $id
                                    JOIN hrm_location c On b.hrm_location_id=c.id
                                    JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                    JOIN hrm_designation e On b.hrm_designation_id=e.id
                                    JOIN hrm_employee_joining f on b.hrm_employee_id=f.hrm_employee_id
                                    JOIN hrm_employment_status g On b.hrm_employment_status_id=g.id
                                    JOIN hrm_category h on b.hrm_category_id=h.id
                                    JOIN hrm_section i on b.hrm_section_id=i.id
                                    JOIN hrm_employee j on b.hrm_manage_by_id=j.id
                                    JOIN hrm_employee_shift k on b.id=k.hrm_employee_job_info_id
                                    AND k.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                                    JOIN hrm_shift l on k.hrm_shift_id=l.id
                                    JOIN hrm_religion m on m.id=a.hrm_religion_id
                                    JOIN hrm_marital_status n on n.id=a.hrm_marital_status_id
                                    JOIN hrm_blood_group o on o.id=a.hrm_blood_group_id
                                    JOIN hrm_education p ON p.id=a.hrm_education_id
                                    JOIN hrm_plant q ON q.id=b.hrm_plant_id
                                    LEFT JOIN hrm_employee_probation r ON r.hrm_employee_id = a.id
                                    LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id")[0];

                $employee_job_info_id = $data['employee_info']->hrm_employee_job_info_id;

                // $data['salary_grade'] = '';


                // if(config('module_config.payroll_module') == 1) {
                //     $salary_info = HrmEmployeeSalary::where('hrm_employee_job_info_id', $employee_job_info_id)->first();

                //     if(!empty($salary_info->hrm_salary_grade_master_id)) {

                //         $data['salary_grade'] = DB::SELECT("SELECT
                //                                                 b.id,
                //                                                 b.grade_name
                //                                             FROM
                //                                                 hrm_salary_grade_master a
                //                                                     JOIN
                //                                                 hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
                //                                                     AND a.id=$salary_info->hrm_salary_grade_master_id")[0];
                //     } else {
                //         $data['salary_grade'] = DB::SELECT("SELECT 0 as id,'' as grade_name FROM hrm_salary_grade_master limit 1");
                //     }
                // }

                $success = true;
                $message = 'Success';
                $error_code = 200;
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function edit(Request $request)
    {
        $success = false;

        $id = $request->employee_id;

        $employee = HrmEmployee::query()->find($id);

        if (empty($employee)) {
            $message = 'Employee not found';
            $error_code = 404;
        } else {
            $data['edit_employee'] = DB::select("SELECT a.id,
                a.employee_name,
                a.nickname,
                a.gender,
                a.contact_number,
                a.father_name,
                a.mother_name,
                a.dob,
                a.email,
                a.nid,
                a.passport,
                a.present_address,
                a.permanent_address,
                a.Images,
                a.tin,
                b.id as hrm_employee_job_info_id,
                c.id as location_id,
                c.location_name,
                d.id as department_id,
                d.depertment_name,
                e.id as designation_id,
                e.designation_name,
                a.hrm_education_id,
                b.basic_salary,
                b.employee_code,
                f.joining_date,
                f.confirmation_date,
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
                a.hrm_education_id,
                p.education_name,
                q.id as hrm_plant_id,
                q.plant_name,
                b.insurance,
                s.period,
                r.hrm_probation_period_id
                from hrm_employee a
                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id and  a.active_status=1 and b.employee_activity=1 and a.id=$id
                Join hrm_location c On b.hrm_location_id=c.id
                JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                Join hrm_designation e On b.hrm_designation_id=e.id
                Join hrm_employee_joining f on b.hrm_employee_id=f.hrm_employee_id
                Join hrm_employment_status g On b.hrm_employment_status_id=g.id
                Join hrm_category h on b.hrm_category_id=h.id
                Join hrm_section i on b.hrm_section_id=i.id
                join hrm_employee j on b.hrm_manage_by_id=j.id
                Join hrm_employee_shift k on b.id=k.hrm_employee_job_info_id And k.end_date is null
                Join hrm_shift l on k.hrm_shift_id=l.id
                Join hrm_religion m on m.id=a.hrm_religion_id
                Join hrm_marital_status n on n.id=a.hrm_marital_status_id
                Join hrm_blood_group o on o.id=a.hrm_blood_group_id
                Join hrm_education p ON p.id=a.hrm_education_id
                JOIN hrm_plant q ON q.id=b.hrm_plant_id
                LEFT JOIN hrm_employee_probation r ON r.hrm_employee_id = a.id
                LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id
            ")[0];

            if (empty($data['edit_employee'])) {
                $error = "Sorry This Employee Information Can not be Editable";
            }

            // $data['employee_salary_grade'] = '';

            // if(config('module_config.payroll_module') == 1) {
            //     $hrm_employee_job_info_id   = $data['edit_employee']->hrm_employee_job_info_id;
            //     $hrm_salary_grade_master_id = HrmEmployeeSalary::where('hrm_employee_job_info_id',$hrm_employee_job_info_id)->first()->hrm_salary_grade_master_id;

            //     if(!empty($hrm_salary_grade_master_id)) {

            //         $data['employee_salary_grade'] = DB::SELECT("SELECT
            //                                     b.id,
            //                                     b.grade_name
            //                                 FROM
            //                                     hrm_salary_grade_master a
            //                                         JOIN
            //                                     hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
            //                                         AND a.id=$hrm_salary_grade_master_id")[0];
            //     } else {
            //         $data['employee_salary_grade'] = DB::SELECT("SELECT 0 as id,'' as grade_name FROM hrm_salary_grade_master limit 1")[0];
            //     }
            // }
        }

        // $data = array_merge($data['edit_employee'], $data['employee_salary_grade']);

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }




   public function bossWiseEmployeeList()
    {
            $success = false;
            $hrm_employee_id = auth()->user()->hrm_employee_id;

            $data = DB::select("SELECT 
                                        a.id,
                                        a.employee_name,
                                        a.contact_number,
                                        a.email,
                                        d.depertment_name,
                                        e.designation_name,
                                        f.joining_date,
                                        h.category_name
                                    FROM
                                        hrm_employee a
                                            JOIN
                                        hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                            AND a.active_status = 1
                                            AND b.employee_activity = 1
                                            AND b.hrm_manage_by_id = $hrm_employee_id
                                            JOIN
                                        hrm_location c ON b.hrm_location_id = c.id
                                            JOIN
                                        hrm_depertment d ON b.hrm_depertment_id = d.id
                                            JOIN
                                        hrm_designation e ON b.hrm_designation_id = e.id
                                            JOIN
                                        hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                            JOIN
                                        hrm_employment_status g ON b.hrm_employment_status_id = g.id
                                            JOIN
                                        hrm_category h ON b.hrm_category_id = h.id
                                            UNION 
                                        SELECT 
                                        a.id,
                                        a.employee_name,
                                        a.contact_number,
                                        a.email,
                                        d.depertment_name,
                                        e.designation_name,
                                        f.joining_date,
                                        h.category_name
                                    FROM
                                        hrm_employee a
                                            JOIN
                                        hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                            AND a.active_status = 1
                                            AND b.employee_activity = 1
                                            AND b.hrm_employee_id = $hrm_employee_id
                                            JOIN
                                        hrm_location c ON b.hrm_location_id = c.id
                                            JOIN
                                        hrm_depertment d ON b.hrm_depertment_id = d.id
                                            JOIN
                                        hrm_designation e ON b.hrm_designation_id = e.id
                                            JOIN
                                        hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                            JOIN
                                        hrm_employment_status g ON b.hrm_employment_status_id = g.id
                                            JOIN
                                        hrm_category h ON b.hrm_category_id = h.id");



        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }






    public function newEmployeeCreate(Request $request)
    {
        $success = false;

            $validator = Validator::make($request->all(), [
                'employee_name'     => 'required',
                'present_address'   => 'required',
                'permanent_address' => 'required',
                'contact_number'    => 'required',
                'gender'            => 'required',
                'blood_group_id'       => 'required',
                'date_of_birth'     => 'required',
                'nid'               => 'required',
                'depertment'        => 'required|exists:hrm_depertment,id',
                'designation'       => 'required|exists:hrm_designation,id',
                'job_location'      => 'required|exists:hrm_location,id',
                'working_shift'     => 'required|exists:hrm_shift,id',
                'overtime'          => 'required',
                'manage_by'         => 'required|exists:hrm_employee,id',
                'employeestatus'    => 'required|exists:hrm_employment_status,id',
                // 'basic_salary'      => 'required',
            ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Form validation failed..!';
            $error_code = 422;
        } else {


                $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date)));
                $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->confirmation_date)));
                $currentdate        = date('Y-m-d');
                $dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth)));

                DB::beginTransaction();
                try {

                        $employee = new HrmEmployee;
                        $employee->employee_name          = trim($request->employee_name);
                        $employee->nickname               = trim($request->nickname);
                        $employee->contact_number         = trim($request->contact_number);
                        $employee->father_name            = trim($request->father_name);
                        $employee->mother_name            = trim($request->mothers_name);
                        $employee->dob                    = $dob;
                        $employee->email                  = trim($request->email);
                        $employee->nid                    = trim($request->nid);
                        $employee->tin                    = trim($request->tin);
                        $employee->passport               = trim($request->passport);
                        $employee->present_address        = trim($request->present_address);
                        $employee->permanent_address      = trim($request->permanent_address);
                        $employee->gender                 = $request->gender;
                        $employee->hrm_blood_group_id     = $request->blood_group_id;
                        $employee->hrm_religion_id        = $request->religion_id;
                        $employee->hrm_marital_status_id  = $request->marital_status_id;
                        $employee->hrm_education_id       = $request->hrm_education_id;
                        // $employee->Images                 = '';
                        $employee->active_status          = 1;
                        $employee->users_id               = auth()->id();
                        $employee->save();

                        $employeejobinfo     = new HrmEmployeeJobInfo;
                        $employeejobinfo->hrm_employee_id            = $employee->id;
                        $employeejobinfo->employee_code              = $request->employee_code;
                        $employeejobinfo->hrm_depertment_id          = $request->depertment;
                        $employeejobinfo->hrm_designation_id         = $request->designation;
                        $employeejobinfo->hrm_location_id            = $request->job_location;
                        $employeejobinfo->overtime_status            = $request->overtime;
                        $employeejobinfo->hrm_manage_by_id           = $request->manage_by;
                        $employeejobinfo->hrm_employment_status_id   = $request->employeestatus;
                        $employeejobinfo->basic_salary               = 0;
                        $employeejobinfo->hrm_category_id            = $request->category;
                        $employeejobinfo->employee_shift_status      = 1;
                        $employeejobinfo->employee_activity          = 1;
                        $employeejobinfo->hrm_section_id             = $request->section;
                        $employeejobinfo->insurance                  = $request->insurance;
                        $employeejobinfo->hrm_plant_id               = 1;
                        $employeejobinfo->users_id                   = auth()->id();
                        $employeejobinfo->save();


                        $insert_shift  = new HrmEmployeeShift;
                        $insert_shift->hrm_employee_job_info_id = $employeejobinfo->id;
                        $insert_shift->hrm_shift_id             = $request->working_shift;
                        $insert_shift->start_date               = $joining_date;
                        $insert_shift->valid                    = 1;
                        $insert_shift->comment                  = 'NewJoin';
                        $insert_shift->users_id                 = Auth::user()->id;
                        $insert_shift->save();


                        $insert_joining  = new HrmEmployeeJoining;
                        $insert_joining->hrm_employee_id    = $employee->id;
                        $insert_joining->joining_date       = $joining_date;
                        if($request->employeestatus==1){}else{$insert_joining->confirmation_date  = $confirmation_date;}
                        $insert_joining->save();




                        $insert_activity      = new HrmEmployeeActivity;
                        $insert_activity->hrm_employee_job_info_id   = $employeejobinfo->id;
                        $insert_activity->activity_date              = $currentdate;
                        $insert_activity->comment                    = 'Join';
                        $insert_activity->users_id                   = Auth::user()->id;
                        $insert_activity->activity                   = 3;
                        $insert_activity->hrm_employee_activity_status_id = 1;
                        $insert_activity->start_date                 = $joining_date;
                        $insert_activity->save();



                            if ($request->employeestatus==1){

                                $insert_probation      = new HrmEmployeeProbation;
                                $insert_probation->hrm_employee_id          = $employee->id;
                                $insert_probation->hrm_probation_period_id  = $request->probation_period;
                                $insert_probation->users_id                 = Auth::user()->id;
                                $insert_probation->save();

                            }


                        $insert_card     = new HrmEmployeeCardCode;
                        $insert_card->card_code                = 'App'.$employee->id;
                        $insert_card->device_id                = 1;
                        $insert_card->hrm_employee_job_info_id = $employeejobinfo->id;
                        $insert_card->save();

                        $HrmDesignation = HrmDesignation::findOrFail($request->designation);

                        $userdata = User::create([
                            'name'              => trim($request->employee_name),
                            'email'             => trim($request->email),
                            'designation'       => $HrmDesignation->designation_name,
                            'password'          => bcrypt($request->password),
                            'hrm_employee_id'   => $employee->id,
                        ]);
                        
            
                        $insertLocation  = new HrmUserLocation;
                        $insertLocation->users_id          = $userdata->id;
                        $insertLocation->hrm_location_id   = $request->job_location;
                        $insertLocation->default_location  = 1;
                        $insertLocation->save();





                    DB::commit();

                    $success = true;
                    $message = 'Sucessfully New Employee Joined';
                    $error_code = 200;

                }catch (\Exception $e) {
                    DB::rollback();
                    $error = $e->getMessage();
                    $message = 'Something went wrong..!';
                    $error_code = 500; 
                }

        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }




    public function update(Request $request)
    {
        $success = false;

        if($request->emp_info == 1) {
            $validator = Validator::make($request->all(), [
                'employee_name'     => 'required',
                'present_address'   => 'required',
                'permanent_address' => 'required',
                'contact_number'    => 'required',
                'gender'            => 'required',
                'blood_group_id'       => 'required',
                'date_of_birth'     => 'required',
                'nid'               => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'depertment'        => 'required|exists:hrm_depertment,id',
                'designation'       => 'required|exists:hrm_designation,id',
                'job_location'      => 'required|exists:hrm_location,id',
                'working_shift'     => 'required|exists:hrm_shift,id',
                'overtime'          => 'required',
                'manage_by'         => 'required|exists:hrm_employee,id',
                'employeestatus'    => 'required|exists:hrm_employment_status,id',
                'basic_salary'      => 'required',
            ]);
        }

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Form validation failed..!';
            $error_code = 422;
        } else {
            $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date)));
            $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->confirmation_date)));

            $employee = HrmEmployee::find($request->employee_id);

            $dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth)));
            $employeeImage = DB::select("SELECT Images from hrm_employee  Where id = $request->employee_id");
            $fileName = $employeeImage[0]->Images;

            if($request->emp_info == 1) {
                DB::beginTransaction();
                try {
                    $employee->employee_name          = trim($request->employee_name);
                    $employee->nickname               = trim($request->nickname);
                    $employee->contact_number         = trim($request->contact_number);
                    $employee->father_name            = trim($request->father_name);
                    $employee->mother_name            = trim($request->mothers_name);
                    $employee->dob                    = $dob;
                    $employee->email                  = trim($request->email);
                    $employee->nid                    = trim($request->nid);
                    $employee->tin                    = trim($request->tin);
                    $employee->passport               = trim($request->passport);
                    $employee->present_address        = trim($request->present_address);
                    $employee->permanent_address      = trim($request->permanent_address);
                    $employee->gender                 = $request->gender;
                    $employee->hrm_blood_group_id     = $request->blood_group_id;
                    $employee->hrm_religion_id        = $request->religion_id;
                    $employee->hrm_marital_status_id  = $request->marital_status_id;
                    $employee->hrm_education_id       = $request->hrm_education_id;
                    $employee->Images                 = $fileName;
                    $employee->active_status          = 1;
                    $employee->users_id               = auth()->id();
                    $employee->save();

                    DB::commit();

                    $success = true;
                    $message = 'Personal data has been saved successfully..!';
                    $error_code = 200;
                } catch (\Exception $e) {
                    DB::rollback();

                    $error = $e->getMessage();
                    $message = 'Something went wrong..!';
                    $error_code = 500;
                }
            }

            if($request->emp_info == 2) {
                DB::beginTransaction();
                try {
                    $employeejobinfo = HrmEmployeeJobInfo::query()
                        ->where('hrm_employee_id', $request->employee_id)
                        ->where('employee_activity', 1)
                        ->first();

                    $employeejobinfo = HrmEmployeeJobInfo::find($employeejobinfo->id);

                    $employeejobinfo->hrm_employee_id            = $request->employee_id;
                    $employeejobinfo->employee_code              = $request->employee_code;
                    $employeejobinfo->hrm_depertment_id          = $request->depertment;
                    $employeejobinfo->hrm_designation_id         = $request->designation;
                    $employeejobinfo->hrm_category_id            = $request->category;
                    $employeejobinfo->hrm_employment_status_id   = $request->employeestatus;
                    $employeejobinfo->hrm_manage_by_id           = $request->manage_by;
                    $employeejobinfo->employee_shift_status      = 1;
                    $employeejobinfo->overtime_status            = $request->overtime;
                    $employeejobinfo->employee_activity          = 1;
                    $employeejobinfo->basic_salary               = $request->basic_salary;
                    $employeejobinfo->hrm_location_id            = $request->job_location;
                    $employeejobinfo->hrm_section_id             = $request->section;
                    $employeejobinfo->hrm_plant_id               = $request->plant_name;
                    $employeejobinfo->insurance                  = $request->insurance;
                    $employeejobinfo->users_id                   = auth()->id();
                    $employeejobinfo->save();

                    if($request->employeestatus == 1){
                        DB::UPDATE("UPDATE hrm_employee_joining SET joining_date = '$joining_date', confirmation_date = null WHERE hrm_employee_id = $request->employee_id");
                    } else {
                        DB::UPDATE("UPDATE hrm_employee_joining SET joining_date = '$joining_date', confirmation_date = '$confirmation_date' WHERE hrm_employee_id = $request->employee_id");
                    }

                    if ($request->employeestatus == 1) {
                        DB::table('hrm_employee_probation')->where('hrm_employee_id', '=', $request->employee_id)->delete();

                        $insert_probation      = new HrmEmployeeProbation;
                        $insert_probation->hrm_employee_id          = $request->employee_id;
                        $insert_probation->hrm_probation_period_id  = $request->probation_period;
                        $insert_probation->users_id                 = auth()->id();

                        $insert_probation->save();
                    }

                    $jobid = $request->hrm_employee_job_info_id;

                    DB::update("UPDATE  hrm_employee_activity SET start_date='$joining_date' WHERE hrm_employee_job_info_id =$jobid AND hrm_employee_activity_status_id=1");

                    if(config('module_config.payroll_module') == 1) {
                        $salary_grade_master  = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first();

                        $update_salary_master  = HrmEmployeeSalary::where('hrm_employee_job_info_id', $jobid)->first();
                        $update_salary_master  = HrmEmployeeSalary::find($update_salary_master->id);
                        $update_salary_master->hrm_salary_grade_master_id = $salary_grade_master->id;
                        $update_salary_master->save();
                    }

                    DB::commit();

                    $success = true;
                    $message = 'Official data has been saved successfully..!';
                    $error_code = 200;
                } catch (\Exception $e) {
                    DB::rollback();

                    $error = $e->getMessage();
                    $message = 'Something went wrong..!';
                    $error_code = 500;
                }
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function securitySettings(Request $request)
    {
        $success = false;
        $message = '';

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrm_employee,id',
            'new_password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            $error = collect($validator->errors())->flatten();
            $message = $error[0];
            $error_code = 422;
        } else {
            try {

                $message = 'Successfully changed your ';

                if ($request->filled('new_password') && $request->filled('current_password')) {
                    $user = User::query()->firstWhere('hrm_employee_id', $request->employee_id);

                    if (! Hash::check($request->current_password, $user->password)) {
                        $message = 'The provided password does not match your current password.';
                        $error_code = 422;

                        return response()->json([
                            'success' => $success,
                            'message' => $message ?? '',
                            'data' => $data ?? '',
                            'error' => $error ?? '',
                            'error_code' => $error_code ?? ''
                        ]);
                    } else {
                        $user->forceFill([
                            'password' => Hash::make($request->new_password),
                        ])->save();

                        $message = $message.' password ';
                    }
                }

                $employee = HrmEmployee::query()->findOrFail($request->employee_id);

                if (!empty($request['image'])) {
                    if($employee->Images){
                        if (file_exists(public_path('employee_image/'.$employee->Images))) {
                            unlink(public_path('employee_image/'.$employee->Images));
                        }
                    }

                    $image_parts = explode(";base64,", $request['image']);

                    if (count($image_parts) > 1) {
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type = $image_type_aux[1];
                        $image_base64 = base64_decode($image_parts[1]);
                    } else {
                        $image_base64 = base64_decode($image_parts[0]);
                        $image_type = 'png';
                    }

                    $fileName = uniqid() . '.'.$image_type;
                    $file = public_path().'/employee_image/'.$fileName;
                    file_put_contents($file, $image_base64);
                    $employee->Images = $fileName;
                    $employee->save();

                    $message = $message.'- photo ';
                }

                $success = true;
                $error_code = 200;
            } catch (\Exception $e) {
                $success = false;
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }
}
