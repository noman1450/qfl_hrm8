<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SalaryHoldupApplication;
use Validator;
use Carbon\Carbon;
use Redirect;

class SalaryHoudup_realiseController extends Controller
{
    /**
     * Show unpaid list view.
     */
    public function unpaidListView()
    {
        return view('salary_holdup_application.unpaid_list');
    }

    /**
     * Show paid list view.
     */
    public function paidListView()
    {

        // dd("go to view page for data load");
        return view('salary_holdup_application.paid_list');
    }


    public function holdup_paid_list()
    {

        $holdup_application = DB::SELECT("SELECT
                                                a.id,
                                                a.note,
                                                a.approved_status,
                                                a.is_paid,
                                                b.employee_name,
                                                c.holdup_types_name,
                                                CONCAT_WS('-', d.month_name, a.year_id) AS date,
                                                e.name AS paid_by_username,
                                                a.paid_date
                                            FROM
                                                hrm_holdup_employees_salaries AS a
                                                    JOIN
                                                hrm_employee AS b ON a.hrm_employee_id = b.id
                                                    AND a.is_active = 1
                                                    AND a.approved_status = 1
                                                    AND a.is_paid = 1
                                                    JOIN
                                                hrm_holdup_types AS c ON a.hrm_holdup_types_id = c.id
                                                    JOIN
                                                hrm_month AS d ON a.hrm_month_id = d.id
                                                    JOIN
                                                users e ON a.paid_by_user_id = e.id");

        return response()->json(['data' => $holdup_application]);
    }




    /**
     * Fetch Hold Salary applications list based on status.
     */
    public function holdup_application_lists(Request $request)
    {

        $status = $request->status;

        $holdup_application = DB::SELECT("SELECT
                                            a.id,
                                            a.note,
                                            a.approved_status,
                                            a.is_paid,
                                            b.employee_name,
                                            c.holdup_types_name,
                                            concat_ws('-', d.month_name, a.year_id) as date
                                        FROM
                                            hrm_holdup_employees_salaries AS a
                                                JOIN
                                            hrm_employee AS b ON a.hrm_employee_id = b.id
                                                JOIN
                                            hrm_holdup_types AS c ON a.hrm_holdup_types_id = c.id
                                                JOIN
                                            hrm_month AS d ON a.hrm_month_id = d.id
                                        WHERE
                                            a.is_active = 1
                                                AND
                                            a.approved_status = 1
                                                AND
                                            a.is_paid = $status
                                        ");

        return response()->json(['data' => $holdup_application]);
    }

    /**
     * Store new hold salary application.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'holdup_types'  => 'required',
            'note'          => 'required',
            'date'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $date = explode('-', $request->date);
        $year  = $date[1];
        $month = Carbon::parse($date[0])->month;

        $insert = new SalaryHoldupApplication();
        $insert->hrm_holdup_types_id = $request->holdup_types;
        $insert->hrm_employee_id     = $request->employee_name;
        $insert->note                = $request->note;
        $insert->year_id             = $year;
        $insert->hrm_month_id        = $month;
        $insert->approved_status     = 2;
        $insert->create_by_users_id  = auth()->id();
        $insert->created_at          = now();
        $insert->save();

        $request->session()->flash('alert-success', 'Data has been successfully added!');
        return Redirect::to('holdup_application');
    }

    /**
     * Edit a hold salary application.
     */
    public function edit($id)
    {
        $edit_data = DB::SELECT("SELECT
                                    a.id,
                                    a.note,
                                    b.id as employee_id,
                                    c.id as holdup_id,
                                    c.holdup_types_name,
                                    concat_ws('-', d.month_name, a.year_id) as date,
                                    concat_ws(' | ', b.employee_name, e.employee_code, b.contact_number) as employee_name
                                FROM
                                    hrm_holdup_employees_salaries AS a
                                        JOIN
                                    hrm_employee AS b ON a.hrm_employee_id = b.id
                                        JOIN
                                    hrm_holdup_types AS c ON a.hrm_holdup_types_id = c.id
                                        JOIN
                                    hrm_month AS d ON a.hrm_month_id = d.id
                                        JOIN
                                    hrm_employee_job_info e  ON b.id = e.hrm_employee_id
                                WHERE
                                    a.id = ?
                                ", [$id])[0] ?? null;

        if (empty($edit_data)) {
            session()->flash('alert-danger', 'Invalid Holdup Application !!');
            return Redirect()->back();
        }

        return view('salary_holdup_application.edit_holdup_application')
            ->with('edit_data', $edit_data);
    }

    // /**
    //  * Update a hold salary application.
    //  */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'holdup_types' => 'required',
            'note'         => 'required',
            'date'         => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $date = explode('-', $request->date);
        $year  = $date[1];
        $month = Carbon::parse($date[0])->month;

        $update = SalaryHoldupApplication::find($id);
        $update->hrm_holdup_types_id = $request->holdup_types;
        $update->note                = $request->note;
        $update->year_id             = $year;
        $update->hrm_month_id        = $month;
        $update->updated_at          = now();
        $update->save();

        $request->session()->flash('alert-success', 'Data has been successfully updated!');
        return Redirect::to('holdup_application');
    }

    // /**
    //  * Delete a hold salary application.
    //  */
    public function destroy($id)
    {
        $update = SalaryHoldupApplication::find($id);
        $update->deleted_by_users_id = auth()->id();
        $update->is_active           = 0;
        $update->save();

        session()->flash('alert-success', 'Successfully deleted!');
        return Redirect::to('hrm_holdup_employees_salaries');
    }

    // /**
    //  * Mark as paid.
    //  */
    public function holdup_application_action(Request $request)
    {

        if($request->action_type=='approve'){
            $approved_status = 1;
        }else{
            $approved_status = 0;
        }

        $update = SalaryHoldupApplication::find($request->id);
        $update->approved_status = $approved_status;
        $update->approved_by_users_id = auth()->id();
        $update->updated_at = date('Y-m-d H:i:s');
        $update->save();

        return response()->json([
            'success' => true,
            'status'  => 1,
            'message' => "Successfully Updated Paid Status",
        ]);
    }


    public function holdup_paid_action(Request $request)
    {

        $update = SalaryHoldupApplication::find($request->id);
        $update->is_paid = 1;
        $update->paid_by_user_id = auth()->id();
        $update->paid_date = date('Y-m-d H:i:s');
        $update->save();

        return response()->json([
            'success' => true,
            'status'  => 1,
            'message' => "Successfully Updated Paid Status",
        ]);
    }
}
