<?php

namespace App\Http\Controllers;

use App\Models\SalaryHoldupApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SalaryHoldupApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('salary_holdup_application.holdup_application');
    }

    public function holdup_application_list()
    {
        $status =  request('status');
        $holdup_application = DB::SELECT("SELECT
                                            a.id,
                                            a.note,
                                            a.approved_status,
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
                                            a.approved_status = $status
                                        ");

        return json_encode(array('data' => $holdup_application));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('salary_holdup_application.create_holdup_application');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name'    => 'required',
            'holdup_types'     => 'required',
            'note'             => 'required',
            'date'             => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('holdup_application/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $date = explode('-', $request->date);
        $year  = $date[1];
        $month = Carbon::parse($date[0])->month;

        $insert = new SalaryHoldupApplication();
        $insert->hrm_holdup_types_id        = $request->holdup_types;
        $insert->hrm_employee_id            = $request->employee_name;
        $insert->note                       = $request->note;
        $insert->year_id                    = $year;
        $insert->hrm_month_id               = $month;
        $insert->approved_status            = 2;
        $insert->create_by_users_id         = auth()->id();
        $insert->created_at                 = now();
        $insert->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holdup_application');
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
                                        a.id = $id
                                    ")[0];
        if (empty($edit_data)){
            session()->flash('alert-danger', 'Invalid Holiday Information !!');
            return Redirect()->back();
        }

        return view('salary_holdup_application.edit_holdup_application')
            ->with('edit_data',$edit_data);
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
            'holdup_types'     => 'required',
            'note'             => 'required',
            'date'             => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $date = explode('-', $request->date);
        $year  = $date[1];
        $month = Carbon::parse($date[0])->month;

        $update = SalaryHoldupApplication::find($id);
        $update->hrm_holdup_types_id        = $request->holdup_types;
        $update->note                       = $request->note;
        $update->year_id                    = $year;
        $update->hrm_month_id               = $month;
        $update->updated_at                 = now();
        $update->save();


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holdup_application');
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

    public function cancel(Request $request,$id)
    {
        $update = SalaryHoldupApplication::find($id);
        $update->deleted_by_users_id        = auth()->id();
        $update->is_active                  = 0;
        $update->save();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('hrm_holdup_employees_salaries');
    }

    public function holdup_application_get_data(Request $request)
    {
        $id = $request->id;
        $data = DB::SELECT("SELECT
                                    a.id,
                                    a.note,
                                    b.id as employee_id,
                                    c.id as holdup_id,
                                    c.holdup_types_name,
                                    concat_ws('-', d.month_name, a.year_id) as date,
                                    b.employee_name,
                                    a.approved_status
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
                                    a.id = $id
                                ")[0];
        return response()->json($data);
    }

    public function holdup_application_action(Request $request)
    {
        if ($request->action_type == 'approve') {
            $update = SalaryHoldupApplication::find($request->id);
            $update->approved_status        = 1;
            $update->save();

            return response()->json([
                'success' => true,
                'status'  => 1,
                'message' => "Successfully Approved"
            ]);
        } elseif ($request->action_type == 'reject') {
            $update = SalaryHoldupApplication::find($request->id);
            $update->approved_status        = 0;
            $update->save();

            return response()->json([
                'success' => true,
                'status'  => 0,
                'message' => "Rejected!"
            ]);
        }
    }


}
