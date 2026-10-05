<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HRMEmployeeTask;
use App\Models\HRMEmployeeTaskAssign;
use App\Models\HRMEmployeeTaskComplete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeTaskController extends Controller
{

    public function index()
    {
        $success = false;

        try {
            // $hrm_employee_id = auth()->user()->hrm_employee_id;
            $hrm_employee_id = $id;

            $pendingTasks = DB::select("
                select
                    b.id, a.task_name,
                    c.employee_name,
                    date_format(b.assign_date, '%d %b') as assign_date,
                    b.priority,
                    date_format(b.due_date, '%d %b %h:%i %p') as due_date,
                    e.task_type_name
                from
                    hrm_employee_task as a
                join
                    hrm_employee_task_assign as b on b.hrm_employee_task_id = a.id
                join
                    hrm_employee as c on b.hrm_employee_id = c.id
                JOIN
                    hrm_employee_task_type e ON a.hrm_employee_task_type_id = e.id
                where b.final_complete_date is null
                -- AND b.assign_date <= adddate(now(), interval 2 day)
                and b.hrm_employee_id = $hrm_employee_id
            ");

            $success = true;
            $message = 'Data get successfully..!';
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $pendingTasks ?? [],
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }


    public function pendingEmployeeTask($id)
    {
        $success = false;

        try {
            // $hrm_employee_id = auth()->user()->hrm_employee_id;
            $hrm_employee_id = $id;

            $pendingTasks = DB::select("
                select
                    b.id, a.task_name,
                    c.employee_name,
                    date_format(b.assign_date, '%d %b') as assign_date,
                    b.priority,
                    date_format(b.due_date, '%d %b %h:%i %p') as due_date,
                    e.task_type_name
                from
                    hrm_employee_task as a
                join
                    hrm_employee_task_assign as b on b.hrm_employee_task_id = a.id
                join
                    hrm_employee as c on b.hrm_employee_id = c.id
                JOIN
                    hrm_employee_task_type e ON a.hrm_employee_task_type_id = e.id
                where b.final_complete_date is null
                -- AND b.assign_date <= adddate(now(), interval 2 day)
                and b.hrm_employee_id = $hrm_employee_id
            ");

            $success = true;
            $message = 'Data get successfully..!';
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $pendingTasks ?? [],
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }


    public function store(Request $request)
    {
        $success = false;

        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string|min:5',
            'priority' => 'required|string|max:6'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Form validation failed..!';
        } else {
            DB::beginTransaction();
            try {
                $taskId = HRMEmployeeTask::create([
                    'task_name' => $request->task_name,
                    'status' => 2,
                    'hrm_employee_task_type_id' => $request->hrm_employee_task_type_id,
                ])->id;


                $due_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->due_date)));

                HRMEmployeeTaskAssign::create([
                    'hrm_employee_task_id' => $taskId,
                    'hrm_employee_id' => $request->hrm_employee_id,
                    'assign_date' => now()->toDateTimeString(),
                    'priority' => $request->priority,
                    'due_date' => $due_date,
                ]);

                DB::commit();

                $success = true;
                $message = 'Data has been saved successfully..!';
                $error_code = 200;
            } catch (\Exception $e) {
                DB::rollBack();

                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return [
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ];
    }

    public function complete_task_store(Request $request)
    {
        $success = false;

        $validator = Validator::make($request->all(), [
            'hrm_employee_task_assign_id' => 'required|integer|exists:hrm_employee_task_assign,id',
            'complete_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Form validation failed..!';
        } else {
            DB::beginTransaction();
            try {
                $isExists = HRMEmployeeTaskComplete::query()
                    ->where('hrm_employee_task_assign_id', $request->hrm_employee_task_assign_id)
                    ->where(DB::raw("date(complete_date)"), date('Y-m-d'))
                    ->get();

                if ($isExists->isNotEmpty()) {
                    $isExists->each->delete();
                }

                if ($request->complete_status) {
                    HRMEmployeeTaskAssign::query()
                        ->where('id', $request->hrm_employee_task_assign_id)
                        ->first()
                        ->update([
                            'final_complete_date' => now()->toDateTimeString()
                        ]);
                }

                HRMEmployeeTaskComplete::create([
                    'hrm_employee_task_assign_id' => $request->hrm_employee_task_assign_id,
                    'comments' => $request->comments,
                    'complete_date' => now()->toDateTimeString(),
                ]);

                DB::commit();

                $success = true;
                $message = 'Data has been saved successfully..!';
                $error_code = 200;
            } catch (\Exception $e) {
                DB::rollBack();

                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function complete_employee_tasks(Request $request)
    {
        $success = false;

        try {
            $complete_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->complete_date)));

            // $hrm_employee_id = auth()->user()->hrm_employee_id;
            $hrm_employee_id = $request->hrm_employee_id;

            $completeTasks = DB::select("
                select
                    a.id, a.task_name,
                    c.employee_name,
                    date_format(b.assign_date, '%d %b, %Y') as assign_date,
                    b.priority,
                    b.final_complete_date,
                    b.due_date,
                    e.task_type_name
                from
                    hrm_employee_task as a
                join
                    hrm_employee_task_assign as b on b.hrm_employee_task_id = a.id
                join
                    hrm_employee as c on b.hrm_employee_id = c.id
                join
                    hrm_employee_task_complete as d on d.hrm_employee_task_assign_id = b.id
                JOIN
                    hrm_employee_task_type e ON a.hrm_employee_task_type_id = e.id
                where
                    date(d.complete_date) = '$complete_date'
                and
                    b.hrm_employee_id = $hrm_employee_id
            ");

            $success = true;
            $message = 'Data get successfully..!';
            $error_code = 200;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $message = 'Something went wrong..!';
            $error_code = 500;
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $completeTasks ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }
}
