<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HRMEmployeeTaskType;
use Illuminate\Support\Facades\Validator;

class EmployeeTaskTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('task_type.index');
    }

    public function getLoadData()
    {
        $task_types = DB::select("
            select
                a.id, a.task_type_name
            from
                hrm_employee_task_type as a
        ");

        return datatables()->of($task_types)
            ->addColumn('Link', function($task) {
                return '
                <a href="#" class="btn btn-primary btn-sm showme" data-task_type_name="'.$task->task_type_name.'" data-task_type_id="'.$task->id.'">
                    Edit
                </a>
                <a href="'.url('employee_task_type/'.encrypt($task->id).'/delete').'" onclick="return confirm(\'Are you sure to delete this.!\')" class="btn btn-danger btn-sm">
                    Delete
                </a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_type_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with($validator->errors());
        }

        $task = HRMEmployeeTaskType::query()->find($request->task_type_id);

        if ($task == null) {
            $task = new HRMEmployeeTaskType;
        }

        $task->task_type_name = $request->task_type_name;
        $task->save();

        return back()->with('message', 'Data has been saved successfully..!');
    }

    public function delete($id)
    {
        $task_type = HRMEmployeeTaskType::query()->findOrFail(decrypt($id));

        $task_type->delete();

        return back()->with('message', 'Data has been deleted successfully..!');
    }

    public function task_type_list(Request $request)
    {
        $task_types = HRMEmployeeTaskType::query()
            ->select('id', 'task_type_name as text')
            ->where('task_type_name', 'like', "%$request->term%")
            ->get();

        return response()->json($task_types);
    }
}
