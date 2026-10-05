<?php

namespace App\Http\Controllers;

use App\Models\HrmEmployee;
use App\Models\HrmReligion;
use App\Models\HrmEducation;
use Illuminate\Http\Request;
use App\Models\HrmBloodGroup;
use App\Models\HrmMaritalStatus;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmProbationPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeProbation;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Auth;
use App\Models\HrmShift as Shift;
use Illuminate\Support\Facades\Validator;
use App\Models\HrmCategory as Category;
use App\Models\HrmDepertment as Depertment;
use App\Models\HrmDesignation as Designation;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employee.employee_list');
    }

    public function create()
    {
        return view('employee.create_employee')
            ->with('religion',HrmReligion::all())
            ->with('marital_status',HrmMaritalStatus::all())
            ->with('blood_group',HrmBloodGroup::all());
    }

    public function employeelist()
    {
        $employee = DB::select("SELECT
                                a.id,
                                a.employee_name,
                                a.contact_number,
                                a.email,
                                b.religion,
                                IF(a.gender = 1, 'Male', 'Female') AS gender,
                                Images
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_religion b ON a.hrm_religion_id = b.id AND a.active_status=1  AND a.id NOT IN (SELECT hrm_employee_id FROM hrm_employee_job_info  order by a.id)");

        return json_encode(array('data' => $employee));
    }

    public function employeehistory_list(Request $request)
    {
        $employee = DB::select("SELECT
                                    a.id,
                                    d.description,
                                    a.start_date,
                                    IF(a.end_date IS NULL,
                                        'Running',
                                        a.end_date) AS end_date,
                                    e.location_name,
                                    f.depertment_name,
                                    g.alis as designation_name,
                                    b.basic_salary,
                                    h.category_name
                                FROM
                                    hrm_employee_activity a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id AND c.id = $request->employee_id
                                        JOIN
                                    hrm_employee_activity_status d ON a.hrm_employee_activity_status_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id=e.id
                                        JOIN
                                    hrm_depertment f ON b.hrm_depertment_id=f.id
                                        JOIN
                                    hrm_designation g ON b.hrm_designation_id = g.id
                                        JOIN
                                    hrm_category h ON b.hrm_category_id=h.id
                                    Order By a.id ;");

        return json_encode(array('data' => $employee));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'employee_name'    => 'required|unique:hrm_marital_status,marital_status|max:255',
            'employee_name'     => 'required',
            'present_address'   => 'required',
            'permanent_address' => 'required',
            'contact_number'    => 'required',
            'gender'            => 'required',
            'religion'          => 'required',
            'marital_status'    => 'required',
            'blood_group'       => 'required',
            'date_of_birth'     => 'required',
            'nid'               => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employee/create')
                        ->withErrors($validator)
                        ->withInput();
        } else {
            $dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth)));

            DB::beginTransaction();
            try {
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


                $insert     = new HrmEmployee;
                $insert->employee_name          = $request->employee_name;
                $insert->nickname               = $request->nickname;
                $insert->contact_number         = $request->contact_number;
                $insert->father_name            = $request->father_name;
                $insert->mother_name            = $request->mothers_name;
                $insert->dob                    = $dob;
                $insert->email                  = $request->email;
                $insert->nid                    = $request->nid;
                $insert->tin                    = $request->tin;
                $insert->passport               = '';
                $insert->present_address        = $request->present_address;
                $insert->permanent_address      = $request->permanent_address;
                $insert->gender                 = $request->gender;
                $insert->hrm_blood_group_id     = $request->blood_group;
                $insert->hrm_religion_id        = $request->religion;
                $insert->hrm_marital_status_id  = $request->marital_status;

                $insert->hrm_education_id       = $request->hrm_education_id;
                $insert->Images                 = $fileUrl;
                $insert->active_status          = 1;
                $insert->users_id               = Auth::user()->id;
                $insert->save();


                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }
        }

        return response()->json(array(
            'success'   => true,
            'messages'  => 'successfully employee add!'
        ));
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function show($id)
    {
        $employee = HrmEmployee::find($id);

        return view('join_employee.join_employee')
            ->with('employee', $employee)
            ->with('depertment', Depertment::where('valid', 1)->get())
            ->with('designation', Designation::where('valid', 1)->get())
            ->with('category', Category::where('valid', 1)->get())
            ->with('probation', HrmProbationPeriod::all())
            ->with('payroll_module', config('module_config.payroll_module'))
            ->with('working_shift', Shift::where('valid', 1)->get());
    }

    public function edit($id)
    {
        $employee = HrmEmployee::find($id);
        if (empty($employee)){
            session()->flash('alert-danger', 'Invalid employee !!');
            return Redirect()->back();
        }
        return view('employee.edit_employee')
            ->with('employee',$employee)
            ->with('religion',HrmReligion::all())
            ->with('marital_status',HrmMaritalStatus::all())
            ->with('education',HrmEducation::all())
            ->with('blood_group',HrmBloodGroup::all());
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'present_address'   => 'required',
            'permanent_address' => 'required',
            'contact_number'    => 'required',
            'gender'            => 'required',
            'religion'          => 'required',
            'marital_status'    => 'required',
            'blood_group'       => 'required',
            'date_of_birth'     => 'required',
            'nid'               => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employee/create')
                ->withErrors($validator)
                ->withInput();
        }

        $employee = HrmEmployee::find($id);

        $dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth)));

       $employeeImage = DB::select("SELECT Images from hrm_employee  Where id= $id");

        DB::beginTransaction();
        try {
            $file = $request->file('images');
            $findImg = public_path().'/employee_image/'.$employeeImage[0]->Images;

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
                $fileUrl = $employeeImage[0]->Images;
            }

            $employee->employee_name          = $request->employee_name;
            $employee->nickname               = $request->nickname;
            $employee->contact_number         = $request->contact_number;
            $employee->father_name            = $request->father_name;
            $employee->mother_name            = $request->mothers_name;
            $employee->dob                    = $dob;
            $employee->email                  = $request->email;
            $employee->nid                    = $request->nid;
            $employee->tin                    = $request->tin;
            $employee->passport               = '';
            $employee->present_address        = $request->present_address;
            $employee->permanent_address      = $request->permanent_address;
            $employee->gender                 = $request->gender;
            $employee->hrm_blood_group_id     = $request->blood_group;
            $employee->hrm_religion_id        = $request->religion;
            $employee->hrm_marital_status_id  = $request->marital_status;
            $employee->hrm_education_id       = $request->hrm_education_id;
            $employee->Images                 = $fileUrl;
            $employee->active_status          = 1;
            $employee->users_id               = Auth::user()->id;
            $employee->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        return response()->json(array(
            'success' => true,
            'messages' => 'successfully employee updated.!'
        ));

        // $request->session()->flash('alert-success', 'data has been successfully updated!');
        // return Redirect::to('employee');
        // unlink('path/to/file.jpg');
    }

    public function cancel(Request $request,$id){

        $cancel = HrmEmployee::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid employee !!');
            return back();
        }
        if ($cancel->Images != null){
            $path = public_path() . '/employee_image/'; // upload directory
            unlink($path.$cancel->Images);
        }

        // $activity   = DB::table('hrm_employee')->where('hrm_marital_status_id','=',$id)->first();

        // if (!empty($activity)){
        //     session()->flash('alert-danger', "You can't delete this marital status !! This marital status use to employees ");
        //     return Redirect()->back();
        // }

        DB::table('hrm_employee')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return redirect()->to('employee');
    }


    public function employeeinfo($id)
    {
        $employee = HrmEmployee::find($id);

        if (empty($employee)) {
            session()->flash('alert-danger', 'Invalid employee !!');
            return back();
        }

        $editemployee = DB::select("SELECT a.id,
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
                                LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id");


         $hrm_employee_job_info_id = $editemployee[0]->hrm_employee_job_info_id;
         $employee_salary_grade    = '';


         if(config('module_config.payroll_module') == 1){

            $hrm_employee_salary_info = HrmEmployeeSalary::where('hrm_employee_job_info_id',$hrm_employee_job_info_id)->first();

            if(!empty($hrm_employee_salary_info->hrm_salary_grade_master_id)){

                $employee_salary_grade    = DB::SELECT("SELECT
                                                        b.id,
                                                        b.grade_name
                                                    FROM
                                                        hrm_salary_grade_master a
                                                            JOIN
                                                        hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
                                                            AND a.id=$hrm_employee_salary_info->hrm_salary_grade_master_id");
            }else{
                $employee_salary_grade = DB::SELECT("SELECT 0 as id,'' as grade_name FROM hrm_salary_grade_master limit 1");
            }
        }

        $approvalHistories = DB::select("SELECT
                a.id,
                a.hrm_employee_job_info_id,
                a.action_type,
                a.forward,
                a.comment,
                d.name forward_to,
                e.name forward_by,
                a.updated_at action_time
            FROM
                hrm_employee_job_approval a
                    JOIN
                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                    AND a.hrm_employee_job_info_id = $hrm_employee_job_info_id
                    JOIN
                users d ON a.job_users_id = d.id
                    JOIN
                users e ON a.users_id = e.id
        ");

        $file_type = DB::SELECT("SELECT id,file_type_name FROM hrm_file_type");
        return view('employee.employee_info')
            ->with('employee',$employee)
            ->with('employee_salary_grade',$employee_salary_grade)
            ->with('editemployee',$editemployee)
            ->with('file_type',$file_type)
            ->with('approvalHistories',$approvalHistories);
    }

    public function edit_employeeinfo($id)
    {
        $employee = HrmEmployee::find($id);
        if (empty($employee)) {
            session()->flash('alert-danger', 'Invalid employee !!');
            return Redirect()->back();
        }

        $editemployee = DB::select("SELECT a.id,
                                a.employee_name,
                                a.nickname,
                                a.gender,
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
                                a.contact_number,
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
                                p.education_name,
                                q.id as hrm_plant_id,
                                q.plant_name,
                                a.Images,
                                b.insurance,
                                s.period,
                                b.official_email,
                                b.official_contact_no,
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
                                 ") ;

        if (empty($editemployee)) {
            dd("Sorry This Employee Information Can not be Editable");
        }

         $employee_salary_grade='';
         if(config('module_config.payroll_module') == 1) {

            $hrm_employee_job_info_id   = $editemployee[0]->hrm_employee_job_info_id;
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

        return view('employee.edit_employee_info')
            ->with('employee',$employee)
            ->with('editemployee',$editemployee)
            ->with('probation',HrmProbationPeriod::all())
            ->with('employee_salary_grade',$employee_salary_grade);
    }



    public function modify_employeeinfo(Request $request)
    {

            if($request->emp_info==1){
                $validator = Validator::make($request->all(), [
                    'employee_name'     => 'required',
                    'present_address'   => 'required',
                    'permanent_address' => 'required',
                    'contact_number'    => 'required',
                    'gender'            => 'required',
                    // 'religion'          => 'required',
                    // 'marital_status'    => 'required',
                    'blood_group'       => 'required',
                    'date_of_birth'     => 'required',
                    'nid'               => 'required',
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'depertment'        => 'required|exists:hrm_depertment,id',
                    'designation'       => 'required|exists:hrm_designation,id',
                    // 'category'          => 'required|exists:hrm_category,id',
                    'jod_location'      => 'required|exists:hrm_location,id',
                    // 'section'           => 'required|exists:hrm_section,id',
                    'working_shift'     => 'required|exists:hrm_shift,id',
                    'overtime'          => 'required',
                    'manage_by'         => 'required|exists:hrm_employee,id',
                    'employeestatus'    => 'required|exists:hrm_employment_status,id',
                    'basic_salary'      => 'required',
                ]);
            }

            if ($validator->fails()) {
                return Redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date)));
            $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->confirmation_date)));



            $employee = HrmEmployee::find($request->employee_id);

            $dob      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_of_birth)));
            $employeeImage =DB::select("SELECT Images from hrm_employee  Where id= $request->employee_id");
            $fileName= $employeeImage[0]->Images;

            if($request->emp_info==1) {
                DB::beginTransaction();
                try {
                    $employee->employee_name          = $request->employee_name;
                    $employee->nickname                = $request->nickname;
                    $employee->contact_number         = $request->contact_number;
                    $employee->father_name            = $request->father_name;
                    $employee->mother_name            = $request->mothers_name;
                    $employee->dob                    = $dob;
                    $employee->email                  = $request->email;
                    $employee->nid                    = $request->nid;
                    $employee->tin                    = $request->tin;
                    $employee->passport               = '';
                    $employee->present_address        = $request->present_address;
                    $employee->permanent_address      = $request->permanent_address;
                    $employee->gender                 = $request->gender ;
                    $employee->hrm_blood_group_id     = $request->blood_group ;
                    $employee->hrm_religion_id        = $request->religion ;
                    $employee->hrm_marital_status_id  = $request->marital_status;
                    $employee->hrm_education_id         = $request->hrm_education_id;
                    $employee->Images                 = $fileName;
                    $employee->active_status          = 1;
                    $employee->users_id               = Auth::user()->id;

                    //Image Update
                    $fileName= $employee->Images;

                    if(!empty($request->images)) {

                        $findImg = public_path().'/employee_image/'.$fileName;
                        if (file_exists($findImg)) {
                            @unlink($findImg);
                        }
                        $valid_exts = array('jpeg', 'jpg', 'png', 'gif');     // valid extensions
                        $max_size = 2000 * 1024;                              // max file size (200kb)
                        $path = public_path() . '/employee_image/'; // upload directory
                        $file = $request->images;
                        $ext  = $file->guessClientExtension();
                        // $size = $file->getClientSize();
                        $name = $this->generateRandomString();
                        // if (in_array($ext, $valid_exts) AND $size < $max_size){
                            if ($file->move($path, $name)){
                                $status = 'Image successfully uploaded!';
                                $fileName = $name;
                            }
                        // }
                        $employee->Images                 = $fileName;
                    }else{
                        $employee->Images                 = $fileName;
                    }
                    //End Image Update
                    $employee->save();

                    DB::commit();

                    $this->recordActivity(
                         1,
                         'Updated Employee Personal Informations',
                         $employee->getChanges(),
                         $employee->id,
                         'hrm_employee'
                    );

                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(array(
                        'success'           => false,
                        'error_messages'    => true,
                        'errors'          => "insert problem !! " . $e->getMessage()
                    ));
                }
                return response()->json(array(
                    'success'    => true,
                    'messages'   => 'Successfully Updated'
                ));
            }

            if($request->emp_info==2){


                        DB::beginTransaction();
                        try {


                        $employeejobinfo = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_id)
                                                             ->where('employee_activity', 1)
                                                             ->first();

                        $employeejobinfo->hrm_employee_id            = $request->get('employee_id');
                        $employeejobinfo->employee_code              = $request->get('employee_code');
                        $employeejobinfo->official_email             = $request->get('official_email');
                        $employeejobinfo->official_contact_no        = $request->get('official_contact_no');
                        $employeejobinfo->hrm_depertment_id          = $request->get('depertment');
                        $employeejobinfo->hrm_designation_id         = $request->get('designation');
                        $employeejobinfo->hrm_category_id            = $request->get('category');
                        $employeejobinfo->hrm_employment_status_id   = $request->get('employeestatus');
                        $employeejobinfo->hrm_manage_by_id           = $request->get('manage_by');
                        $employeejobinfo->employee_shift_status      = 1;
                        $employeejobinfo->overtime_status            = $request->get('overtime');
                        $employeejobinfo->employee_activity          = 1;
                        $employeejobinfo->basic_salary               = $request->get('basic_salary');
                        $employeejobinfo->hrm_location_id            = $request->get('jod_location');
                        $employeejobinfo->hrm_section_id             = $request->get('section');
                        $employeejobinfo->hrm_plant_id               = $request->get('plant_name');
                        $employeejobinfo->insurance                  = $request->get('insurance');
                        $employeejobinfo->users_id                   = Auth::user()->id;
                        $employeejobinfo->save();



                        // $employeejoining = HrmEmployeeJoining::where('hrm_employee_id', $request->employee_id)->first();
                        // $employeejoining = HrmEmployeeJoining::find($employeejoining->id);
                        // $employeejoining->hrm_employee_id    = $request->get('employee_id');
                        // $employeejoining->joining_date       = $joining_date;
                        // if($request->employeestatus==1){
                        // $employeejoining->confirmation_date  = null;
                        // }else{
                        // $employeejoining->confirmation_date  = $confirmation_date;
                        // }
                        // $employeejoining->save();

                   if($request->employeestatus==1){
                        DB::UPDATE("UPDATE hrm_employee_joining SET joining_date='$joining_date',confirmation_date=null WHERE hrm_employee_id= $request->employee_id");
                    }else{

                        DB::UPDATE("UPDATE hrm_employee_joining SET joining_date='$joining_date',confirmation_date='$confirmation_date' WHERE hrm_employee_id= $request->employee_id");
                    }





                if ($request->employeestatus==1){

                    DB::table('hrm_employee_probation')->where('hrm_employee_id', '=', $request->employee_id)->delete();


                    $insert_probation      = new HrmEmployeeProbation;
                    $insert_probation->hrm_employee_id          = $request->employee_id;
                    $insert_probation->hrm_probation_period_id  = $request->probation_period;
                    $insert_probation->users_id                 = Auth::user()->id;

                    $insert_probation->save();

                }

                    // $jobid           = $request->hrm_employee_job_info_id;
                    // $insert_activity = HrmEmployeeActivity::where('hrm_employee_job_info_id',$jobid)->first();
                    // $insert_activity = HrmEmployeeActivity::find($insert_activity->id);
                    // $insert_activity->start_date    = $joining_date;
                    // $insert_activity->save();

                $jobid = $request->hrm_employee_job_info_id;
                DB::update("UPDATE  hrm_employee_activity SET start_date='$joining_date' WHERE hrm_employee_job_info_id =$jobid AND hrm_employee_activity_status_id=1");


                // SHIFT CHANGE NOT PERMITE FROM HERE,Problem in date maintain (NOMAN) any query Knock Me.

                //         //Check  Shift Data
                //         $employee_shift_id= $request->get('working_shift');
                //         $employee_jobinfo_id= $request->get('hrm_employee_job_info_id');
                //         $isExitDatabase =DB::select("SELECT id from hrm_employee_shift  Where hrm_employee_job_info_id=$employee_jobinfo_id and hrm_shift_id=$employee_shift_id and end_date is null");
                // //End Check Data

                //     if (empty($isExitDatabase)){

                //         $ldate = date('Y-m-d H:i:s');

                //         $insert_shift  = new HrmEmployeeShift;
                //         $insert_shift->hrm_employee_job_info_id = $request->get('hrm_employee_job_info_id');
                //         $insert_shift->hrm_shift_id             = $request->get('working_shift');
                //         $insert_shift->start_date               = $ldate;
                //         $insert_shift->valid                    = 1;
                //         $insert_shift->save();



                //         $employeeshift   = HrmEmployeeShift::where('hrm_employee_job_info_id', $request->hrm_employee_job_info_id)
                //                                            ->where('end_date',null)->first();

                //         $employeeshift->end_date                 = $ldate;
                //         $employeeshift->valid                    = 1;
                //         $employeeshift->save();


                //         }


                if (config('module_config.payroll_module') == 1) {
                    $salary_grade_master  = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first();
                    // dd($salary_grade_master);
                    // dd($salary_grade_master);
                    $update_salary_master  = HrmEmployeeSalary::where('hrm_employee_job_info_id',$request->hrm_employee_job_info_id)->first();
                    if($update_salary_master){
                        $update_salary_master  = HrmEmployeeSalary::find($update_salary_master->id);
                        $update_salary_master->hrm_salary_grade_master_id = $salary_grade_master->id;
                        $update_salary_master->save();

                    }

                }

                DB::commit();

                $this->recordActivity(
                    1,
                    'Updated Employee Official Informations',
                    $employeejobinfo->getChanges(),
                    $employeejobinfo->id,
                    'hrm_employee_job_info'
                );


                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(array(
                        'success'           => false,
                        'error_messages'    => true,
                        // 'errors'          => "insert problem !! " . $e->getTrace()
                        'errors'          => $e->getTrace()
                    ));
                }
                // $request->session()->flash('alert-success', 'data has been successfully update!');
                // return Redirect::to('employee');
                return response()->json(array(
                    'success'    => true,
                    'messages'   => 'Successfully Updated'
                ));

            }
    }





    public function print_employeeinfo($id)
    {
        $employee = HrmEmployee::find($id);
        if (empty($employee)){
            session()->flash('alert-danger', 'Invalid employee !!');
            return Redirect()->back();
        }

        $editemployee = DB::select("SELECT a.id,
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
                                s.period,
                                b.official_email,
                                b.official_contact_no
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
                                LEFT JOIN hrm_probation_period s ON r.hrm_probation_period_id = s.id");


        $hrm_employee_job_info_id = $editemployee[0]->hrm_employee_job_info_id;
        $employee_salary_grade    = '';


        if(config('module_config.payroll_module') == 1){
            $hrm_employee_salary_info = HrmEmployeeSalary::where('hrm_employee_job_info_id',$hrm_employee_job_info_id)->first();
            if (isset($hrm_employee_salary_info)) {
                $employee_salary_grade    = DB::SELECT("SELECT
                                                    b.id,
                                                    b.grade_name
                                                FROM
                                                    hrm_salary_grade_master a
                                                        JOIN
                                                    hrm_salary_grade b ON b.id = a.hrm_salary_grade_id
                                                        AND a.id=$hrm_employee_salary_info->hrm_salary_grade_master_id");
            }


         }

        return view('employee.employee_info_print')
            ->with('employee',$employee)
            ->with('employee_salary_grade',$employee_salary_grade)
            ->with('editemployee',$editemployee);




        // $jasper_server = new Client(
        //     Config::get('configaration.jasperjasper_url'),
        //     Config::get('configaration.jasper_user'),
        //     Config::get('configaration.jasper_password')
        // );

        // $parameter = " AND (a.id) = '". $id . "'";




        // $controls = array(
        //     'company_name'          => Config::get('configaration.company_name'),
        //     'address'               => Config::get('configaration.company_address'),
        //     'condition_parameter'   => $parameter,

        // );


        // $report_path = Config::get('configaration.report_path').'hrm_employee_info_dtls';
        // $exporttype  = "pdf";
        // $report      = $jasper_server->reportService()->runReport($report_path, $exporttype,null,null,$controls);

        // if (strlen($report) > 940){
        //     header('Content-Transfer-Encoding: binary');
        //     header('Content-Length: ' . strlen($report));
        //     header('Content-Type: application/'.$exporttype);
        //     echo $report;
        //     echo $report;
        //     echo $report;
        //     echo $report;
        //     echo "application/pdf;base64, " . $report;
        // }else{  dd("No data found");
        // }

    }}
