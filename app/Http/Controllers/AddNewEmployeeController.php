<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HrmEmployee;
use Illuminate\Http\Request;
use App\Models\HrmUserLocation;
use App\Models\HrmEmployeeDraft;
use App\Models\HrmEmployeeShift;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmProbationPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeJobApproval;
use App\Models\HrmEmployeeProbation;
use App\Models\HrmSalaryGradeMaster;
use App\Models\HrmEmployeeJobInfoDraft;
use App\Models\HrmEmployeeJoining;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeSalaryDetails;
use App\Models\HrmLocation;
use Illuminate\Support\Facades\Validator;

class AddNewEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('add_new_employee.index');
    }

    public function draftEmployeeList()
    {
        // $employee = DB::select("SELECT
        //         a.id,
        //         a.employee_name,
        //         a.contact_number,
        //         a.email,
        //         b.religion,
        //         IF(a.gender = 1, 'Male', 'Female') AS gender,
        //         Images
        //     FROM
        //         hrm_employee_draft a
        //             JOIN
        //         hrm_religion b ON a.hrm_religion_id = b.id AND a.active_status = 1
        // ");

        $user_id = auth()->id();

        $employee = DB::select("SELECT
                                    a.id,
                                    a.employee_name,
                                    a.contact_number,
                                    a.email,
                                    b.religion,
                                    IF(a.gender = 1, 'Male', 'Female') AS gender,
                                    a.Images,
                                    1 AS status_value,
                                    'Draft' AS status
                                FROM hrm_employee_draft a
                                JOIN hrm_religion b
                                    ON a.hrm_religion_id = b.id
                                WHERE a.active_status = 1

                                UNION

                                SELECT
                                    a.id,
                                    a.employee_name,
                                    a.contact_number,
                                    a.email,
                                    r.religion,
                                    IF(a.gender = 1, 'Male', 'Female') AS gender,
                                    a.Images,
                                    2 AS status_value,
                                    'Waiting for Approval' AS status
                                FROM hrm_employee a
                                JOIN hrm_employee_job_info b
                                    ON a.id = b.hrm_employee_id
                                JOIN hrm_religion r
                                    ON a.hrm_religion_id = r.id
                                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id
                                AND g.users_id = $user_id
                                AND b.id IN
                                    (SELECT hrm_employee_job_info_id FROM hrm_employee_job_approval WHERE action_type = 1 and forward = 1 )
                                WHERE a.active_status = 1
                                AND b.employee_activity = 10;");

        return json_encode(array('data' => $employee));
    }

    public function create()
    {
        $probations = HrmProbationPeriod::all();

        $role_lists = DB::select("SELECT id, name as display_name from roles");

        $employee_info = null;

        return view('add_new_employee.create_edit', compact('probations', 'role_lists', 'employee_info'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $status = false;

        $rules = [];

        if ($request->step === 'one') {
            $rules = [
                'employee_name' => 'required|string',
                'nickname' => 'required|string',
                'present_address' => 'required|string',
                'permanent_address' => 'required|string',
                'contact_number' => 'required|string',
                'email' => 'required|string|email',
                'religion' => 'required',
                'marital_status' => 'required',
                'blood_group' => 'required',
                'hrm_education_id' => 'required',
            ];
        } elseif ($request->step === 'two') {
            $rules = [
                'employee_code' => 'required|string',
                'depertment' => 'required',
                'designation' => 'required',
                'category' => 'required',
                'job_location' => 'required',
                'section' => 'required',
                'working_shift' => 'required',
                'plant_name' => 'required',
                'manage_by' => 'required',
                'basic_salary' => 'required',
                'employeestatus' => 'required',
            ];
        } elseif ($request->step === 'three') {
            $rules = [
                'card_code' => 'required',
            ];
        } elseif ($request->step === 'final' && $request->skipUserInfo == null) {
            $rules = [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'userrole' => 'required',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = $error;
        } else {
            try {





        if(is_null($request->hrm_employee_id)){



                if ($request->step === 'one') {
                    $dob = $request->date_of_birth ? date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth))) : null;

                    $fileUrl = null;

                    if ($request->hasFile('images')) {
                        $file = $request->file('images');
                        $var_path  = public_path('employee_image');
                        $ext = $file->getClientOriginalExtension();
                        $hash = $this->generateRandomString();
                        $fileName = $hash.'.'.$ext;
                        $file->move($var_path, $fileName);
                        $fileUrl = $fileName;
                    }

                    $employeeDraft = HrmEmployeeDraft::updateOrCreate([
                        'id' => $request->employee_id
                    ], [
                        'employee_name'          => $request->employee_name,
                        'nickname'               => $request->nickname,
                        'contact_number'         => $request->contact_number,
                        'father_name'            => $request->father_name,
                        'mother_name'            => $request->mother_name,
                        'dob'                    => $dob,
                        'email'                  => $request->email,
                        'nid'                    => $request->nid,
                        'tin'                    => $request->tin,
                        'passport'               => '',
                        'present_address'        => $request->present_address,
                        'permanent_address'      => $request->permanent_address,
                        'gender'                 => $request->gender,
                        'hrm_blood_group_id'     => $request->blood_group,
                        'hrm_religion_id'        => $request->religion,
                        'hrm_marital_status_id'  => $request->marital_status,

                        'hrm_education_id'       => $request->hrm_education_id,
                        'Images'                 => $fileUrl,
                        'active_status'          => 1,
                        'users_id'               => auth()->id(),
                    ]);

                    $status = true;
                    $message = "Employee's personal data saved";
                    $employee_draft_id = $employeeDraft->id;
                }

                //-------------------------------------------------------------------
                if ($request->step === 'two') {
                    $joining_date = $request->joining_date ? date('Y-m-d', strtotime($request->joining_date)) : null;

                    $confirmation_date = $request->confirmation_date ? date('Y-m-d', strtotime($request->confirmation_date)) : null;

                    $isEmployeeJoiningApproval = (bool) HrmLocation::query()
                        ->where('id', $request->job_location)
                        ->first()->is_employee_joining_approval;
                    // dd()
                    session()->put('isEmployeeJoiningApproval', $isEmployeeJoiningApproval);

                    DB::beginTransaction();

                    HrmEmployeeJobInfoDraft::updateOrCreate([
                        'hrm_employee_draft_id' => $request->employee_id
                    ], [
                        'employee_code'             => $request->employee_code,
                        'official_contact_no'       => $request->official_contact_no,
                        'official_email'            => $request->official_email,
                        'hrm_depertment_id'         => $request->depertment,
                        'hrm_designation_id'        => $request->designation,
                        'hrm_category_id'           => $request->category,
                        'hrm_employment_status_id'  => $request->employeestatus,
                        'hrm_manage_by_id'          => $request->manage_by,
                        'employee_shift_status'     => 1,
                        'overtime_status'           => $request->overtime,
                        'employee_activity'         => $isEmployeeJoiningApproval ? 10 : 1,
                        'basic_salary'              => $request->basic_salary,
                        'hrm_location_id'           => $request->job_location,
                        'hrm_section_id'            => $request->section,
                        'hrm_plant_id'              => $request->plant_name,
                        'insurance'                 => $request->insurance,
                        'working_shift_id'          => $request->working_shift,
                        'salary_grade_id'           => $request->salary_grade,
                        'joining_date'              => $joining_date,
                        'confirmation_date'         => $request->employeestatus == 1 ? null : $confirmation_date,
                        'probation_period'          => $request->employeestatus == 1 ? $request->probation_period : null,
                        'users_id'                  => auth()->id(),
                    ]);

                    DB::commit();

                    $status = true;
                    $message = "Employee's official data saved";
                    $employee_draft_id = $request->employee_id;
                }

                //------------------------------------------------------------------
                if ($request->step === 'three') {
                    $cardInfo = HrmEmployeeJobInfoDraft::query()
                                ->where('hrm_employee_draft_id', $request->employee_id)
                                ->first();
                    $cardInfo->card_code    = $request->card_code;
                    $cardInfo->old_code     = $request->old_code;
                    $cardInfo->device_id    = 1;
                    $cardInfo->save();

                    $status = true;
                    $message = 'Card Info has been saved.!';
                    $employee_draft_id = $request->employee_id;
                }

                //------------------------------------------------------------------
                if ($request->step === 'final') {
                    DB::beginTransaction();

                    $employeeDraft = HrmEmployeeDraft::query()->findOrFail($request->employee_id);

                    $employee                           = new HrmEmployee;
                    $employee->employee_name            = $employeeDraft->employee_name;
                    $employee->nickname                 = $employeeDraft->nickname;
                    $employee->contact_number           = $employeeDraft->contact_number;
                    $employee->father_name              = $employeeDraft->father_name;
                    $employee->mother_name              = $employeeDraft->mother_name;
                    $employee->dob                      = $employeeDraft->dob;
                    $employee->email                    = $employeeDraft->email;
                    $employee->nid                      = $employeeDraft->nid;
                    $employee->tin                      = $employeeDraft->tin;
                    $employee->passport                 = $employeeDraft->passport;
                    $employee->present_address          = $employeeDraft->present_address;
                    $employee->permanent_address        = $employeeDraft->permanent_address;
                    $employee->gender                   = $employeeDraft->gender;
                    $employee->hrm_blood_group_id       = $employeeDraft->hrm_blood_group_id;
                    $employee->hrm_religion_id          = $employeeDraft->hrm_religion_id;
                    $employee->hrm_marital_status_id    = $employeeDraft->hrm_marital_status_id;
                    $employee->hrm_education_id         = $employeeDraft->hrm_education_id;
                    $employee->Images                   = $employeeDraft->Images;
                    $employee->active_status            = $employeeDraft->active_status;
                    $employee->users_id                 = $employeeDraft->users_id;
                    $employee->save();


                    $employeeJobInfoDraft = HrmEmployeeJobInfoDraft::query()
                        ->where('hrm_employee_draft_id', $request->employee_id)
                        ->whereIn('employee_activity', [1, 10])
                        ->first();

                    $employeeJobInfo                             = new HrmEmployeeJobInfo;
                    $employeeJobInfo->hrm_employee_id            = $employee->id;
                    $employeeJobInfo->employee_code              = $employeeJobInfoDraft->employee_code;
                    $employeeJobInfo->official_contact_no        = $employeeJobInfoDraft->official_contact_no;
                    $employeeJobInfo->official_email             = $employeeJobInfoDraft->official_email;
                    $employeeJobInfo->hrm_depertment_id          = $employeeJobInfoDraft->hrm_depertment_id;
                    $employeeJobInfo->hrm_designation_id         = $employeeJobInfoDraft->hrm_designation_id;
                    $employeeJobInfo->hrm_category_id            = $employeeJobInfoDraft->hrm_category_id;
                    $employeeJobInfo->hrm_employment_status_id   = $employeeJobInfoDraft->hrm_employment_status_id;
                    $employeeJobInfo->hrm_manage_by_id           = $employeeJobInfoDraft->hrm_manage_by_id;
                    $employeeJobInfo->employee_shift_status      = $employeeJobInfoDraft->employee_shift_status;
                    $employeeJobInfo->overtime_status            = $employeeJobInfoDraft->overtime_status;
                    $employeeJobInfo->employee_activity          = $employeeJobInfoDraft->employee_activity;
                    $employeeJobInfo->basic_salary               = $employeeJobInfoDraft->basic_salary;
                    $employeeJobInfo->hrm_location_id            = $employeeJobInfoDraft->hrm_location_id;
                    $employeeJobInfo->hrm_section_id             = $employeeJobInfoDraft->hrm_section_id;
                    $employeeJobInfo->hrm_plant_id               = $employeeJobInfoDraft->hrm_plant_id;
                    $employeeJobInfo->insurance                  = $employeeJobInfoDraft->insurance;
                    $employeeJobInfo->users_id                   = $employeeJobInfoDraft->users_id;
                    $employeeJobInfo->save();

                    $insert_shift  = new HrmEmployeeShift;
                    $insert_shift->hrm_employee_job_info_id = $employeeJobInfo->id;
                    $insert_shift->hrm_shift_id             = $employeeJobInfoDraft->working_shift_id;
                    $insert_shift->start_date               = $employeeJobInfoDraft->joining_date;
                    $insert_shift->valid                    = 1;
                    $insert_shift->comment                  = 'NewJoin';
                    $insert_shift->users_id                 = $employeeJobInfoDraft->users_id;
                    $insert_shift->save();

                    DB::table('hrm_employee_joining')
                        ->insert([
                            'joining_date'      => $employeeJobInfoDraft->joining_date,
                            'confirmation_date' => $employeeJobInfoDraft->hrm_employment_status_id == 1 ? null : $employeeJobInfoDraft->confirmation_date,
                            'hrm_employee_id'   => $employee->id
                        ]);

                    if ($employeeJobInfoDraft->hrm_employment_status_id == 1) {
                        $probation                           = new HrmEmployeeProbation;
                        $probation->hrm_employee_id          = $employee->id;
                        $probation->hrm_probation_period_id  = $employeeJobInfoDraft->probation_period;
                        $probation->users_id                 = $employeeJobInfoDraft->users_id;
                        $probation->save();
                    }

                    $insert_activity                                    = new HrmEmployeeActivity;
                    $insert_activity->hrm_employee_job_info_id          = $employeeJobInfo->id;
                    $insert_activity->activity_date                     = date('Y-m-d');
                    $insert_activity->comment                           = 'Join';
                    $insert_activity->users_id                          = auth()->id();
                    $insert_activity->activity                          = 3;
                    $insert_activity->hrm_employee_activity_status_id   = 1;
                    $insert_activity->start_date                        = $employeeJobInfoDraft->joining_date;
                    $insert_activity->save();

                    if(config('module_config.payroll_module') == 1) {

                        $hrm_salary_grade_master_id = HrmSalaryGradeMaster::query()
                            ->where('hrm_salary_grade_id', $employeeJobInfoDraft->salary_grade_id)
                            ->first();
                        // dd($hrm_salary_grade_master_id);
                        if (!empty($hrm_salary_grade_master_id)) {
                            $hrm_salary_grade_master_id = $hrm_salary_grade_master_id->id;

                            (new CommonController)->insert_salary_config(
                                $employeeJobInfo->id,
                                $hrm_salary_grade_master_id,
                                $employeeJobInfo->basic_salary,
                                $employeeJobInfo->hrm_designation_id,
                                0
                            );

                            DB::UPDATE("UPDATE hrm_employee_salary SET accounts_code= LPAD($employee->id, 5, '0')  WHERE hrm_employee_job_info_id =$employeeJobInfo->id ");
                        }
                    }

                    if($employeeJobInfoDraft->card_code){
                        $isExist = DB::SELECT("SELECT
                                                    b.id
                                                FROM
                                                    hrm_employee_card_code a
                                                  JOIN hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id AND b.employee_activity = 1
                                                WHERE
                                                    b.hrm_location_id =  $employeeJobInfoDraft->hrm_location_id
                                                    AND a.card_code   = '$employeeJobInfoDraft->card_code'");

                        if(!empty($isExist)) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Sorry Your This Card No. Already Exists!'
                            ]);
                        }

                        $insert_card                            = new HrmEmployeeCardCode;
                        $insert_card->card_code                 = $employeeJobInfoDraft->card_code;
                        $insert_card->old_code                  = $employeeJobInfoDraft->old_code;
                        $insert_card->device_id                 = $employeeJobInfoDraft->device_id;
                        $insert_card->hrm_employee_job_info_id  = $employeeJobInfo->id;
                        $insert_card->hrm_employee_id           = $employee->id;
                        $insert_card->save();
                    }

                    if ($request->skipUserInfo == null) {
                        $userdata = User::create([
                            'name'              => $request->username,
                            'email'             => $request->email,
                            'designation'       => $request->designation,
                            'password'          => bcrypt($request->password),
                            'hrm_employee_id'   => $employee->id,
                        ]);

                        $insert     = new HrmUserLocation;
                        $insert->users_id          = $userdata->id;
                        $insert->hrm_location_id   = $employeeJobInfoDraft->hrm_location_id;
                        $insert->default_location  = 1;
                        $insert->save();

                        $userdata->assignRole($request->input('userrole'));
                    }

                    if ((bool) session('isEmployeeJoiningApproval')) {
                        HrmEmployeeJobApproval::create([
                            'action_type' => 1,
                            'comment' => 'New Join: '. $request->approval_comment,
                            'forward' => 1,
                            'hrm_employee_job_info_id' => $employeeJobInfo->id,
                            'job_users_id' => $request->job_users_id,
                            'users_id' => auth()->id()
                        ]);
                    }
                    //----------------------------------------------------------------

                    $this->cancel($request->employee_id, false);

                    session()->forget('isEmployeeJoiningApproval');

                    DB::commit();

                    $this->recordActivity(
                         1,
                         'Created Add New Employee',
                         null,
                         $employeeJobInfo->id,
                         'hrm_employee_job_info'
                    );

                    $status = true;
                    $message = 'Information Save in Main Table.!';
                    $employee_draft_id = $request->employee_id;
                }

        }else{


                $hrm_employee_id = $request->hrm_employee_id;
                $hrm_employee_job_info_data = HrmEmployeeJobInfo::query()
                                            ->where('hrm_employee_id', $hrm_employee_id)
                                            ->first();
                $hrm_employee_job_info_id = $hrm_employee_job_info_data->id;
                $hrm_location_id = $hrm_employee_job_info_data->hrm_location_id;


                if ($request->step === 'one') {
                    $dob = $request->date_of_birth ? date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth))) : null;

                    $fileUrl = null;

                    if ($request->hasFile('images')) {
                        $file = $request->file('images');
                        $var_path  = public_path('employee_image');
                        $ext = $file->getClientOriginalExtension();
                        $hash = $this->generateRandomString();
                        $fileName = $hash.'.'.$ext;
                        $file->move($var_path, $fileName);
                        $fileUrl = $fileName;
                    }

                    HrmEmployee::where('id', $hrm_employee_id)
                            ->update([
                                'employee_name'          => $request->employee_name,
                                'nickname'               => $request->nickname,
                                'contact_number'         => $request->contact_number,
                                'father_name'            => $request->father_name,
                                'mother_name'            => $request->mother_name,
                                'dob'                    => $dob,
                                'email'                  => $request->email,
                                'nid'                    => $request->nid,
                                'tin'                    => $request->tin,
                                'passport'               => '',
                                'present_address'        => $request->present_address,
                                'permanent_address'      => $request->permanent_address,
                                'gender'                 => $request->gender,
                                'hrm_blood_group_id'     => $request->blood_group,
                                'hrm_religion_id'        => $request->religion,
                                'hrm_marital_status_id'  => $request->marital_status,
                                'hrm_education_id'       => $request->hrm_education_id,
                                'Images'                 => $fileUrl,
                                'active_status'          => 1,
                                'users_id'               => auth()->id(),
                            ]);

                            $status = true;
                            $message = "Employee's personal data saved";

                }

                //-------------------------------------------------------------------
                if ($request->step === 'two') {

                    // Pending data Update


                    DB::beginTransaction();



                     $employeeJobInfo = HrmEmployeeJobInfo::where('id', $hrm_employee_job_info_id)
                        ->update([
                            'employee_code'             => $request->employee_code,
                            'official_contact_no'       => $request->official_contact_no,
                            'official_email'            => $request->official_email,
                            'hrm_depertment_id'         => $request->depertment,
                            'hrm_designation_id'        => $request->designation,
                            'hrm_category_id'           => $request->category,
                            'hrm_employment_status_id'  => $request->employeestatus,
                            'hrm_manage_by_id'          => $request->manage_by,
                            'employee_shift_status'     => 1,
                            'overtime_status'            => $request->overtime,
                            'basic_salary'              => $request->basic_salary,
                            'hrm_location_id'           => $request->job_location,
                            'hrm_section_id'            => $request->section,
                            'hrm_plant_id'              => $request->plant_name,
                            'insurance'                 => $request->insurance,
                            'users_id'                  => auth()->id(),
                        ]);


                        $joining_date = $request->joining_date ? date('Y-m-d', strtotime($request->joining_date)) : null;
                        $confirmation_date = $request->confirmation_date ? date('Y-m-d', strtotime($request->confirmation_date)) : null;


                        DB::table('hrm_employee_joining')
                            ->where('hrm_employee_id', $hrm_employee_id)
                            ->update([
                                'joining_date' => $joining_date,
                                'confirmation_date' => $request->hrm_employment_status_id == 1
                                    ? null
                                    : $confirmation_date,
                            ]);



                            HrmEmployeeShift::where(
                                'hrm_employee_job_info_id',
                                $hrm_employee_job_info_id
                            )->update([
                                'hrm_shift_id' => $request->working_shift,
                                'start_date'   => $joining_date,
                                'valid'        => 1,
                                'comment'      => 'NewJoin',
                                'users_id'     => auth()->id(),
                            ]);


                        if ($request->hrm_employment_status_id == 1) {

                            $probation = HrmEmployeeProbation::where(
                                'hrm_employee_id',
                                $hrm_employee_id
                            )->update([
                                'hrm_probation_period_id' => $request->probation_period,
                                'users_id'                => auth()->id(),
                            ]);

                        }

                        HrmEmployeeActivity::where(
                            'hrm_employee_job_info_id',
                            $hrm_employee_job_info_id
                        )->update([
                            'activity_date'                   => date('Y-m-d'),
                            'comment'                         => 'Join',
                            'users_id'                        => auth()->id(),
                            'activity'                        => 3,
                            'hrm_employee_activity_status_id' => 1,
                            'start_date'                      => $joining_date
                        ]);



                    if(config('module_config.payroll_module') == 1) {

                        $hrm_salary_grade_master_id = HrmSalaryGradeMaster::query()
                            ->where('hrm_salary_grade_id', $request->salary_grade_id)
                            ->first();

                        if (!empty($hrm_salary_grade_master_id)) {

                            $salary = HrmEmployeeSalary::where('hrm_employee_job_info_id', $hrm_employee_job_info_id)->first();

                            if ($salary) {
                                HrmEmployeeSalaryDetails::where('hrm_employee_salary_id',$salary->id)->delete();
                                $salary->delete();
                            }


                            $hrm_salary_grade_master_id = $hrm_salary_grade_master_id->id;

                            (new CommonController)->insert_salary_config(
                                $employeeJobInfo->id,
                                $hrm_salary_grade_master_id,
                                $employeeJobInfo->basic_salary,
                                $employeeJobInfo->hrm_designation_id,
                                0
                            );

                            DB::UPDATE("UPDATE hrm_employee_salary SET accounts_code= LPAD($hrm_employee_id, 5, '0')  WHERE hrm_employee_job_info_id =$hrm_employee_job_info_id ");
                        }
                    }





                    DB::commit();

                    $status = true;
                    $message = "Employee's official data saved";
                }


                //------------------------------------------------------------------
                if ($request->step === 'three') {

                    $cardInfo = HrmEmployeeCardCode::updateOrCreate(
                        [
                            'hrm_employee_job_info_id' => $hrm_employee_job_info_id,
                        ],
                        [
                            'card_code' => $request->card_code,
                            'old_code'  => $request->old_code,
                            'device_id' => 1,
                        ]
                    );
                                        $status = true;
                    $message = 'Card Info has been saved!';
                    $status = true;
                    $message = "Employee's official data saved";

                }

                if ($request->step === 'final') {


                 DB::beginTransaction();

                    if ($request->skipUserInfo == null) {

                        $userdata = User::updateOrCreate(
                            [
                                'hrm_employee_id' => $hrm_employee_id ,
                            ],
                            [
                                'name'        => $request->username,
                                'email'       => $request->email,
                                'designation' => $request->designation,
                                'password'    => bcrypt($request->password),
                            ]
                        );


                       HrmUserLocation::updateOrCreate(
                                [
                                    'users_id' => $userdata->id,
                                ],
                                [
                                    'hrm_location_id'  => $hrm_location_id,
                                    'default_location' => 1,
                                ]
                            );

                        $userdata->assignRole($request->input('userrole'));
                    }



                    HrmEmployeeJobApproval::updateOrCreate(
                        [
                            'hrm_employee_job_info_id' => $hrm_employee_job_info_id,
                        ],
                        [
                            'action_type' => 1,
                            'comment' => 'New Join Update: ' . $request->approval_comment,
                            'forward' => 1,
                            'job_users_id' => $request->job_users_id,
                            'users_id' => auth()->id(),
                        ]
                    );

                    //----------------------------------------------------------------
                    DB::commit();

                    $status = true;
                    $message = "Employee's official data saved";


                }


                    $this->recordActivity(
                         1,
                         'Update Employee Pending Data',
                         null,
                         $hrm_employee_job_info_id,
                         'hrm_employee_job_info'
                    );

                $employee_draft_id = $hrm_employee_id;


        }


            } catch (\Exception $e) {
                $status = false;
                $message = $e->getMessage();
            }
        }
        return response()->json([
            'status'            => $status,
            'message'           => $message ?? '',
            'employee_draft_id' => $employee_draft_id ?? '',
            "error"             => $error ?? ''
        ]);
    }

    public function edit($id)
    {

    // dd($id);

        $data['probations'] = HrmProbationPeriod::all();
        $data['role_lists'] = DB::select("SELECT id, name as display_name from roles");

        $datafind['employee'] = HrmEmployeeDraft::query()->find($id);

        if (!$datafind['employee']) {

            $data['employee_info'] = DB::select("SELECT a.*,
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
                                f.joining_date,
                                f.confirmation_date,
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
                                l.id as working_shift_id,
                                l.shift_name,
                                m.id as religion_id,
                                m.religion,
                                n.id as marital_status_id,
                                n.marital_status,
                                o.id as blood_group_id,
                                o.blood_group,
                                p.education_name,
                                q.id AS hrm_plant_id,
                                q.plant_name,
                                a.Images,
                                b.insurance,
                                b.official_contact_no,
                                b.official_email,
                                s.period as probation_period,
                                u.id as salary_grade_id,
                                v.grade_name,
                                r.hrm_probation_period_id,
                                w.card_code,
                                w.old_code,
                                k.id,
                                b.hrm_employee_id
                                from hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                                AND b.id in (SELECT max(id) FROM hrm_employee_job_info WHERE hrm_employee_id=$id )
                                AND a.id= $id
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
                                LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id
                                LEFT JOIN hrm_employee_salary t ON b.id = t.hrm_employee_job_info_id
                                LEFT JOIN hrm_salary_grade_master u ON t.hrm_salary_grade_master_id = u.id
                                LEFT JOIN hrm_salary_grade v ON v.id = u.hrm_salary_grade_id
                                LEFT JOIN hrm_employee_card_code w ON w.hrm_employee_job_info_id = b.id
                                ")[0];





        }else{


            $data['employee_info'] = DB::select("SELECT
                                                    a.*,
                                                    b.id AS hrm_employee_job_info_draft_id,
                                                    b.probation_period,
                                                    c.id AS location_id,
                                                    c.location_name,
                                                    d.id AS department_id,
                                                    d.depertment_name,
                                                    e.id AS designation_id,
                                                    e.designation_name,
                                                    b.basic_salary,
                                                    b.official_contact_no,
                                                    b.official_email,
                                                    b.card_code,
                                                    b.old_code,
                                                    b.device_id,
                                                    b.employee_code,
                                                    b.joining_date,
                                                    b.confirmation_date,
                                                    g.id AS employeestatus_id,
                                                    g.employment_status AS employeestatus_name,
                                                    h.id AS category_id,
                                                    h.category_name,
                                                    i.id AS sectionid,
                                                    i.section_name,
                                                    b.overtime_status,
                                                    j.id AS manage_by_id,
                                                    j.employee_name AS manage_by_name,
                                                    m.id AS religion_id,
                                                    m.religion,
                                                    n.id AS marital_status_id,
                                                    n.marital_status,
                                                    o.id AS blood_group_id,
                                                    o.blood_group,
                                                    p.education_name,
                                                    p.id AS hrm_education_id,
                                                    q.id AS hrm_plant_id,
                                                    q.plant_name,
                                                    b.insurance,
                                                    s.period,
                                                    r.hrm_probation_period_id,
                                                    b.working_shift_id,
                                                    ss.shift_name,
                                                    b.salary_grade_id,
                                                    t.grade_name,
                                                    null as hrm_employee_id
                                                FROM
                                                    hrm_employee_draft a
                                                        LEFT JOIN
                                                    hrm_employee_job_info_draft b ON a.id = b.hrm_employee_draft_id
                                                        AND a.active_status = 1
                                                        AND b.employee_activity IN(1, 10)
                                                        LEFT JOIN
                                                    hrm_location c ON b.hrm_location_id = c.id
                                                        LEFT JOIN
                                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                                        LEFT JOIN
                                                    hrm_designation e ON b.hrm_designation_id = e.id
                                                        LEFT JOIN
                                                    hrm_employment_status g ON b.hrm_employment_status_id = g.id
                                                        LEFT JOIN
                                                    hrm_category h ON b.hrm_category_id = h.id
                                                        LEFT JOIN
                                                    hrm_section i ON b.hrm_section_id = i.id
                                                        LEFT JOIN
                                                    hrm_employee j ON b.hrm_manage_by_id = j.id
                                                        JOIN
                                                    hrm_religion m ON m.id = a.hrm_religion_id
                                                        JOIN
                                                    hrm_marital_status n ON n.id = a.hrm_marital_status_id
                                                        JOIN
                                                    hrm_blood_group o ON o.id = a.hrm_blood_group_id
                                                        JOIN
                                                    hrm_education p ON p.id = a.hrm_education_id
                                                        LEFT JOIN
                                                    hrm_plant q ON q.id = b.hrm_plant_id
                                                        LEFT JOIN
                                                    hrm_employee_probation r ON r.hrm_employee_id = a.id
                                                        LEFT JOIN
                                                    hrm_probation_period s ON r.hrm_probation_period_id = s.id
                                                        LEFT JOIN
                                                    hrm_salary_grade t ON b.salary_grade_id = t.id
                                                        LEFT JOIN
                                                    hrm_shift ss ON b.working_shift_id = ss.id
                                                            WHERE a.id = $id ")[0];






        }

        return view('add_new_employee.create_edit', $data);
    }

    public function checkIsEmployeeJoiningApproval()
    {
        return session('isEmployeeJoiningApproval') == 1 ? true: 0;
    }

/*
    public function update(Request $request, $id)
    {
        $status = false;

        $rules = [];

        if ($request->step === 'one') {
            $rules = [
                'employee_name' => 'required|string',
                'nickname' => 'required|string',
                'present_address' => 'required|string',
                'permanent_address' => 'required|string',
                'contact_number' => 'required|string',
                'email' => 'required|string|email',
                'religion' => 'required',
                'marital_status' => 'required',
                'blood_group' => 'required',
                'hrm_education_id' => 'required',
            ];
        }

        elseif ($request->step === 'two') {
            $rules = [
                'employee_code' => 'required|string',
                'depertment' => 'required',
                'designation' => 'required',
                'category' => 'required',
                'job_location' => 'required',
                'section' => 'required',
                'working_shift' => 'required',
                'plant_name' => 'required',
                'manage_by' => 'required',
                'basic_salary' => 'required',
                'salary_grade' => 'required',
                'employeestatus' => 'required',
            ];
        }

        elseif ($request->step === 'three') {
            $rules = [
                'card_code' => 'required',
            ];
        }

        Validator::make($request->all(), $rules)
            ->validate();


        $employeeDraft = HrmEmployeeDraft::query()->find($id);


        if ($request->step === 'one') {

            $dob = $request->date_of_birth ? date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth))) : null;

            try {
                $file = $request->file('images');
                $findImg = public_path().'/employee_image/'.$employeeDraft->Images;

                if ($file) {
                    if (file_exists($findImg)) {
                        @unlink($findImg);
                    }

                    $var_path  = public_path().'/employee_image/';
                    $ext = $file->getClientOriginalExtension();
                    $hash = $this->generateRandomString();
                    $fileName = $hash.'.'.$ext;

                    $file->move($var_path, $fileName);
                    $fileUrl = $fileName;
                } else {
                    $fileUrl = $employeeDraft->Images;
                }

                $employeeDraft->employee_name          = $request->employee_name;
                $employeeDraft->nickname               = $request->nickname;
                $employeeDraft->contact_number         = $request->contact_number;
                $employeeDraft->father_name            = $request->father_name;
                $employeeDraft->mother_name            = $request->mothers_name;
                $employeeDraft->dob                    = $dob;
                $employeeDraft->email                  = $request->email;
                $employeeDraft->nid                    = $request->nid;
                $employeeDraft->tin                    = $request->tin;
                $employeeDraft->passport               = '';
                $employeeDraft->present_address        = $request->present_address;
                $employeeDraft->permanent_address      = $request->permanent_address;
                $employeeDraft->gender                 = $request->gender;
                $employeeDraft->hrm_blood_group_id     = $request->blood_group;
                $employeeDraft->hrm_religion_id        = $request->religion;
                $employeeDraft->hrm_marital_status_id  = $request->marital_status;

                $employeeDraft->hrm_education_id       = $request->hrm_education_id;
                $employeeDraft->Images                 = $fileUrl;
                $employeeDraft->active_status          = 1;
                $employeeDraft->users_id               = auth()->id();
                $employeeDraft->save();

                $status = true;
                $message = "Employee's personal data saved";

            } catch (\Exception $e) {

                $status = false;
                $message = $e->getMessage();
            }

            return response()->json([
                'status'   => $status,
                'message'  => $message ?? '',
                'employee_draft_id' => $employeeDraft->id ?? ''
            ]);

        }

        elseif ($request->step === 'two') {

            $joining_date       = $request->joining_date ?
                                    date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date))) :
                                    null;

            $confirmation_date  = $request->confirmation_date ?
                                    date('Y-m-d', strtotime(str_replace('/', '-', $request->confirmation_date))) :
                                    null;

            try {
                $employeejobinfo = HrmEmployeeJobInfoDraft::query()
                    ->where('hrm_employee_draft_id', $employeeDraft->id)
                    ->where('employee_activity', 1)
                    ->first();

                if (empty($employeejobinfo)) {
                    $employeejobinfo = new HrmEmployeeJobInfoDraft;
                }

                $employeejobinfo->hrm_employee_draft_id      = $employeeDraft->id;
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
                $employeejobinfo->working_shift_id           = $request->working_shift;
                $employeejobinfo->salary_grade_id            = $request->salary_grade;
                $employeejobinfo->insurance                  = $request->insurance;
                $employeejobinfo->users_id                   = auth()->id();
                $employeejobinfo->save();

                $hrm_employee_joining_draft = HrmEmployeeJoiningDraft::query()
                    ->where('hrm_employee_draft_id', $employeeDraft->id)->first();

                if (empty($hrm_employee_joining_draft)) {
                    $hrm_employee_joining_draft = new HrmEmployeeJoiningDraft;
                    $hrm_employee_joining_draft->joining_date = $joining_date;
                    $hrm_employee_joining_draft->confirmation_date = $confirmation_date;
                    $hrm_employee_joining_draft->hrm_employee_draft_id = $employeeDraft->id;
                    $hrm_employee_joining_draft->save();
                } else {
                    if($request->employeestatus == 1) {
                        DB::UPDATE("UPDATE hrm_employee_joining_draft SET joining_date='$joining_date', confirmation_date = null WHERE hrm_employee_draft_id = $employeeDraft->id");
                    } else {
                        DB::UPDATE("UPDATE hrm_employee_joining_draft SET joining_date='$joining_date', confirmation_date = '$confirmation_date' WHERE hrm_employee_draft_id = $employeeDraft->id");
                    }
                }


                DB::commit();

                $status = true;
                $message = "Employee's official data saved";
            } catch (\Exception $e) {
                DB::rollback();

                $status = false;
                $message = $e->getMessage();
            }

            return response()->json([
                'status'   => $status,
                'message'  => $message ?? ''
            ]);
        }

        elseif ($request->step === 'three') {
            try {
                $cardInfo = HrmEmployeeJobInfoDraft::query()
                    ->where('hrm_employee_draft_id', $employeeDraft->id)
                    ->where('employee_activity', 1)
                    ->first();

                $cardInfo->card_code = $request->card_code;
                $cardInfo->old_code = $request->old_code;
                $cardInfo->device_id = $request->device_id;
                $cardInfo->save();

                $status = true;
                $message = 'Card Info has been saved.!';
            } catch (\Exception $e) {
                $status = false;
                $message = $e->getMessage();
            }

            return response()->json([
                'status' => $status,
                'message' => $message ?? '',
            ]);
        }
    }
*/
    // public function cancel($id, $imageDelete = true)
    // {
    //     $employeeDraft = HrmEmployeeDraft::query()->findOrFail($id);

    //     if ($imageDelete) {
    //         if ($employeeDraft->Images != null) {
    //             $path = public_path() . '/employee_image/';
    //             @unlink($path.$employeeDraft->Images);
    //         }
    //     }

    //     DB::table('hrm_employee_draft')->where('id', $id)->delete();

    //     DB::table('hrm_employee_job_info_draft')->where('hrm_employee_draft_id', $id)->delete();

    //     return back();
    // }

    public function cancel($id, $imageDelete = true)
    {
        DB::beginTransaction();

        try {

            $employeeDraft = HrmEmployeeDraft::find($id);

            if ($employeeDraft) {

                if ($imageDelete) {
                    if ($employeeDraft->Images != null) {
                        $path = public_path() . '/employee_image/';
                        @unlink($path.$employeeDraft->Images);
                    }
                }

                DB::table('hrm_employee_draft')->where('id', $id)->delete();

                DB::table('hrm_employee_job_info_draft')->where('hrm_employee_draft_id', $id)->delete();

            } else {

                $employee = HrmEmployee::find($id);

                if (!$employee) {
                    return back()->with('error', 'Employee not found.');
                }

                // Main employee delete logic
                $this->deleteEmployee($id, $imageDelete);
            }

            DB::commit();

            return back()->with('success', 'Employee cancelled successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function deleteEmployee($id, $imageDelete = true)
    {
        $employee = DB::table('hrm_employee')->where('id', $id)->first();

        if (!$employee) {
            return false;
        }

        if ($imageDelete && !empty($employee->Images)) {

            $path = public_path(
                'employee_image/' . $employee->Images
            );

            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $jobInfoIds = DB::table('hrm_employee_job_info')->where('hrm_employee_id', $id)->pluck('id');
        $user = DB::table('users')->where('hrm_employee_id', $id)->first();

        if ($user) {
            DB::table('user_location')->where('users_id', $user->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Job Info Related Data
        |--------------------------------------------------------------------------
        */
        if ($jobInfoIds->isNotEmpty()) {

            $salaryIds = DB::table('hrm_employee_salary')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->pluck('id');

            if ($salaryIds->isNotEmpty()) {

                DB::table('hrm_employee_salary_details')->whereIn('hrm_employee_salary_id',$salaryIds)->delete();
            }


            DB::table('hrm_employee_salary')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_shift')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_job_approval')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_bonus')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_salary_increment_details')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_activity')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_resignation')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_inactive')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_reporting_boss_change')->whereIn('hrm_employee_job_id',$jobInfoIds)->delete();

            DB::table('hrm_salary_extra_feature_details')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_ot_process_details')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_plantwise_employee')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_leave_year')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_card_code')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_kpi_employee_master')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('pay_register')->whereIn('hrm_employee_job_info_id',$jobInfoIds)->delete();

            DB::table('hrm_employee_job_info')->whereIn('id',$jobInfoIds)->delete();
        }


        DB::table('hrm_attendance')->where('hrm_employee_id', $id)->delete();

        DB::table('hrm_employee_joining')->where('hrm_employee_id', $id)->delete();

        DB::table('hrm_employee_probation')->where('hrm_employee_id', $id)->delete();

        DB::table('hrm_employee_card_code')->where('hrm_employee_id', $id)->delete();

        DB::table('tmp_employee_shift')->where('hrm_employee_id', $id)->delete();

        DB::table('hrm_employee')->where('id', $id)->delete();


        return true;
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
