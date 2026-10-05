<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;

use App\Models\HrmEmployeeLevelingMaster;
use App\Models\HrmEmployeeLevelingDetails;
use App\Models\HrmSalaryGrade;

class EmployeeLevelingController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the employee leveling master records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employee_leveling.employee_leveling_list');
    }

    /**
     * AJAX - return the list data for the DataTable.
     *
     * @return \Illuminate\Http\Response
     */
    public function employeeleveling_list(Request $request)
    {
        $data = DB::SELECT("SELECT
                                a.id,
                                a.Level_name,
                                a.users_id,
                                IF((a.valid = 1), 'Active', 'Deactive') status,
                                GROUP_CONCAT(CONCAT(b.grade_name) SEPARATOR '<br>') AS salary_grades
                            FROM hrm_employee_leveling_master a
                            LEFT JOIN hrm_employee_leveling_details d ON a.id = d.hrm_employee_leveling_master_id AND d.valid = 1
                            LEFT JOIN hrm_salary_grade b ON d.hrm_salary_grade_id = b.id
                            GROUP BY a.id, a.Level_name, a.users_id, a.valid");

        return json_encode(array('data' => $data));
    }

    /**
     * Show the form for creating a new employee leveling.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only active salary grades that are NOT already assigned to another
        // active leveling are shown in the checkbox table.
        $salary_grades = DB::SELECT("SELECT a.id, a.grade_name
                                     FROM hrm_salary_grade a
                                     WHERE a.valid = 1
                                       AND a.id NOT IN (
                                          SELECT b.hrm_salary_grade_id
                                          FROM hrm_employee_leveling_details b
                                          WHERE b.valid = 1
                                       )
                                     ORDER BY a.grade_name");

        return view('employee_leveling.create_employee_leveling')
                    ->with('salary_grades', $salary_grades);
    }

    /**
     * Store a newly created employee leveling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Level_name'    => 'required|max:45',
            'salary_grade'  => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Level Name and at least one Salary Grade is required!');
            return Redirect::to('employeeleveling/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        DB::beginTransaction();
        try {

            $insert_master = new HrmEmployeeLevelingMaster;
            $insert_master->Level_name  = $request->Level_name;
            $insert_master->created_at  = date('Y-m-d H:i:s');
            $insert_master->valid       = 1;
            $insert_master->users_id    = Auth::user()->id;
            $insert_master->save();

            foreach ($request->salary_grade as $salary_grade_id) {

                // Skip salary grades that are already assigned somewhere else.
                $already_used = HrmEmployeeLevelingDetails::where('valid', 1)
                                    ->where('hrm_salary_grade_id', $salary_grade_id)
                                    ->count();

                if ($already_used > 0) {
                    continue;
                }

                $insert_details = new HrmEmployeeLevelingDetails;
                $insert_details->hrm_employee_leveling_master_id = $insert_master->id;
                $insert_details->hrm_salary_grade_id             = $salary_grade_id;
                $insert_details->valid                           = 1;
                $insert_details->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', $e->getMessage());
            return Redirect::to('employeeleveling/create');
        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('employeeleveling');
    }

    /**
     * Show the form for editing the specified employee leveling.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $master = HrmEmployeeLevelingMaster::find($id);
        if (empty($master)) {
            session()->flash('alert-danger', 'Invalid Employee Leveling !!');
            return Redirect::to('employeeleveling');
        }

        $selected_grade_ids = DB::SELECT("SELECT b.id
                                          FROM hrm_employee_leveling_details a
                                          JOIN hrm_salary_grade b ON a.hrm_salary_grade_id = b.id
                                          WHERE a.hrm_employee_leveling_master_id = $id AND a.valid = 1");

        $selected_ids = array();
        foreach ($selected_grade_ids as $sg) {
            $selected_ids[] = $sg->id;
        }

        // Active salary grades that are NOT used in another active leveling,
        // plus the ones already in this leveling.
        $salary_grades = DB::SELECT("SELECT a.id, a.grade_name
                                     FROM hrm_salary_grade a
                                     WHERE a.valid = 1
                                       AND ( a.id NOT IN (
                                                SELECT b.hrm_salary_grade_id
                                                FROM hrm_employee_leveling_details b
                                                WHERE b.valid = 1
                                                  AND b.hrm_employee_leveling_master_id <> $id
                                            )
                                            OR a.id IN (
                                                SELECT c.hrm_salary_grade_id
                                                FROM hrm_employee_leveling_details c
                                                WHERE c.valid = 1
                                                  AND c.hrm_employee_leveling_master_id = $id
                                            )
                                          )
                                     ORDER BY a.grade_name");

        return view('employee_leveling.edit_employee_leveling')
                    ->with('master', $master)
                    ->with('selected_ids', $selected_ids)
                    ->with('salary_grades', $salary_grades);
    }

    /**
     * Update the specified employee leveling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Level_name'    => 'required|max:45',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Level Name is required!');
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        DB::beginTransaction();
        try {

            $master = HrmEmployeeLevelingMaster::find($id);
            if (empty($master)) {
                throw new \Exception('Invalid Employee Leveling');
            }
            $master->Level_name = $request->Level_name;
            $master->save();

            // Mark all existing details as deleted.
            DB::table('hrm_employee_leveling_details')
                ->where('hrm_employee_leveling_master_id', $id)
                ->update(['valid' => 0]);

            if (!empty($request->salary_grade) && is_array($request->salary_grade)) {
                foreach ($request->salary_grade as $salary_grade_id) {

                    $already_used = HrmEmployeeLevelingDetails::where('valid', 1)
                                        ->where('hrm_salary_grade_id', $salary_grade_id)
                                        ->where('hrm_employee_leveling_master_id', '<>', $id)
                                        ->count();

                    if ($already_used > 0) {
                        continue;
                    }

                    $existing = HrmEmployeeLevelingDetails::where('hrm_employee_leveling_master_id', $id)
                                    ->where('hrm_salary_grade_id', $salary_grade_id)
                                    ->first();

                    if (!empty($existing)) {
                        $existing->valid = 1;
                        $existing->save();
                    } else {
                        $insert_details = new HrmEmployeeLevelingDetails;
                        $insert_details->hrm_employee_leveling_master_id = $id;
                        $insert_details->hrm_salary_grade_id             = $salary_grade_id;
                        $insert_details->valid                           = 1;
                        $insert_details->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', $e->getMessage());
            return Redirect()->back();
        }

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('employeeleveling');
    }

    /**
     * Soft delete (valid = 0) the specified employee leveling.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $master = HrmEmployeeLevelingMaster::find($id);
        if (empty($master)) {
            session()->flash('alert-danger', 'Invalid Employee Leveling !!');
            return Redirect::to('employeeleveling');
        }

        $master->valid = 0;
        $master->save();

        DB::table('hrm_employee_leveling_details')
            ->where('hrm_employee_leveling_master_id', $id)
            ->update(['valid' => 0]);

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeleveling');
    }
}
