<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HrmDepertment;
use App\Models\HrmKPIMarks;
use App\Models\HrmKPISetConfig;
use App\Models\HrmKPISetConfigDepartment;
use App\Models\HrmKPISetConfigDeptWith;
use App\Models\HrmKPITaskDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KPIConfigController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $where = '';

            if ($hrm_kpi_assesment_date_id = request()->hrm_kpi_assesment_date_id) {
                $where = " AND b.id = {$hrm_kpi_assesment_date_id}";
            }

            $data = DB::select("SELECT
                    a.*,
                    CONCAT(c.description, ' | ', 'From: ', b.start_date, '  To: ', b.end_date) AS financial_year,
                    d.display_name
                FROM
                    hrm_kpi_set_config a
                        JOIN
                    hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                        AND isActive = 1
                        JOIN
                    hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id
                    $where
                        LEFT JOIN
                    hrm_kpi_dynamic_table d ON a.tag_with_department = d.id
            ");

            return datatables()->of($data)
                ->addColumn('Link', function ($data) {
                    if (! $data->is_confirmed) {
                        return '
                        <a href="'.route('kpi_config.edit', encrypt($data->id)).'" data-title="Edit Config" footer-none class="modalLink btn btn-sm btn-info">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="'.route('kpi_config.show', encrypt($data->id)).'" class="btn btn-sm btn-warning" title="Show Details">
                            Details Config
                            <i class="fa fa-arrow-right"></i>
                        </a>';
                    }

                    return '
                    <a href="' . route('kpi_config.show', encrypt($data->id)) . '" class="btn btn-sm btn-warning" title="Show Details">
                        Details Config
                        <i class="fa fa-arrow-right"></i>
                    </a>';
                })
                ->rawColumns(['Link'])
                ->make(true);
        }

        return view('kpi_config.index');
    }

    public function create()
    {
        $kpiConfig = null;

        $dynamicTables = DB::table('hrm_kpi_dynamic_table')
            ->select('id', 'display_name', 'table_name')
            ->get();

        // dd($dynamicTables);

        return view('kpi_config._form', compact('kpiConfig', 'dynamicTables'));
    }

    public function store(Request $request)
    {

        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_kpi_assesment_date_id' => 'required',
            'title' => 'required',
        ], ['*.required' => 'This field is required.']);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation.';
        } else {
            try {
              $insert =  HrmKPISetConfig::create([
                    'title' => $request->title,
                    'hrm_kpi_assesment_date_id' => $request->hrm_kpi_assesment_date_id,
                    'tag_with_department' => $request->tag_with_department,
                    'users_id' => auth()->id(),
                    'isActive' => 1
                ]);

                $this->recordActivity(
                    1,
                   'Created KPI Set Config',
                    $insert,
                    $insert->id,
                    'hrm_kpi_set_config'
                );

                $status = true;
                $message = 'Config has been created.';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function show($id)
    {
        $departments = HrmDepertment::query()->where('valid', 1)->get();

        $hrm_kpi_set_config = DB::select("SELECT
                a.*,
                CONCAT(c.description, ' | ', 'From: ', b.start_date, '  To: ', b.end_date) AS financial_year,
                d.display_name
            FROM
                hrm_kpi_set_config a
                    JOIN
                hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                    JOIN
                hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id
                    LEFT JOIN
                hrm_kpi_dynamic_table d ON a.tag_with_department = d.id
            WHERE a.id = ?
        ", [decrypt($id)])[0];

        $tasks = DB::select("SELECT
                a.id, a.description,b.id as hrm_kpi_task_type_id,b.task_type_name
            FROM
                hrm_kpi_task a
                    JOIN
                hrm_kpi_task_type b ON a.hrm_kpi_task_type_id = b.id
            WHERE a.valid = 1
        ");

        $selectedTasks = DB::table('hrm_kpi_task_details')
            ->where('hrm_kpi_set_config_id', decrypt($id))
            ->where('valid', 1)
            ->pluck('id', 'hrm_kpi_task_id')
            ->toArray();

        $selectedDepartments = DB::table('hrm_kpi_set_config_department')
            ->where('hrm_kpi_set_config_id', decrypt($id))
            ->pluck('id', 'hrm_depertment_id')
            ->toArray();

        $configId = decrypt($id);

        $finalTasks = DB::select("SELECT
                b.description, c.task_type_name
            FROM
                hrm_kpi_task_details a
                    JOIN
                hrm_kpi_task b ON a.hrm_kpi_task_id = b.id
                    JOIN
                hrm_kpi_task_type c ON b.hrm_kpi_task_type_id = c.id
            WHERE
                a.hrm_kpi_set_config_id = $configId
        ");

        $finalMarks = HrmKPIMarks::query()
            ->where('hrm_kpi_set_config_id', $configId)
            ->where('valid', 1)
            ->get();



        return view('kpi_config.show', compact('departments', 'tasks', 'hrm_kpi_set_config', 'selectedTasks', 'selectedDepartments', 'finalTasks', 'finalMarks'));
    }

    public function edit($id)
    {
        $kpiConfig = DB::select("SELECT
                a.*,
                CONCAT(c.description, ' | ', 'From: ', b.start_date, '  To: ', b.end_date) AS financial_year,
                d.display_name
            FROM
                hrm_kpi_set_config a
                    JOIN
                hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                    JOIN
                hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id
                    LEFT JOIN
                hrm_kpi_dynamic_table d ON a.tag_with_department = d.id
            WHERE a.id = ?
        ", [decrypt($id)])[0];

        $dynamicTables = DB::table('hrm_kpi_dynamic_table')
            ->select('id', 'display_name', 'table_name')
            ->get();

        return view('kpi_config._form', compact('kpiConfig', 'dynamicTables'));
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_kpi_assesment_date_id' => 'required',
            'title' => 'required',
        ], ['*.required' => 'This field is required.']);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation.';
        } else {
            try {
                $kpiConfig = HrmKPISetConfig::query()->findOrFail(decrypt($id));

                $kpiConfig->update([
                    'title' => $request->title,
                    'hrm_kpi_assesment_date_id' => $request->hrm_kpi_assesment_date_id,
                    'tag_with_department' => $request->tag_with_department,
                    'users_id' => auth()->id(),
                ]);

                $this->recordActivity(
                     1,
                     'Updated KPI Set Config',
                     $kpiConfig->getChanges(),
                     $kpiConfig->id,
                     'hrm_kpi_set_config'
                );

                $status = true;
                $message = 'Config has been updated.';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function dynamicTableList()
    {
        $term = request('term');

        $data = DB::table('hrm_kpi_dynamic_table')
            ->select('id', 'display_name as text', 'table_name')
            ->where('display_name', 'like', "%{$term}%")
            ->get();

        return response()->json($data);
    }

    public function kpiTaskStore()
    {
        if (request('all')) {
            foreach (request('hrm_kpi_task_id') as $key => $value) {
               $insert = HrmKPITaskDetails::query()->updateOrCreate([
                    'hrm_kpi_task_id' => $value,
                    'hrm_kpi_set_config_id' => request('hrm_kpi_set_config_id')
                ], [
                    'valid' => 1,
                    'users_id' => auth()->id(),
                ]);

               
            }

            return response()->json(['message' => 'Task Added.']);
        }

        $insert = HrmKPITaskDetails::query()->updateOrCreate([
            'hrm_kpi_task_id' => request('hrm_kpi_task_id'),
            'hrm_kpi_set_config_id' => request('hrm_kpi_set_config_id')
        ],[
            'valid' => 1,
            'users_id' => auth()->id(),
        ]);


        $this->recordActivity(
            1,
           'Tasked Added From KPI Set Config',
            $insert,
            $insert->id,
            'hrm_kpi_task_details'
        );


        return response()->json(['message' => 'Task Added.']);
    }

    public function kpiTaskRemove()
    {
        if (request('all')) {
            DB::table('hrm_kpi_task_details')
                ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
                ->delete();

            return response()->json(['message' => 'Task Deleted.']);
        }

        DB::table('hrm_kpi_task_details')
            ->where('hrm_kpi_task_id', request('hrm_kpi_task_id'))
            ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
            ->delete();

            $this->recordActivity(
                 1,
                 'Tasked Deleted From KPI Set Config',
                 null,
                 request('hrm_kpi_task_id'),
                 'hrm_religion'
            );

        return response()->json(['message' => 'Task Deleted.']);
    }

    public function departmentsStore()
    {
        if (request('all')) {
            foreach (request('hrm_depertment_id') as $key => $value) {
                HrmKPISetConfigDepartment::query()->updateOrCreate([
                    'hrm_depertment_id' => $value,
                    'hrm_kpi_set_config_id' => request('hrm_kpi_set_config_id')
                ], ['users_id' => auth()->id()]);
            }

            return response()->json(['message' => 'Department Added.']);
        }

        HrmKPISetConfigDepartment::query()->updateOrCreate([
            'hrm_depertment_id' => request('hrm_depertment_id'),
            'hrm_kpi_set_config_id' => request('hrm_kpi_set_config_id')
        ], ['users_id' => auth()->id()]);

        return response()->json(['message' => 'Department Added.']);
    }

    public function departmentsRemove()
    {
        if (request('all')) {
            HrmKPISetConfigDepartment::query()
                ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
                ->get()->each->delete();

            return response()->json(['message' => 'Department Deleted.']);
        }

        $configDepartment = HrmKPISetConfigDepartment::query()
            ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
            ->where('hrm_depertment_id', request('hrm_depertment_id'))
            ->first();

        $configDepartment->delete();

        return response()->json(['message' => 'Department Deleted.']);
    }

    public function departmentsRemoveNotNa()
    {
        $configDepartment = HrmKPISetConfigDepartment::query()
            ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
            ->where('hrm_depertment_id', request('hrm_depertment_id'))
            ->first();

        HrmKPISetConfigDeptWith::query()
            ->where('hrm_kpi_set_config_department_id', $configDepartment->id)
            ->get()
            ->each->delete();

        $configDepartment->delete();

        return response()->json(['message' => 'Department Deleted.']);
    }

    public function departmentsSet()
    {
        HrmKPISetConfigDepartment::query()->updateOrCreate([
            'hrm_depertment_id' => request('hrm_depertment_id'),
            'hrm_kpi_set_config_id' => request('hrm_kpi_set_config_id')
        ], ['users_id' => auth()->id()]);

        return response()->json(['message' => 'Department Added.']);
    }

    public function departmentsDetail()
    {
        $kpiConfig = DB::select("SELECT
                a.tag_with_department,
                a.is_confirmed,
                d.table_name,
                d.display_name,
                d.table_field_name
            FROM
                hrm_kpi_set_config a
                    JOIN
                hrm_kpi_dynamic_table d ON a.tag_with_department = d.id
            WHERE a.id = ?
        ", [request('hrm_kpi_set_config_id')])[0];

        $tableData = DB::table($kpiConfig->table_name)
            ->select('id', $kpiConfig->table_field_name . ' as table_field_name')
            ->whereValid(true)->get();

        $display_name = $kpiConfig->display_name;
        $depertment_name = request('depertment_name');

        $configDept = HrmKPISetConfigDepartment::query()
            ->where('hrm_depertment_id', request('hrm_depertment_id'))
            ->where('hrm_kpi_set_config_id', request('hrm_kpi_set_config_id'))
            ->first();

        $selectedDeptWith = HrmKPISetConfigDeptWith::query()
            ->where('hrm_kpi_set_config_department_id', $configDept->id)
            ->pluck('id', 'ref_id')
            ->toArray();

        return response()->json([
            'message' => 'Department Added.',
            'tableData' => $tableData,
            'display_name' => $display_name,
            'department_name' => $depertment_name,
            'hrm_kpi_set_config_department_id' => $configDept->id,
            'config_is_confirmed' => $kpiConfig->is_confirmed,
            'selectedDeptWith' => $selectedDeptWith
        ]);
    }

    public function kpiCategoriesStore()
    {
        HrmKPISetConfigDeptWith::query()->updateOrCreate([
            'hrm_kpi_set_config_department_id' => request('hrm_kpi_set_config_department_id'),
            'ref_id' => request('ref_id')
        ], ['users_id' => auth()->id()]);

        return response()->json([
            'message' => 'Added.'
        ]);
    }

    public function kpiCategoriesRemove()
    {
        HrmKPISetConfigDeptWith::query()
            ->where('hrm_kpi_set_config_department_id', request('hrm_kpi_set_config_department_id'))
            ->where('ref_id', request('ref_id'))
            ->delete();

        return response()->json([
            'message' => 'Deleted.'
        ]);
    }

    public function configFinalSubmit()
    {
        $configId = request('hrm_kpi_set_config_id');

        $config = HrmKPISetConfig::query()->findOrFail($configId);

        $config->update(['is_confirmed' => 1]);

        return response()->json(['message' => 'Setup has been configured.']);
    }
}
