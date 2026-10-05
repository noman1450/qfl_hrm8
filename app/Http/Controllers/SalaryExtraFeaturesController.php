<?php

namespace App\Http\Controllers;

use App\Helpers\DateBetween;
use Illuminate\Http\Request;
use App\Models\HrmExtraFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HrmExtraFeatureDetails;
use App\Models\HrmSalaryDeduction;
use Illuminate\Support\Facades\Validator;

class SalaryExtraFeaturesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('salary_extra_features.salary_extra_feature_list')
            ->with('default_user_location',  $default_user_location);
    }

    public function salary_extra_listdata(Request $request)
    {
        // dd($request->all());
        $condition    = "a.active_status=1";


        if ($request->location != 0){
          $condition  = " b.hrm_location_id = ".$request->location;
        }


        if ($request->department_id != 0){
          $condition  =  $condition . " AND e.id = ".$request->department_id ;
        }

        if ($request->designation_id != 0){
          $condition  =  $condition . " AND f.id = ".$request->designation_id ;
        }


        if ($request->employee_id != 0){
          $condition  =  $condition . " AND a.id = ".$request->employee_id ;
        }

        $adjustType  = $request->adjust_type;
        $percentage  = $request->percentage;
        $percentageValue = 0;
        if($adjustType == 1){
            $percentageValue = $percentage/100;
        }

        // dd($percentageValue);
        $amount  = $request->amount;
        $purpose = $request->purpose;


        $data   = DB::select("SELECT
                                b.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                b.basic_salary,
                                f.designation_name,
                                g.location_name,
                                IF($adjustType = 2, '$amount', (round(b.basic_salary * $percentageValue))) AS amount,
                                '$purpose' as purpose

                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                   Where
                                        $condition
                            ");

        return json_encode(array('data' => $data));

    }

    public function role_salaryextra_listdata(Request $request)
    {
        $condition = '';
        if($request->location!=0){
            $condition = 'AND a.hrm_location_id='.$request->location;
        }


        $data   = DB::select("SELECT
                                    a.id,
                                    concat(c.month_name,'-',year(a.month_from),' To ', d.month_name,'-',year(a.month_to) ) AS month_duration,
                                    a.purpose,
                                    b.salary_head,
                                    j.location_name,
                                    GROUP_CONCAT(CONCAT(g.employee_name,' || ',f.employee_code)   SEPARATOR '<br>') AS employee_name,
                                    GROUP_CONCAT(CONCAT(h.designation_name)  SEPARATOR '<br>') AS designation_name,
                                    GROUP_CONCAT(CONCAT(i.depertment_name)  SEPARATOR '<br>') AS depertment_name,
                                    GROUP_CONCAT(CONCAT(e.amount)  SEPARATOR '<br>') AS amount
                                FROM
                                    hrm_salary_extra_feature a
                                        JOIN
                                    hrm_salary_head b ON a.hrm_salary_head_id = b.id
                                        $condition
                                        JOIN
                                    hrm_month c ON MONTH(a.month_from) = c.id
                                        JOIN
                                    hrm_month d ON MONTH(a.month_to) = d.id
                                        JOIN
                                    hrm_salary_extra_feature_details e ON a.id = e.hrm_salary_extra_feature_id
                                        JOIN
                                    hrm_employee_job_info f ON e.hrm_employee_job_info_id = f.id AND f.employee_activity = 1
                                        JOIN
                                    hrm_employee g ON f.hrm_employee_id = g.id
                                        JOIN
                                    hrm_designation h ON f.hrm_designation_id = h.id
                                        JOIN
                                    hrm_depertment i ON f.hrm_depertment_id = i.id
                                        JOIN
                                    hrm_location  j ON a.hrm_location_id = j.id
                                GROUP BY a.id, a.purpose,b.salary_head,c.month_name,d.month_name,a.month_from,a.month_to ");

        return json_encode(array('data' => $data));

    }

    public function create()
    {
        $user_id = Auth::user()->id;
        $user_location = DB::select("SELECT a.id,a.location_name,b.default_location FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

        return view('salary_extra_features.create_salary_extra_features')
            ->with('user_location',  $user_location) ;
    }


    public function store(Request $request)
    {



        if($request->id == null) {

            return back()->with('alert-danger', 'Please Select Employee and Resubmit!');
        }

        $validator = Validator::make($request->all(), [
            'date_from'         => 'required',
            'date_to'           => 'required',
            'purpose'           => 'required',
            'salary_head'       => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $user_id = Auth::user()->id;



        DB::beginTransaction();
        try {
            $insert     = new HrmExtraFeature;
            $insert->month_from         = $date_from;
            $insert->month_to           = $date_to;
            $insert->purpose            = $request->purpose;
            $insert->hrm_salary_head_id = $request->salary_head;
            $insert->users_id           = $user_id;
            $insert->hrm_location_id    = $request->location;
            $insert->save();

            $count_row  = count($request->id);
            for($r = 0; $r <$count_row; $r++) {
                $insert_details     = new HrmExtraFeatureDetails;
                $insert_details->hrm_salary_extra_feature_id = $insert->id;
                $insert_details->hrm_employee_job_info_id    = $request->id[$r];
                $insert_details->amount                      = $request->amount[$request->id[$r]];
                $insert_details->save();

                $this->recordActivity(
                     1,
                     'Created Advance Adjust Multiple',
                     $insert_details,
                     $insert_details->id,
                     'hrm_salary_extra_feature_details'
                );
            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Advance Adjust Multiple',
                 $insert,
                 $insert->id,
                 'hrm_salary_extra_feature'
            );



        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }

        return redirect()->to('salaryextrafeatures')
            ->with('alert-success', 'Successfully Insert!');
    }

    public function salaryextrafeaturesupdate(Request $request)
    {
       if($request->hrm_employee_job_info_id == null) {
            return back()->with('alert-danger', 'Please Select Employee and Resubmit!');
        }


        $validator = Validator::make($request->all(), [
            'date_from'         => 'required',
            'date_to'           => 'required',
            'purpose'           => 'required',
            'salary_head'       => 'required',
        ]);


        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $user_id = Auth::user()->id;


        DB::beginTransaction();
        try {
            DB::table('hrm_salary_extra_feature_details')->where('hrm_salary_extra_feature_id', $request->id)->delete();
            DB::table('hrm_salary_extra_feature')->where('id', $request->id)->delete();

            $insert     = new HrmExtraFeature;
            $insert->month_from         = $date_from;
            $insert->month_to           = $date_to;
            $insert->purpose            = $request->purpose;
            $insert->hrm_salary_head_id = $request->salary_head;
            $insert->users_id           = $user_id;
            $insert->hrm_location_id    = $request->location;
            $insert->save();


            $count_row  = count($request->hrm_employee_job_info_id);
            for($r = 0; $r <$count_row; $r++) {
                $insert_details     = new HrmExtraFeatureDetails;
                $insert_details->hrm_salary_extra_feature_id = $insert->id;
                $insert_details->hrm_employee_job_info_id    = $request->hrm_employee_job_info_id[$r];
                // $insert_details->amount                      = $request->amount[$request->id[$r]];
                $insert_details->amount                      = $request->amount[$r];
                $insert_details->save();

                $this->recordActivity(
                     1,
                     'Updated Salary Advance Adjust Multiple',
                     $insert_details,
                     $insert_details->id,
                     'hrm_salary_extra_feature_details'
                );


            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated Salary Advance Adjust Multiple',
                 $insert,
                 $insert->id,
                 'hrm_salary_extra_feature'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }

        return redirect()->to('salaryextrafeatures')
            ->with('alert-success', 'Successfully Updated!');
    }





    public function edit($id)
    {
        $check   = DB::table('hrm_salary_extra_feature')->where('id', $id)->where('generate_type', 2)->first();

        if (!empty($check)) {
            return back()->with('alert-danger', 'Sorry Already Generated With Salary !!');
        }

        $edit_data = DB::SELECT("SELECT
                                        a.id,
                                        a.month_from,
                                        a.month_to,
                                        a.purpose,
                                        f.basic_salary,
                                        b.salary_head,
                                        CONCAT(g.employee_name, ' || ', f.employee_code) AS employee_name,
                                        h.designation_name,
                                        i.depertment_name,
                                        e.amount,
                                        f.id AS hrm_employee_job_info_id,
                                        b.id AS salary_head_id,
                                        e.id as details_id
                                    FROM
                                        hrm_salary_extra_feature a
                                            JOIN
                                        hrm_salary_head b ON a.hrm_salary_head_id = b.id AND a.id = $id
                                            JOIN
                                        hrm_month c ON MONTH(a.month_from) = c.id
                                            JOIN
                                        hrm_month d ON MONTH(a.month_to) = d.id
                                            JOIN
                                        hrm_salary_extra_feature_details e ON a.id = e.hrm_salary_extra_feature_id
                                            JOIN
                                        hrm_employee_job_info f ON e.hrm_employee_job_info_id = f.id
                                            AND f.employee_activity = 1
                                            JOIN
                                        hrm_employee g ON f.hrm_employee_id = g.id
                                            JOIN
                                        hrm_designation h ON f.hrm_designation_id = h.id
                                            JOIN
                                        hrm_depertment i ON f.hrm_depertment_id = i.id   ");


          $master_data = DB::SELECT("SELECT
                                        a.id,
                                        a.month_from,
                                        a.month_to,
                                        a.purpose,
                                        b.salary_head,
                                        b.id AS salary_head_id,
                                        a.hrm_location_id,
                                        c.location_name
                                    FROM
                                        hrm_salary_extra_feature a
                                            JOIN
                                        hrm_salary_head b ON a.hrm_salary_head_id = b.id  AND a.id = $id
                                            JOIN
                                        hrm_location c ON a.hrm_location_id = c.id");


          return view('salary_extra_features.edit_salary_extra_features')
              ->with('edit_data',  $edit_data)
              ->with('master_data',  $master_data) ;
    }

    public function destroy(Request $request,$id)
    {
        $check = DB::table('hrm_salary_extra_feature')->where('id','=',$id)->where('generate_type','=',2)->first();

        if (!empty($check)) {
            return back()->with('alert-danger', 'Sorry Already Generated With Salary !!');
        }

        DB::table('hrm_salary_extra_feature_details')->where('hrm_salary_extra_feature_id', '=', $id)->delete();
        DB::table('hrm_salary_extra_feature')->where('id', '=', $id)->delete();

        return redirect()->to('salaryextrafeatures')
            ->with('alert-success', 'Successfully Deleted!');
    }

    public function delete(Request $request,$id)
    {
        DB::table('hrm_salary_extra_feature_details')->where('id', '=', $id)->delete();

        return response()->json(array('massages' => true ));
    }

    public function updatedetails(Request $request,$id,$amount)
    {

        DB::UPDATE("Update hrm_salary_extra_feature_details SET amount =$amount WHERE id = $id ");

        return back()->with('alert-success', 'Successfully Updated!');
    }

    public function salary_deduction_index()
    {
        $month = DB::Select("SELECT id, month_name FROM hrm_month");

        return view('salary_deduction.salary_deduction_index', compact('month'));
    }

    public function salary_deduction_list_data()
    {
        // dd(
        //     request()->all()
        // );

        $status    = request()->status;
        $condition = '';

        if(!empty(request()->location)) {
            $condition .= ' AND d.hrm_location_id='.request()->location;
        }

        if(!empty(request()->year)) {
            $condition .= ' AND YEAR(a.month_from)='.request()->year;
        }
        if(!empty(request()->month)){
            $condition .= ' AND month(a.month_from)='.request()->month;
        }


        $salary_deductions = DB::select("
            SELECT
                a.id,
                a.purpose,
                date_format(a.month_from, '%b-%Y') as month_from,
                date_format(a.month_to, '%b-%Y') as month_to,
                a.amount,
                if(a.is_active = 2, 'Processed', 'Pending') as status,
                b.employee_name,
                concat(b.employee_name,' | ',ifnull(d.employee_code,''),' | ',f.designation_name,' | ',g.depertment_name) as employee_name,
                c.salary_head,
                b.id as hrm_employee_id,
                a.is_active,
                e.location_name
            FROM
                hrm_salary_deduction as a
            join
                hrm_employee as b on a.hrm_employee_id = b.id and a.is_active = $status
                AND a.is_active != 0
            join
                hrm_salary_head as c on a.hrm_salary_head_id = c.id
            join
                hrm_employee_job_info d ON b.id = d.hrm_employee_id AND d.employee_activity=1
                $condition
            join hrm_location e On d.hrm_location_id = e.id
            join hrm_designation f On d.hrm_designation_id = f.id
            join hrm_depertment g On d.hrm_depertment_id = g.id

        ");

        return response()->json(['data' => $salary_deductions]);
    }

    public function salary_deduction_create()
    {
        return view('salary_deduction.salary_deduction_create');
    }

    public function salary_deduction_post(Request $request, DateBetween $dateBetween)
    {
        $validator = Validator::make($request->all(), [
            'month_from' => 'required',
            'month_to' => 'required',
            'purpose' => 'required',
            'amount' => 'required',
            'employee_name' => 'required',
            'salary_head' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            DB::beginTransaction();
            try {
                $months = $dateBetween->diffMonths($request->month_from, $request->month_to);

                for ($i = 0; $i < $months; $i++) {
                    $insert = HrmSalaryDeduction::create([
                        'month_from' => date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from) ."+$i month")),
                        'month_to' => date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from) ."+$i month")),
                        'purpose' => $request->purpose,
                        'amount' => $request->amount,
                        'hrm_salary_head_id' => $request->salary_head,
                        'hrm_employee_id' => $request->employee_name,
                        'is_active' => 1
                    ]);
                }

                DB::commit();

                $this->recordActivity(
                     1,
                     'Created Advance Adjust Single',
                     $insert,
                     $insert->id,
                     'hrm_salary_deduction'
                );

                return redirect()->to('/salary_deduction_index')->with('success', 'Data has been saved successfully..!');
            } catch (\Exception $e) {
                DB::rollBack();

                return back()->withErrors(
                    $validator->errors()->add('field', $e->getMessage())
                );
            }
        }
    }

    public function salary_deduction_edit($id)
    {
        $salary_deduction = DB::select("
            SELECT
                a.id,
                a.purpose,
                a.month_from,
                a.month_to,
                a.amount,
                a.is_active,
                a.hrm_employee_id,
                a.hrm_salary_head_id,
                concat_ws(' | ', b.employee_name, d.employee_code, b.contact_number) as employee_name,
                c.salary_head
            from
                hrm_salary_deduction as a
            join
                hrm_employee as b on a.hrm_employee_id = b.id
                AND a.is_active !=0
            join
                hrm_salary_head  as c on a.hrm_salary_head_id = c.id
            join
                hrm_employee_job_info d  on b.id = d.hrm_employee_id
            join
                hrm_designation e on d.hrm_designation_id = e.id
            join
                hrm_depertment f on d.hrm_depertment_id = f.id
            where a.id = $id
        ")[0];

        if ($salary_deduction->is_active != 1) {
            return back()->with('error', 'This data has already been processed.');
        }

        return view('salary_deduction.salary_deduction_edit', compact('salary_deduction'));
    }

    public function salary_deduction_update($id, Request $request)
    {


        $validator = Validator::make($request->all(), [
            'purpose' => 'required',
            'amount' => 'required',
            'salary_head' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            DB::beginTransaction();

            try {
                $salary_deduction = HrmSalaryDeduction::query()->findOrFail($id);

                $salary_deduction->update([
                    'purpose' => $request->purpose,
                    'amount' => $request->amount,
                    'hrm_salary_head_id' => $request->salary_head,
                    // 'is_active' => $request->is_active
                ]);

                DB::UPDATE(" UPDATE hrm_salary_extra_feature_details SET   ");


                DB::commit();


                // $this->recordActivity(
                //      1,
                //      'Updated Advance Adjust Single',
                //      $salary_deduction->getChanges(),
                //      $salary_deduction->id,
                //      'hrm_salary_deduction'
                // );

                return redirect()->to('/salary_deduction_index')->with('success', 'Data has been updated successfully..!');
            } catch (\Exception $e) {
                DB::rollBack();

                return back()->withErrors(
                    $validator->errors()->add('field', $e->getMessage())
                );
            }
        }
    }

    public function salary_deduction_delete($id)
    {
        try {
            $salary_deduction = HrmSalaryDeduction::query()->findOrFail($id);

            $salary_deduction->update([
                'is_active' => 0
            ]);

            $this->recordActivity(
                 1,
                 'Deleted Advance Adjust Single',
                 $salary_deduction,
                 $id,
                 'hrm_salary_deduction'
            );

            return redirect()->to('/salary_deduction_index')->with('success', 'Data has been deleted successfully..!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
