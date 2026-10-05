<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HRMEmployeeTask;
use Illuminate\Support\Facades\DB;
use App\Models\HRMEmployeeTaskAssign;
use Illuminate\Support\Facades\Validator;

class EmployeeTaskManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('task_management.index');
    }

    public function getTableData()
    {
        $from_date = date('Y-m-d', str_replace('/', '-', strtotime(request()->from_date)));
        $to_date = date('Y-m-d', str_replace('/', '-', strtotime(request()->to_date)));

        $employee_id = request()->employee_id;
        $priority = request()->priority;

        $where = '';
        if ($employee_id) {
            $where .= " and b.hrm_employee_id = $employee_id";
        }

        if ($priority) {
            $where .= " and b.priority = '$priority'";
        }

        if (request()->choose === 'assign_date') {
            $where .= " and date(b.assign_date) between '$from_date' and '$to_date'";
        } elseif (request()->choose === 'due_date') {
            $where .= " and date(b.due_date) between '$from_date' and '$to_date'";
        }

        $tasks = DB::select("
            select
                a.id,
                b.id as assign_id,

                date_format(b.assign_date, '%m/%d/%Y %r') as assign_date_modal,
                date_format(b.due_date, '%m/%d/%Y %r') as due_date_modal,

                date_format(b.assign_date, '%d %b, %Y | %h:%i %p') as assign_date,
                date_format(b.due_date, '%d %b, %Y | %h:%i %p') as due_date,
                b.hrm_employee_id,
                b.priority,

                concat(floor(hour(timediff(b.assign_date, b.due_date)) / 24), ' d ', mod(hour(timediff(b.assign_date, b.due_date)), 24), ' h ',
                    minute(timediff(b.assign_date, b.due_date)), ' m ') as duration,

                a.task_name,
                if(a.status = 1, 'Pending', 'Pushed') as status,
                c.employee_name,
                d.task_type_name,
                a.hrm_employee_task_type_id
            from
                hrm_employee_task as a
            left join
                hrm_employee_task_assign as b on a.id = b.hrm_employee_task_id
            left join
                hrm_employee as c on b.hrm_employee_id = c.id
            join
                hrm_employee_task_type as d on a.hrm_employee_task_type_id = d.id
            $where
        ");

        return datatables()->of($tasks)
            ->addColumn('Priority', function($task) {
                $priority = '';

                if ($task->priority === 'Low') {
                    $priority = '<span class="badge">'.$task->priority.'</span>';
                } elseif ($task->priority === 'Medium') {
                    $priority = '<span class="badge" style="background: orange">'.$task->priority.'</span>';
                } else {
                    $priority = '<span class="badge" style="background: red">'.$task->priority.'</span>';
                }

                return $priority;
            })
            ->addColumn('Link', function($task) {
                return '
                <a href="'.route('employee_task_management.show', encrypt($task->id)).'" class="btn btn-success btn-sm btn-block" target="_blank">
                    Assign
                </a>
                <a href="#" class="btn btn-primary btn-sm btn-block showme" data-task_name="'.$task->task_name.'" data-task_id="'.$task->id.'" data-assign_id="'.$task->assign_id.'" data-assign_date="'.$task->assign_date_modal.'" data-due_date="'.$task->due_date_modal.'" data-priority="'.$task->priority.'" data-hrm_employee_task_type_id="'.$task->hrm_employee_task_type_id.'" data-task_type_name="'.$task->task_type_name.'" data-hrm_employee_id="'.$task->hrm_employee_id.'" data-employee_name="'.$task->employee_name.'">
                    Edit
                </a>
                <a href="'.url('employee_task_management/'.encrypt($task->id).'/delete').'" onclick="return confirm(\'Are you sure to delete this.!\')" class="btn btn-danger btn-sm btn-block">
                    Delete
                </a>';
            })
            ->rawColumns(['Link', 'Priority'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string|min:5',
            'hrm_employee_task_type_id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with($validator->errors());
        }

        DB::beginTransaction();
        try {
            $task = HRMEmployeeTask::query()->find($request->task_id);

            $assign = HRMEmployeeTaskAssign::query()->find($request->assign_id);

            if (empty($task)) {
                $task = new HRMEmployeeTask;
            }

            if (empty($assign)) {
                $assign = new HRMEmployeeTaskAssign;
            }

            $task->task_name = $request->task_name;
            $task->hrm_employee_task_type_id = $request->hrm_employee_task_type_id;
            $task->status = $request->hrm_employee_id ? 2 : 1;
            $task->save();

            if ($request->hrm_employee_id) {
                $assign->assign_date = date('Y-m-d H:i:s', strtotime($request->assign_date));
                $assign->due_date = date('Y-m-d H:i:s', strtotime($request->due_date));
                $assign->hrm_employee_id = $request->hrm_employee_id;
                $assign->priority = $request->priority;
                $assign->hrm_employee_task_id = $task->id;
                $assign->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }

        return back()->with('message', 'Data has been saved successfully..!');
    }

    public function show($id)
    {
        $task = HRMEmployeeTask::query()->findOrFail(decrypt($id));

        return view('task_management.show', compact('task'));
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assign_date' => 'required',
            'due_date' => 'required',
            'hrm_employee_id' => 'required',
            'priority' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $task = HRMEmployeeTask::query()->findOrFail(decrypt($id));

            $task->update(['status' => 2]);

            $isExists = HRMEmployeeTaskAssign::query()
                ->where('hrm_employee_task_id', decrypt($id))
                ->where('hrm_employee_id', $request->hrm_employee_id)
                ->first();

            if ($isExists) {
                return back()->with('message', 'This task is already assigned on this employee.!');
            } else {
                HRMEmployeeTaskAssign::create([
                    'hrm_employee_task_id' => $task->id,
                    'hrm_employee_id' => $request->hrm_employee_id,
                    'assign_date' => date('Y-m-d H:i:s', str_replace('/', '-', strtotime($request->assign_date) )),
                    'due_date' => date('Y-m-d H:i:s', str_replace('/', '-', strtotime($request->due_date) )),
                    'priority' => $request->priority,
                ]);
            }

            DB::commit();

            return redirect()->route('employee_task_management.index')
                ->with('message', 'Task Assign successfully..!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('message', $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::table('hrm_employee_task_assign')->where('hrm_employee_task_id', decrypt($id))->delete();

        $task = HRMEmployeeTask::query()->findOrFail(decrypt($id));

        $task->delete();

        return back()->with('message', 'Data has been deleted successfully..!');
    }
}
