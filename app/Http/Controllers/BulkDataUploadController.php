<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport2;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Models\HrmEmployeeSalary;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HrmEmployeeSalaryDetails;

class BulkDataUploadController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('bulkdataupload.bulk_data_upload');
    }

    public function employeebulkdataupload()
    {
        return view('bulkdataupload.employee_bulk_data_upload');
    }

    public function fingercarddataupload()
    {
        return view('bulkdataupload.fingercard_data_upload');
    }

    public function importExcel(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');

            Excel::import(new EmployeeImport, $file);

            return back()->with('success', 'Data was imported successfully..!');
        }

        return back()->with('error', 'Please Check your file, Something is wrong there.');
    }

    public function importExcel2(Request $request)
    {

        $request->validate(
            [
                'location' => 'required',
                'import_file' => 'required'
            ],
            [
                'location.required'=>'Job Location is required',
                'import_file.required' => 'Please import a file'
            ]
        );
            $file = $request->file('import_file');


            Excel::import(new EmployeeImport2($request->location), $file);

            return back();


//        return back()->with('error', 'Please Check your file, Something is wrong there.');
    }

    public function downloadExcel()
    {
        $path = public_path() . '/download_sample_excel_file/employee_sample_sheet.xls';

        return response()->download($path);
    }

    public function downloadExcel2()
    {
        $path = public_path() . '/download_sample_excel_file/employee_sample_sheet_2.xlsx';

        return response()->download($path);
    }


    public function downloadExcel_fingercard()
    {
        $path = public_path() . '/download_sample_excel_file/fingercard_sample_sheet.xls';

        return response()->download($path);
    }

    public function importExcel_fingercard(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $path = $request->file('import_file')->getRealPath();

            $data = Excel::load($path, function ($reader) {
            })->get();


            if (!empty($data) && $data->count()) {

                foreach ($data->toArray() as $key => $value) {
                    if (!empty($value)) {
                        if ($value['card_code'] == null) {
                            $card_code = '0000';
                        } else {
                            $card_code = $value['card_code'];
                        }

                        if ($value['old_code'] == null) {
                            $old_code = 'N/A';
                        } else {
                            $old_code = $value['old_code'];
                        }

                        if ($value['device_id'] == null) {
                            $device_id = 'N/A';
                        } else {
                            $device_id = $value['device_id'];
                        }

                        $employee_name = $value['employee_name'];
                        $contact_number = $value['contact_number'];


                        //check for blood Group
                        $employee_id = 0;
                        $employee_id = DB::SELECT("SELECT id FROM hrm_employee WHERE employee_name='$employee_name' AND contact_number= '$contact_number'");

                        if (!empty($employee_id[0])) {

                            $insert[] = [
                                'hrm_employee_id' => $employee_id[0]->id,
                                'card_code' => $card_code,
                                'old_code' => $old_code,
                                'device_id' => $device_id
                            ];
                        }
                    }
                }

                if (!empty($insert)) {
                    HrmEmployeeCardCode::insert($insert);

                    return redirect()->to('employeecard');
                }
            }
        }

        return back()->with('error', 'Please Check your file, Something is wrong there.');
    }

    public function employeesalaryinsert(Request $request)
    {

    // dd("sdsdsd");

        if (config('module_config.payroll_module') == 1) {

            // $job_info = DB::SELECT("SELECT
            //                             a.id,
            //                             a.basic_salary,
            //                             b.payment_mode,
            //                             b.account_no,
            //                             b.by_bank_percent,
            //                             b.hrm_bank_id,
            //                             b.accounts_code
            //                         FROM
            //                             hrm_employee_job_info a
            //                                 JOIN
            //                             hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id
            //                         WHERE
            //                             a.employee_activity = 1
            //                             GROUP BY a.id,
            //                             a.basic_salary,
            //                             b.payment_mode,
            //                             b.account_no,
            //                             b.by_bank_percent,
            //                             b.hrm_bank_id,
            //                             b.accounts_code");


            $job_info = DB::SELECT("SELECT
                                        *
                                    FROM
                                        hrm_employee_job_info
                                    WHERE
                                        employee_activity = 1 AND hrm_employee_id= 72");


            $count_row = count($job_info);

            DB::beginTransaction();
            try {
                for ($r = 0; $r < $count_row; $r++) {

                            $hrm_employee_job_info_id     = $job_info[$r]->id;
                            $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', 1)->first()->id;
                            $gross_salary                 = $job_info[$r]->basic_salary;
                            $hrm_designation_id           = $job_info[$r]->hrm_designation_id;
                            $old_hrm_employee_job_info_id = 0;


                            $getFunction = new CommonController();
                            $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);

                }
                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();

                dd($e->getMessage());
            }

            $request->session()->flash('alert-success', 'Data has been successfully Inserted!');
            return redirect()->to('/employeesalary');
        }
    }

    public function jobinfotoemployeecard()
    {
        $job_info = DB::SELECT("SELECT id,hrm_employee_id,employee_code FROM `hrm_employee_job_info` WHERE `employee_activity`=1 ANd hrm_location_id=4 ");
        $count_row = count($job_info);

        DB::beginTransaction();
        try {

            for ($r = 0; $r < $count_row; $r++) {
                $employeeid = $job_info[$r]->id;
                $employeecode = $job_info[$r]->employee_code;

                $insert = new HrmEmployeeCardCode;
                // $insert->hrm_employee_id          = $employeeid;
                $insert->hrm_employee_job_info_id = $employeeid;

                $insert->card_code = $employeecode;
                $insert->old_code = 'N/A';
                $insert->device_id = 1;
                $insert->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            dd($e->getMessage());
        }

        return redirect()->to('/employeecard');
    }

    public function deleteattendancedata_dateWise()
    {
        DB::beginTransaction();
        try {

            dd("test");

            $delete_date = '2022-06-18';
            $hrm_location_id = 4;

            DB::DELETE("DELETE FROM hrm_attendance_data where attandance_date='$delete_date' and data_from=1  and row_data_id in
            (SELECT id FROM hrm_attendance_raw_data where punch_date='$delete_date' and data_from=1 and hrm_location_id=$hrm_location_id)");

            DB::DELETE("DELETE FROM hrm_attendance where punche_date = '$delete_date' and hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info Where employee_activity=1 and hrm_location_id=$hrm_location_id)");

            DB::DELETE("DELETE FROM log where punch_date = '$delete_date'  and hrm_location_id=$hrm_location_id");

            DB::DELETE("DELETE FROM log_ot where attandance_date = '$delete_date'  and hrm_location_id=$hrm_location_id");

            DB::DELETE("DELETE FROM hrm_attendance_raw_data where punch_date='$delete_date' and data_from=1  and hrm_location_id=$hrm_location_id");


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success' => false,
                'error_messages' => true,
                'errors' => "insert problem !! " . $e->getMessage()
            ));
        }

        return response()->json(array(
            'success' => true,
            'messages' => 'successfully process'
        ));
    }

    public function deleteattendancedata_employeeWise($id)
    {
        DB::DELETE("DELETE FROM hrm_attendance_data where attandance_date='2021-06-05' and data_from=1 and hrm_employee_id=5270 and row_data_id in
        (SELECT id FROM hrm_attendance_raw_data where punch_date='2021-06-05' and data_from=1 and hrm_employee_id=5270 and hrm_location_id=6)");

        DB::DELETE("DELETE FROM hrm_attendance where punche_date = '2021-06-05' and hrm_employee_id = 5270");

        DB::DELETE("DELETE FROM log where punch_date = '2021-06-05' and hrm_employee_id = 5270 and hrm_location_id=6");

        DB::DELETE("DELETE FROM log_ot where attandance_date = '2021-06-05' and hrm_employee_id = 5270 and hrm_location_id=6");

        DB::DELETE("DELETE FROM hrm_attendance_raw_data where punch_date='2021-06-05' and data_from=1 and hrm_employee_id=5270 and hrm_location_id=6");
    }

    public function deletealljobidfromsystem()
    {
        DB::beginTransaction();
        try {
            $findDouble = DB::SELECT("SELECT
                                            hrm_employee_id
                                        FROM
                                            hrm_employee_job_info
                                        WHERE
                                            employee_activity = 1 and hrm_location_id=2
                                        GROUP BY hrm_employee_id
                                        having COUNT(id)>1");


            foreach ($findDouble as $keys) {

                $findId = DB::SELECT("SELECT max(id) as jobid FROM hrm_employee_job_info WHERE employee_activity=1 AND hrm_employee_id=$keys->hrm_employee_id ");

                $jobid = $findId[0]->jobid;

                DB::DELETE("DELETE FROM hrm_employee_shift Where hrm_employee_job_info_id= $jobid and end_date is null");
                DB::DELETE("DELETE FROM hrm_employee_activity Where hrm_employee_job_info_id= $jobid and end_date is null ");
                DB::DELETE("DELETE FROM hrm_employee_salary_details Where hrm_employee_salary_id in (SELECT id FROM hrm_employee_salary
                            WHERE hrm_employee_job_info_id=$jobid )");
                DB::DELETE("DELETE FROM hrm_employee_salary Where hrm_employee_job_info_id= $jobid");
                DB::DELETE("DELETE FROM hrm_employee_card_code Where hrm_employee_job_info_id= $jobid");
                DB::DELETE("DELETE FROM hrm_employee_job_info Where id= $jobid");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success' => false,
                'error_messages' => true,
                'errors' => "insert problem !! " . $e->getMessage()
            ));
        }

        return response()->json(array(
            'success' => true,
            'messages' => 'successfully process'
        ));
    }
}
