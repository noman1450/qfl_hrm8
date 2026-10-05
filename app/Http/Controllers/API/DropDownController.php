<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DropDownController extends Controller
{
    public function common_list_data(Request $request)
    {
        if(! method_exists($this, $request->call_function)) {
            return response()->json([
                sprintf('Opps! method %s does not exists in %s', $request->call_function, __CLASS__)
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->{$request->call_function}($request);
    }


    public function common_store(Request $request)
    {
        if(! method_exists($this, $request->call_function)) {
            return response()->json([
                sprintf('Opps! method %s does not exists in %s', $request->call_function, __CLASS__)
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->{$request->call_function}($request);
    }

    public function appCategoryStore(Request $request)
    {
        // $data = [];
        // if (!empty($request->term)){
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        // } else {
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        // }

        // return response()->json($data);
    }


    public function appDepartmentStore(Request $request)
    {
        // $data = [];
        // if (!empty($request->term)){
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        // } else {
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        // }

        // return response()->json($data);
    }


    public function appDesignationStore(Request $request)
    {
        // $data = [];
        // if (!empty($request->term)){
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        // } else {
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        // }

        // return response()->json($data);
    }


    public function appSectionStore(Request $request)
    {
        // $data = [];
        // if (!empty($request->term)){
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        // } else {
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        // }

        // return response()->json($data);
    }


    public function appShiftStore(Request $request)
    {
        // $data = [];
        // if (!empty($request->term)){
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        // } else {
        //     $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        // }

        // return response()->json($data);
    }




    public function religion_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,religion as text FROM hrm_religion WHERE religion LIKE '%$request->term%'");
        } else {
            $data = DB::select("SELECT id,religion as text FROM hrm_religion");
        }

        return response()->json($data);
    }

    public function maritalstatus_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,marital_status as text FROM hrm_marital_status WHERE marital_status LIKE '%$request->term%'");
        } else {
            $data = DB::select("SELECT id,marital_status as text FROM hrm_marital_status");
        }

        return response()->json($data);
    }

    public function blood_group_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,blood_group as text FROM hrm_blood_group
                                WHERE blood_group LIKE '%$request->term%';");

        }else{
            $data = DB::select("SELECT id,blood_group as text FROM hrm_blood_group;");
        }

        return response()->json($data);
    }

    public function education_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)) {
            $data = DB::select("SELECT id,education_name as text FROM hrm_education WHERE education_name LIKE '%$request->term%'");
        } else {
            $data = DB::select("SELECT id,education_name as text FROM hrm_education");
        }

        return response()->json($data);
    }

    public function designation_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,designation_name as text FROM hrm_designation WHERE valid = 1 AND designation_name LIKE '%$request->term%';");

        } else {
            $data = DB::select("SELECT id,designation_name as text FROM hrm_designation ;");
        }

        return response()->json($data);
    }

    public function depertment_list_data(Request $request)
    {
        $depertment = [];
        if (!empty($request->term)){
            $depertment = DB::select("SELECT id,depertment_name as text FROM hrm_depertment
                                    WHERE valid = 1 AND depertment_name LIKE '%$request->term%';");

        }else{
            $depertment = DB::select("SELECT id,depertment_name as text FROM hrm_depertment
                                    WHERE valid = 1;");
        }

        return response()->json($depertment);
    }

    public function location_list_data(Request $request)
    {
        $data = [];
        $user_id    = Auth::user()->id;

        if (!empty($request->term)){
            $data = DB::select("SELECT a.id,a.location_name as text,c.short_description,c.id as hrm_device_information_id FROM hrm_location a
                            JOIN user_location b ON a.id = b.hrm_location_id AND b.users_id = $user_id
                            LEFT JOIN hrm_device_information c ON a.hrm_device_information_id=c.id
                            WHERE a.valid = 1 AND a.location_name LIKE '%$request->term%';");

        }else{
            $data = DB::select("SELECT a.id,a.location_name as text,c.short_description,c.id as hrm_device_information_id   FROM hrm_location a
                            JOIN user_location b ON a.id = b.hrm_location_id AND b.users_id = $user_id
                            LEFT JOIN hrm_device_information c ON a.hrm_device_information_id=c.id
                            WHERE a.valid = 1 ;");
        }

        return response()->json($data);
    }

    public function category_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,category_name as text FROM hrm_category
                                WHERE valid = 1 AND category_name LIKE '%$request->term%';");

        }else{
        $data = DB::select("SELECT id,category_name as text FROM hrm_category
                                WHERE valid = 1 ;");
        }
            return response()->json($data);
    }

    public function section_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,section_name as text FROM hrm_section WHERE section_name LIKE '%$request->term%'");
        } else {
            $data = DB::select("SELECT id,section_name as text FROM hrm_section");
        }

        return response()->json($data);
    }

    public function shift_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,shift_name as text FROM hrm_shift
                                WHERE valid = 1 AND shift_name LIKE '%$request->term%';");

        }else{
        $data = DB::select("SELECT id,shift_name as text FROM hrm_shift
                                WHERE valid = 1 ;");
        }
        return response()->json($data);
    }

    public function plantname_list_data(Request $request)
    {
        $user_id = Auth::user()->id;

        $plant_list = DB::SELECT("SELECT a.id,
                                a.plant_name AS text
                                FROM hrm_plant a WHERE a.hrm_location_id is null
                                UNION
                                SELECT a.id,
                                CONCAT(a.plant_name,' | ',b.location_name) AS text
                                FROM hrm_plant a
                                JOIN hrm_location b ON a.hrm_location_id=b.id AND a.valid=1
                                JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id WHERE a.plant_name LIKE '%$request->term%' ;");

        return response()->json($plant_list);
    }

    public function employee_list_data(Request $request)
    {

        $employeeId = Auth::user()->hrm_employee_id;
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,concat(employee_name, ' | ' ,IFNull(contact_number,'N/A'))as text FROM hrm_employee
                                WHERE active_status=1 AND id=$employeeId and  employee_name LIKE '%$request->term%' ;");

        }else{
        $data = DB::select("SELECT id,concat(employee_name, ' | ' ,IFNull(contact_number,'N/A')) as text FROM hrm_employee
                                WHERE active_status=1 AND id=$employeeId ;");
        }
        return response()->json($data);
    }

    public function salarygrade_list(Request $request)
    {
        $data = [];
        if (!empty($request->term)) {
            $data = DB::select("SELECT id,grade_name text FROM hrm_salary_grade WHERE valid = 1 and grade_name LIKE '%$request->term%'");

        } else {
            $data = DB::select("SELECT id,grade_name text FROM hrm_salary_grade WHERE valid = 1");
        }

        return response()->json($data);
    }

    public function employeestatus_list_data(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,employment_status as text FROM hrm_employment_status WHERE employment_status LIKE '%$request->term%'");
        }else{
            $data = DB::select("SELECT id,employment_status as text FROM hrm_employment_status");
        }

        return response()->json($data);
    }


    public function employee_task_type(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,task_type_name as text FROM hrm_employee_task_type WHERE task_type_name LIKE '%$request->term%'");
        }else{
            $data = DB::select("SELECT id,task_type_name as text FROM hrm_employee_task_type");
        }

        return response()->json($data);
    }

    public function file_type_list(Request $request)
    {
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,file_type_name text FROM hrm_file_type
                                WHERE    file_type_name LIKE '%$request->term%' ;");

        } else {
        $data = DB::select("SELECT id,file_type_name text FROM hrm_file_type;");
        }

        return response()->json($data);
    }



}
