<?php

namespace App\Imports;

use Config;
use App\Http\Controllers\CommonController;
use App\Models\HrmBloodGroup;
use App\Models\HrmCategory;
use App\Models\HrmDepertment;
use App\Models\HrmDesignation;
use App\Models\HrmEmployee;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeJoining;
use App\Models\HrmEmployeeShift;
use App\Models\HrmMaritalStatus;
use App\Models\HrmReligion;
use App\Models\HrmSalaryGradeMaster;
use App\Models\HrmSection;
use App\Models\HrmShift;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmployeeImport2 implements ToCollection
{
    public  $location;

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function collection(Collection $rows)
    {
        $userId = auth()->id();
        $currentDate = date('Y-m-d');
        $hrm_location_id = $this->location;
        $total_count = 0;

        // dd($rows->count());

        $contacts = [];

        $i = 0;

        DB::beginTransaction();
        try {
            foreach ($rows->toArray() as $row) {
                if ($i !== 0) {

                    if ($row[0] == null || trim($row[0]) == '') {
                      continue;
                    } else {
                        $employee_name = $row[0];
                    }

                    if ($row[2] === "Male") {
                        $gender = 1;
                    } else {
                        $gender = 2;
                    }



                    if ($row[3] == null) {
                        $joiningDate = null;
                    } else {
                        $joiningDate = $this->dateConvert($row[3]);
                    }
                    if ($row[4] == null || strtolower($row[4]) == strtolower("Probation")) {
                        $confirmationDate = null;
                    } else {
                        $confirmationDate = $this->dateConvert($row[4]);
                    }

                    if (strtolower(($row[5])) == strtolower("Permanent")) {
                        $employment_status = 2;
                    } else if (strtolower(($row[5])) == strtolower("Contractual")) {
                        $employment_status = 3;
                    } else if (strtolower(($row[5])) == strtolower("Casual")) {
                        $employment_status = 4;
                    } else {
                        $employment_status = 1;
                    }

                    if ($row[6] == null) {
                        $employee_code = null;
                    } else {
                        $employee_code = $row[6];
                    }

                    $department = HrmDepertment::query()->firstOrCreate([
                        'depertment_name' => $row[7],
                    ], [
                        'users_id' => $userId,
                        'valid' => 1,
                    ])->id;

                    $jobPlacement = HrmSection::query()->firstOrCreate([
                        'section_name' => $row[8],
                    ])->id;

                    $designation = HrmDesignation::query()->firstOrCreate([
                        'designation_name' => $row[8],
                    ], [
                        'alis' => $row[8],
                        'valid' => 1,
                        'users_id' => $userId,
                    ])->id;

                    $category = HrmCategory::query()->firstOrCreate([
                        'category_name' => $row[10],
                    ], [
                        'valid' => 1,
                        'users_id' => $userId,
                    ])->id;


                    $manageBy = HrmEmployee::query()->firstWhere('email', $row[11]);

                    $start = date('H:i:s', strtotime($row[12]));
                    $end = date('H:i:s', strtotime($row[13]));

                    $shift = HrmShift::query()
                        ->where('start_time', date('H:i:s', strtotime($row[12])))
                        ->where('end_time', date('H:i:s', strtotime($row[13])))
                        ->first();

                    if (empty($shift)) {
                        $duration = Carbon::parse($end)->diff(Carbon::parse($start));
                        $workingHour = $duration->h . ':' . $duration->i . ':' . $duration->s;

                        $shift = HrmShift::create([
                            'shift_name' => date('H:i:s', strtotime($row[12])) . '-' . date('H:i:s', strtotime($row[13])),
                            'start_time' => date('H:i:s', strtotime($row[12])),
                            'end_time' => date('H:i:s', strtotime($row[13])),
                            'working_hours' => $workingHour,
                            'users_id' => $userId,
                            'valid' =>1
                        ]);
                    }

                    if ($row[14] == null) {
                        $salary = 0;
                    } else {
                        $salary = $row[14];
                    }

                    if ($row[15] == null || $row[15] === "Yes") {
                        $status = 1;
                    } else {
                        $status = 0;
                    }

                    $email = $row[17];

                    if($row[18] == null) {
                        $nickname = 'N/A';
                    } else {
                        $nickname = $row[18];
                    }

                    if($row[19] == null) {
                        $fatherName = 'N/A';
                    } else {
                        $fatherName = $row[19];
                    }

                    if($row[20] == null) {
                        $motherName = 'N/A';
                    } else {
                        $motherName = $row[20];
                    }

                    if ($row[21] == null) {
                        $dob = null;
                    } else {
                        $dob =$this->dateConvert($row[21]);
                    }



                    if($row[22] == null) {
                        $nid = 'N/A';
                    } else {
                        $nid = $row[22];
                    }

                    if($row[23] == null) {
                        $tin = 'N/A';
                    } else {
                        $tin = $row[23];
                    }

                    if($row[24] == null) {
                        $passport = 'N/A';
                    } else {
                        $passport = $row[24];
                    }

                    if($row[25] == null) {
                        $presentAddress = 'N/A';
                    } else {
                        $presentAddress = $row[25];
                    }

                    if($row[26] == null) {
                        $permanentAddress = 'N/A';
                    } else {
                        $permanentAddress = $row[26];
                    }
  
                    $blood_group = HrmBloodGroup::query()->firstOrCreate([
                            'blood_group' => $row[27]
                    ])->id;

                    $religion = HrmReligion::query()->firstOrCreate([
                            'religion' => $row[28]
                        ])->id;

                    $marital_status = HrmMaritalStatus::query()->firstOrCreate([
                        'marital_status' => $row[29]
                    ])->id;

                    $employee = HrmEmployee::query()
                        ->where('email', trim($email))
                        ->first();

                    $contact = HrmEmployee::query()
                        ->where('contact_number', trim($row[1]))
                        ->first();


                    if (!empty($contact)) {
                        $contacts[] = $contact->employee_name;

                        continue;
                    } else {

                        $contact_number = trim($row[1]) ?: 'Nil'.rand(11111111111, 6576543098);

                        $employee = HrmEmployee::create([
                            'email' => (!empty($employee) || $email == "-" || $email == null) ? trim($row[1]).'@email.com' : trim($email),
                            'employee_name' => trim($employee_name),
                            'nickname' => trim($nickname),
                            'contact_number' => $contact_number ,
                            'father_name' => trim($fatherName),
                            'mother_name' => trim($motherName),
                            'dob' => $dob,
                            'nid' => trim($nid),
                            'tin' => trim($tin),
                            'passport' => trim($passport),
                            'present_address' => trim($presentAddress),
                            'permanent_address' => trim($permanentAddress),
                            'gender' => $gender,
                            'hrm_blood_group_id' => $blood_group,
                            'hrm_religion_id' => $religion,
                            'hrm_marital_status_id' => $marital_status,
                            'hrm_education_id' => 1,
                            'active_status' => 1,
                            'users_id' => auth()->id()
                        ]);

                        $employeejobinfo = HrmEmployeeJobInfo::create([// if have emp with this email don't create.
                            'hrm_employee_id' => $employee->id,
                            'employee_code' => $employee_code,
                            'hrm_depertment_id' => $department,
                            'hrm_designation_id' => $designation,
                            'overtime_status' => $status,
                            'hrm_location_id' => $hrm_location_id,
                            'hrm_manage_by_id' => optional($manageBy)->id ?: $employee->id,
                            'hrm_employment_status_id' => $employment_status,
                            'basic_salary' => $salary,
                            'hrm_category_id' => $category,
                            'employee_shift_status' => 1,
                            'employee_activity' => 1,
                            'hrm_section_id' => $jobPlacement,
                            'hrm_plant_id' => 1,
                            'users_id' => $userId
                        ]);

                        HrmEmployeeShift::create([
                            'hrm_shift_id' => $shift->id,
                            'hrm_employee_job_info_id' => $employeejobinfo->id,
                            'start_date' => $joiningDate,
                            'valid' => 1,
                            'comment' => 'NewJoin',
                            'users_id' => $userId
                        ]);

                        HrmEmployeeJoining::create([
                            'hrm_employee_id' => $employee->id,
                            'joining_date' => $joiningDate,
                            'confirmation_date' => $confirmationDate
                        ]);

                        HrmEmployeeActivity::create([
                            'hrm_employee_job_info_id' => $employeejobinfo->id,
                            'activity_date' => $currentDate,
                            'comment' => 'Join',
                            'users_id' => auth()->id(),
                            'activity' => 3,
                            'hrm_employee_activity_status_id' => 1,
                            'start_date' => $joiningDate
                        ]);

                        HrmEmployeeCardCode::create([
                            'card_code' => $row[16] ?? 'Web'.$employee->id,
                            'device_id' => 1,
                            'hrm_employee_job_info_id' => $employeejobinfo->id
                        ]);

                        // $weekend = DB::table("hrm_days_name")
                        //     ->where("days_name", $row[30])->first();

                        // if($weekend) {
                        //     DB::table("hrm_holiday_configure")->insert([
                        //         'hrm_days_name_id' => $weekend->id,
                        //         'hrm_location_id' => $hrm_location_id,
                        //         'hrm_employee_id' => $employee->id,
                        //         'start_date' => date('Y-m-d'),
                        //         'users_id' => auth()->id(),
                        //         'created_at' => now()->toDateTimeString(),
                        //         'updated_at' => now()->toDateTimeString(),
                        //     ]);
                        // }

                        if(config('module_config.payroll_module') == 1) {
                            $hrm_employee_job_info_id     = $employeejobinfo->id;
                            $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', 1)->first()->id;
                            $gross_salary                 = $salary;
                            $hrm_designation_id           = $designation;
                            $old_hrm_employee_job_info_id = 0;

                            $getFunction = new CommonController();
                            $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);
                        }
                    }

                    $total_count = $i;
                }

                $i++;
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();

            return back()->with('error', $exception->getMessage());
        }

        $contMsg = '';

        if (count($contacts) > 0) {
            $implodeContacts = implode(', ', $contacts);

            $contMsg = " These [ {$implodeContacts} ] are already exists.";
        }

        return back()->with('success', 'Data was imported successfully..! Insert total data = '.$total_count.' || '.$contMsg);
    }


    protected function dateConvert($date)
    {
        if (is_null($date)) return null;

        $date = strtotime(str_replace('/', '-', $date));
        // $unixDate = ($date - 25569) * 86400;
        // dd($unixDate);
        // dd(gmdate("Y-m-d", $unixDate));
        return date("Y-m-d", $date);
    }
}
