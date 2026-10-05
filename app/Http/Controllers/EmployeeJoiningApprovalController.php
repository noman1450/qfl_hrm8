<?php

namespace App\Http\Controllers;

use App\Models\HrmEmployeeJobApproval;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeCardCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeJoiningApprovalController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $location = "";

            if (request()->location) {
                $location = "AND b.hrm_location_id= ".request()->location;
            }

            $user_id = auth()->id();

            $forwardTo = " AND job_users_id = $user_id";

            if (auth()->user()->user_type == 2) {
                $forwardTo = "";
            }
           

            $data = DB::select("SELECT
                    a.id,
                    CONCAT(a.employee_name, ' | ', IFNULL(b.employee_code, ' ')) AS employee_name,
                    c.location_name,
                    b.id hrm_employee_job_info_id,
                    LPAD(a.id, 5, '0') AS Unique_Code,
                    d.depertment_name,
                    e.designation_name,
                    h.category_name,
                    a.contact_number,
                    DATE_FORMAT(f.joining_date, '%d-%m-%Y') AS joining_date,
                    e.priority,
                    a.Images image
                FROM
                    hrm_employee a
                        JOIN
                    hrm_employee_job_info b ON a.id = b.hrm_employee_id
                        AND a.active_status = 1
                        AND b.employee_activity = 10
                        JOIN
                    hrm_location c ON b.hrm_location_id = c.id
                        JOIN
                    hrm_depertment d ON b.hrm_depertment_id = d.id
                        JOIN
                    hrm_designation e ON b.hrm_designation_id = e.id
                        JOIN
                    hrm_category h ON b.hrm_category_id = h.id
                        JOIN
                    hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                        JOIN
                    user_location g ON b.hrm_location_id = g.hrm_location_id
                        AND g.users_id = $user_id $location
                        AND b.id IN
                    (SELECT hrm_employee_job_info_id FROM hrm_employee_job_approval WHERE action_type = 1 and forward = 1 $forwardTo)
                    GROUP By b.id
                    ORDER BY a.id DESC
            ");

            return datatables()->of($data)
                ->addColumn('Link', function($data) {
                    return '
                    <a
                        href="'.url('employee_joining_approvals/'.encrypt($data->hrm_employee_job_info_id).'/get_form').'"
                        class="btn btn-info btn-sm float-end"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        Action
                    </a>';
                })
                ->rawColumns(['Link'])
                ->make(true);
        }

        return view('employee_joining_approval.index');
    }

    public function getForm($id)
    {
        $id = decrypt($id);

        $data['employee'] = DB::select("SELECT
                a.id,
                LPAD(a.id, 5, '0') AS unique_Code,
                a.employee_name,
                a.nickname,
                a.email,
                a.gender,
                a.tin,
                a.hrm_education_id,
                b.id AS hrm_employee_job_info_id,
                c.id AS location_id,
                c.location_name,
                d.id AS department_id,
                d.depertment_name,
                e.id AS designation_id,
                e.designation_name,
                b.basic_salary,
                b.employee_code,
                a.contact_number,
                f.joining_date,
                f.confirmation_date,
                CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Year(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' Month(s) ') AS jobduration,
                g.id AS employeestatus_id,
                g.employment_status AS employeestatus_name,
                h.id AS category_id,
                h.category_name,
                i.id AS sectionid,
                i.section_name,
                b.overtime_status,
                j.id AS manage_by_id,
                j.employee_name AS manage_by_name,
                l.id AS shift_id,
                l.shift_name,
                m.id AS religion_id,
                m.religion,
                n.id AS marital_status_id,
                n.marital_status,
                o.id AS blood_group_id,
                o.blood_group,
                p.education_name,
                q.plant_name,
                a.Images image,
                a.dob,
                a.nid,
                a.father_name,
                a.mother_name,
                a.present_address,
                a.permanent_address,
                b.insurance,
                s.period,
                b.official_email,
                b.official_contact_no

            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    AND b.id = $id
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
                    JOIN
                hrm_section i ON b.hrm_section_id = i.id
                    JOIN
                hrm_employee j ON b.hrm_manage_by_id = j.id
                    JOIN
                hrm_employee_shift k ON b.id = k.hrm_employee_job_info_id
                    AND k.id IN (SELECT MAX(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id = b.id)
                    JOIN
                hrm_shift l ON k.hrm_shift_id = l.id
                    JOIN
                hrm_religion m ON m.id = a.hrm_religion_id
                    JOIN
                hrm_marital_status n ON n.id = a.hrm_marital_status_id
                    JOIN
                hrm_blood_group o ON o.id = a.hrm_blood_group_id
                    JOIN
                hrm_education p ON p.id = a.hrm_education_id
                    JOIN
                hrm_plant q ON q.id = b.hrm_plant_id
                    LEFT JOIN
                hrm_employee_probation r ON r.hrm_employee_id = a.id
                    LEFT JOIN
                hrm_probation_period s ON r.hrm_probation_period_id = s.id
            LIMIT 1
        ")[0];

        $data['approvalHistories'] = DB::select("SELECT
                a.id,
                a.hrm_employee_job_info_id,
                a.comment,
                d.name forward_to,
                e.name forward_by,
                a.updated_at action_time
            FROM
                hrm_employee_job_approval a
                    JOIN
                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                    AND a.hrm_employee_job_info_id = $id
                    -- AND a.action_type = 0
                    -- AND a.forward = 0
                    JOIN
                users d ON a.job_users_id = d.id
                    JOIN
                users e ON a.users_id = e.id
        ");

        return view('employee_joining_approval.approval_form', $data);
    }

    public function action(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'forward' => 'required',
            'forward_employee_name' => 'nullable|required_if:forward,1'
        ], [
            'forward_employee_name.required_if' => 'This field is required.'
        ]);

        $hrm_employee_job_info_id = decrypt($request->hrm_employee_job_info_id);

        $employeeJobApproval = HrmEmployeeJobApproval::query()
            ->where('hrm_employee_job_info_id', $hrm_employee_job_info_id)
            ->latest('id')
            ->first();

        if ($request->action == 2) { // Rejected..
            HrmEmployeeJobApproval::create([
                'action_type' => 2,
                'comment' => $request->comment,
                'forward' => 2,
                'hrm_employee_job_info_id' => $hrm_employee_job_info_id,
                'job_users_id' => auth()->id(),
                'users_id' => auth()->id()
            ]);

            $employeeJobApproval->update([
                'forward' => 0,
                'action_type' => 0
            ]);
        } else {
            if ($request->forward == 1) { // Forward..
                HrmEmployeeJobApproval::create([
                    'action_type' => 1,
                    'comment' => $request->comment,
                    'forward' => 1,
                    'hrm_employee_job_info_id' => $hrm_employee_job_info_id,
                    'job_users_id' => $request->forward_employee_name,
                    'users_id' => auth()->id()
                ]);

                $employeeJobApproval->update([
                    'forward' => 0,
                    'action_type' => 0
                ]);
            }

            else if ($request->forward == 2) { // Approved..
                HrmEmployeeJobApproval::create([
                    'action_type' => 1,
                    'comment' => $request->comment,
                    'forward' => 2,
                    'hrm_employee_job_info_id' => $hrm_employee_job_info_id,
                    'job_users_id' => auth()->id(),
                    'users_id' => auth()->id()
                ]);

                $employeeJobApproval->update([
                    'forward' => 0,
                    'action_type' => 0
                ]);

                HrmEmployeeJobInfo::query()
                    ->where('id', $hrm_employee_job_info_id)
                    ->update(['employee_activity' => 1]);

                $cardQuery = HrmEmployeeCardCode::where('hrm_employee_job_info_id',$hrm_employee_job_info_id)->first();

                if(empty($cardQuery)){

                    $empId = HrmEmployeeJobInfo::where('id',$hrm_employee_job_info_id)->first()->hrm_employee_id;

                    $insert_card     = new HrmEmployeeCardCode;
                    $insert_card->card_code  = $empId;
                    $insert_card->device_id  = 1;
                    $insert_card->hrm_employee_job_info_id = $hrm_employee_job_info_id;
                    $insert_card->save();
                }


            }
        }

        $request->session()->flash('alert-success', 'Data has been successfully added!');
        return redirect()->to('employee_joining_approvals');
    }
}
