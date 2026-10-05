<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeTaskManagementListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function completeTask()
    {
        return view('task_management.completed_task_list');
    }

    public function completeTaskTableData()
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

        $completeTasks = DB::select("
            select
                a.id, a.task_name,
                c.employee_name,
                date_format(b.assign_date, '%d %b, %Y') as assign_date,
                date_format(b.due_date, '%d %b, %Y') as due_date,
                b.priority,

                concat(floor(hour(timediff(b.assign_date, b.due_date)) / 24), ' d ', mod(hour(timediff(b.assign_date, b.due_date)), 24), ' h ',
                    minute(timediff(b.assign_date, b.due_date)), ' m ') as duration,

                date_format(b.final_complete_date, '%d %b, %Y') as completed_date
            from
                hrm_employee_task as a
            left join
                hrm_employee_task_assign as b on b.hrm_employee_task_id = a.id
            left join
                hrm_employee as c on b.hrm_employee_id = c.id
            where
                b.final_complete_date is not null
            and date(b.final_complete_date) between '$from_date' and '$to_date'
            $where
        ");

        return datatables()->of($completeTasks)
            ->addColumn('Priority', function($completeTask) {
                $priority = '';

                if ($completeTask->priority === 'Low') {
                    $priority = '<span class="badge">'.$completeTask->priority.'</span>';
                } elseif ($completeTask->priority === 'Medium') {
                    $priority = '<span class="badge" style="background: orange">'.$completeTask->priority.'</span>';
                } else {
                    $priority = '<span class="badge" style="background: red">'.$completeTask->priority.'</span>';
                }

                return $priority;
            })
            ->rawColumns(['Priority'])
            ->make(true);
    }

    public function pendingTask()
    {
        return view('task_management.pending_task_list');
    }

    public function pendingTaskTableData()
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

        $pendingTasks = DB::select("
            select
                a.id, a.task_name,
                c.employee_name,
                date_format(b.assign_date, '%d %b, %Y') as assign_date,
                date_format(b.due_date, '%d %b, %Y') as due_date,

                concat(floor(hour(timediff(b.assign_date, b.due_date)) / 24), ' d ', mod(hour(timediff(b.assign_date, b.due_date)), 24), ' h ',
                    minute(timediff(b.assign_date, b.due_date)), ' m ') as duration,

                b.priority
            from
                hrm_employee_task as a
            left join
                hrm_employee_task_assign as b on b.hrm_employee_task_id = a.id
            left join
                hrm_employee as c on b.hrm_employee_id = c.id
            where
                b.final_complete_date is null
            $where
        ");

        return datatables()->of($pendingTasks)
            ->addColumn('Priority', function($pendingTask) {
                $priority = '';

                if ($pendingTask->priority === 'Low') {
                    $priority = '<span class="badge">'.$pendingTask->priority.'</span>';
                } elseif ($pendingTask->priority === 'Medium') {
                    $priority = '<span class="badge" style="background: orange">'.$pendingTask->priority.'</span>';
                } else {
                    $priority = '<span class="badge" style="background: red">'.$pendingTask->priority.'</span>';
                }

                return $priority;
            })
            ->rawColumns(['Priority'])
            ->make(true);
    }

    public function dailyCompleteTask()
    {
        return view('task_management.daily_complete_task_list');
    }

    public function dailyCompleteTaskTableData()
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

        $dailyCompletedTasks = DB::select("
            select
                c.task_name,
                date_format(b.assign_date, '%d %b, %Y') as assign_date,
                b.priority,
                d.employee_name,
                a.comments,
                date_format(a.complete_date, '%d %b, %Y') as complete_date,
                if(b.final_complete_date is null, 'Partial', 'Complete') as status
            from
                hrm_employee_task_complete as a
            join
                hrm_employee_task_assign as b on a.hrm_employee_task_assign_id = b.id
            join
                hrm_employee_task as c on b.hrm_employee_task_id = c.id
            join
                hrm_employee as d on b.hrm_employee_id = d.id
            and date(a.complete_date) between '$from_date' and '$to_date'
            $where

            group by a.id
        ");

        return datatables()->of($dailyCompletedTasks)
            ->addColumn('Priority', function($dailyCompletedTask) {
                $priority = '';

                if ($dailyCompletedTask->priority === 'Low') {
                    $priority = '<span class="badge">'.$dailyCompletedTask->priority.'</span>';
                } elseif ($dailyCompletedTask->priority === 'Medium') {
                    $priority = '<span class="badge" style="background: orange">'.$dailyCompletedTask->priority.'</span>';
                } else {
                    $priority = '<span class="badge" style="background: red">'.$dailyCompletedTask->priority.'</span>';
                }

                return $priority;
            })
            ->rawColumns(['Priority'])
            ->make(true);
    }

    public function dailyCompleteTaskPrint(Request $request)
    {
        // return $request->all();

        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));

        $data['from_date'] = date('jS M, Y', strtotime($request->from_date));
        $data['to_date'] = date('jS M, Y', strtotime($request->to_date));

        $employee_id = request()->employee_id;

        $data['employee'] = DB::select("
            select
                a.id,
                a.employee_name,
                d.depertment_name,
                e.designation_name
            from  hrm_employee a
            join hrm_employee_job_info b on a.id = b.hrm_employee_id
                and a.active_status = 1 and b.employee_activity = 1

            join hrm_depertment d On b.hrm_depertment_id = d.id
            join hrm_designation e On b.hrm_designation_id = e.id

            where a.id = $employee_id
        ")[0];

        $data['dailyCompletedTasks'] = DB::select("
            select
                c.task_name,
                date_format(b.assign_date, '%d %b, %Y') as assign_date,
                b.priority,
                date_format(a.complete_date, '%d %b, %Y') as complete_date,
                if(b.final_complete_date is null, 'Partial', 'Complete') as status
            from
                hrm_employee_task_complete as a
            join
                hrm_employee_task_assign as b on a.hrm_employee_task_assign_id = b.id
            join
                hrm_employee_task as c on b.hrm_employee_task_id = c.id

            and date(a.complete_date) between '$from_date' and '$to_date'
            and b.hrm_employee_id = $employee_id

            group by a.id
        ");

        return view('task_management.daily_complete_task_print', $data);
    }
}
