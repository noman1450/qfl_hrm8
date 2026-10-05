<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountsIntegration\HrmAccExchangeEmp;

class AccountHeadEmployeeExchangeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $where = '';

            if ($hrm_acc_head_title_id = request('hrm_acc_head_title_id')) {
                $where .= " and i.id = $hrm_acc_head_title_id";
            }

            if ($hrm_location_id = request('hrm_location_id')) {
                $where .= " and b.hrm_location_id = $hrm_location_id";
            }

            if ($hrm_category_id = request('hrm_category_id')) {
                $where .= " and b.hrm_category_id = $hrm_category_id";
            }

            $data = DB::select("SELECT
                                        a.id,
                                        f.id AS employee_id,
                                        f.employee_name,
                                        d.depertment_name,
                                        e.designation_name,
                                        g.location_name,
                                        c.category_name,
                                        i.head_name
                                    FROM
                                        hrm_acc_headwise_emp_tag_details a
                                            JOIN
                                        hrm_employee_job_info b ON a.hrm_location_id = b.hrm_location_id
                                            AND b.employee_activity = 1
                                            AND b.hrm_category_id IN (SELECT
                                                hrm_category_id
                                            FROM
                                                hrm_acc_headwise_emp_tag_details
                                            WHERE
                                                hrm_acc_headwise_emp_tag_id = a.hrm_acc_headwise_emp_tag_id)
                                            AND b.hrm_employee_id not in (  SELECT hrm_employee_id  FROM  hrm_acc_exchange_emp)
                                            JOIN
                                        hrm_category c ON b.hrm_category_id = c.id
                                            JOIN
                                        hrm_depertment AS d ON b.hrm_depertment_id = d.id
                                            JOIN
                                        hrm_designation AS e ON b.hrm_designation_id = e.id
                                            JOIN
                                        hrm_employee AS f ON b.hrm_employee_id = f.id
                                            JOIN
                                        hrm_location g ON b.hrm_location_id=g.id
                                            join
                                        hrm_acc_headwise_emp_tag h ON a.hrm_acc_headwise_emp_tag_id = h.id
                                            join
                                        hrm_acc_head_title i ON h.hrm_acc_head_title_id = i.id
                                              $where
            ");

            return datatables()->of($data)
                ->make(true);
        }

        return view('AccountsIntegration.exchange_employee.index');
    }

    public function exchangedList()
    {
        if (request()->ajax()) {
            $where = '';

            if ($hrm_acc_head_title_id = request('hrm_acc_head_title_id')) {
                $where .= " and a.hrm_acc_head_title_id = $hrm_acc_head_title_id";
            }

            if ($hrm_location_id = request('hrm_location_id')) {
                $where .= " and c.hrm_location_id = $hrm_location_id";
            }

            if ($hrm_category_id = request('hrm_category_id')) {
                $where .= " and c.hrm_category_id = $hrm_category_id";
            }

            $data = DB::select("
                SELECT
                    a.id,
                    b.employee_name,
                    a.hrm_employee_id as employee_id,
                    d.depertment_name,
                    e.designation_name,
                    f.location_name,
                    g.category_name,
                    h.head_name
                from
                    hrm_acc_exchange_emp as a
                        join
                    hrm_employee as b on a.hrm_employee_id = b.id
                        join
                    hrm_employee_job_info as c on c.hrm_employee_id = b.id
                        join
                    hrm_depertment as d on c.hrm_depertment_id = d.id
                        join
                    hrm_designation as e on c.hrm_designation_id = e.id
                        join
                    hrm_location as f on c.hrm_location_id = f.id
                        join
                    hrm_category as g on c.hrm_category_id = g.id
                        JOIN
                    hrm_acc_head_title h ON a.hrm_acc_head_title_id = h.id
                    $where
            ");

            return datatables()->of($data)
                ->make(true);
        }

        return view('AccountsIntegration.exchange_employee.index');
    }

    public function employeeChangeHead(Request $request)
    {
        $status = false;

        DB::beginTransaction();
        try {

            $isExist = DB::table('hrm_acc_exchange_emp')
                ->where('hrm_employee_id', $request->employee_id)
                ->first();

            if ($isExist) {
                DB::UPDATE("UPDATE hrm_acc_exchange_emp SET hrm_acc_head_title_id=$request->hrm_acc_head_title_id WHERE hrm_employee_id=$request->employee_id");
            } else {
                HrmAccExchangeEmp::create([
                    'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id,
                    'hrm_employee_id' => $request->employee_id,
                    'users_id' => auth()->id(),
                ]);
            }

            DB::commit();

            $status = true;
            $message = 'Data has been updated..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ]);
    }

    public function exchangedEmpDelete($id)
    {
        $status = false;

        DB::beginTransaction();
        try {
            HrmAccExchangeEmp::query()->findOrFail($id)
                ->delete();

            DB::commit();

            $status = true;
            $message = 'Data has been deleted..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ]);
    }
}
