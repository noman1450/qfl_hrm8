<?php

namespace App\Http\Controllers\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HrmCustomFilterMaster;
use App\Models\HrmCustomFilterDetails;
use Illuminate\Support\Facades\Validator;

class CustomFilterController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = DB::select("SELECT
                    a.id,
                    a.filter_name,
                    b.description
                FROM
                    hrm_custom_filter_master a
                        JOIN
                    hrm_custom_filter b ON a.hrm_custom_filter_id = b.id
                        AND a.valid = 1
            ");

            return datatables()->of($data)
                ->addColumn('Action', function ($data) {
                    return '
                    <a href="'.url('/custom_filter').'/'.encrypt($data->id).'/edit" class="btn btn-success btn-sm">
                        Edit
                    </a>
                    <a href="'.url('custom_filter/'.encrypt($data->id).'/delete').'" onclick="return confirm(\'Are you sure to delete this.!\')" class="btn btn-danger btn-sm">
                        Delete
                    </a>';
                })
                ->rawColumns(['Action'])
                ->make(true);
        }

        return view('custom_filter.index');
    }

    public function create()
    {
        return view('custom_filter.create');
    }

    public function getTableData()
    {
        $table = request('tableName');

        $data['tableData'] = DB::table($table)->get();

        $data['tableColumns'] = DB::getSchemaBuilder()->getColumnListing($table);

        return response()->json(
            view('custom_filter.table_data', $data)->render()
        );
    }

    public function store(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'filter_name' => 'required|string|max:45',
            'hrm_custom_filter_id' => 'required|integer|exists:hrm_custom_filter,id'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = "Form Validation Failed..!";
        } else {
            DB::beginTransaction();
            try {
                if ($request->ref_ids > 0) {
                    $masterId = HrmCustomFilterMaster::create([
                        'filter_name' => $request->filter_name,
                        'hrm_custom_filter_id' => $request->hrm_custom_filter_id,
                        'valid' => 1,
                        'users_id' => auth()->id(),
                    ])->id;

                    foreach ($request->ref_ids as $ref_id) {
                        HrmCustomFilterDetails::create([
                            'hrm_custom_filter_master_id' => $masterId,
                            'ref_id' => $ref_id,
                        ]);
                    }

                    DB::commit();

                    $this->recordActivity(
                         1,
                         'Created Custom Filter Name',
                         null,
                         $masterId,
                         'hrm_custom_filter_master'
                    );

                    $status = true;
                    $message = "Custom filter has been created.!";
                } else {
                    $status = false;
                    $message = "Please select a checkbox";
                }
            } catch (\Exception $e) {
                DB::rollBack();

                $status = false;
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status'            => $status,
            'message'           => $message ?? '',
            "error"             => $error ?? ''
        ]);
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $filterMaster = DB::select("SELECT
                a.id,
                a.hrm_custom_filter_id,
                a.filter_name,
                b.table_name,
                b.description
            FROM
                hrm_custom_filter_master a
                    JOIN
                hrm_custom_filter b ON a.hrm_custom_filter_id = b.id
                AND a.id = $id
        ")[0];

        if (request()->ajax()) {
            $tableData = DB::table($filterMaster->table_name)->get();

            $tableColumns = DB::getSchemaBuilder()->getColumnListing($filterMaster->table_name);

            $filterMasterDetails = HrmCustomFilterDetails::query()
                ->where('hrm_custom_filter_master_id', $id)
                ->pluck('ref_id')->toArray();

            return response()->json(
                view('custom_filter.table_data', compact('tableData', 'tableColumns', 'filterMasterDetails'))->render()
            );
        }

        return view('custom_filter.edit', compact('filterMaster'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $status = false;

        $validator = Validator::make($request->all(), [
            'filter_name' => 'required|string|max:45',
            'hrm_custom_filter_id' => 'required|integer|exists:hrm_custom_filter,id'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = "Form Validation Failed..!";
        } else {
            DB::beginTransaction();
            try {
                if ($request->ref_ids > 0) {
                    $master = HrmCustomFilterMaster::query()->findOrFail($id);

                    $master->update([
                        'filter_name' => $request->filter_name,
                        'hrm_custom_filter_id' => $request->hrm_custom_filter_id
                    ]);

                    DB::table('hrm_custom_filter_details')
                        ->where('hrm_custom_filter_master_id', $id)
                        ->delete();

                    foreach ($request->ref_ids as $ref_id) {
                        HrmCustomFilterDetails::create([
                            'hrm_custom_filter_master_id' => $id,
                            'ref_id' => $ref_id,
                        ]);
                    }

                    DB::commit();

                    $this->recordActivity(
                         1,
                         'Updated Custom Filter Name',
                         $master->getChanges(),
                         $master->id,
                         'hrm_custom_filter_master'
                    );

                    $status = true;
                    $message = "Custom filter has been updated.!";
                } else {
                    $status = false;
                    $message = "Please select a checkbox";
                }
            } catch (\Exception $e) {
                DB::rollBack();

                $status = false;
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status'            => $status,
            'message'           => $message ?? '',
            "error"             => $error ?? ''
        ]);
    }

    public function delete($id)
    {
        $master = HrmCustomFilterMaster::query()->findOrFail(decrypt($id));

        $master->update(['valid' => 0]);

        $this->recordActivity(
             1,
             'Deleted Custom Filter Name',
             null,
             $master->id,
             'hrm_custom_filter_master'
        );

        return back()->with('message', 'Filter has been deleted.!');
    }

    public function filterNameList()
    {
        $data = DB::select("SELECT
                id,
                filter_name text
            FROM
                hrm_custom_filter_master
            Where valid = 1
        ");

        return response()->json($data);
    }

    public function tableNameList()
    {
        $tableNames = DB::table('hrm_custom_filter')
            ->select('id', 'description as text', 'table_name')
            ->get();

        return response()->json($tableNames);
    }
}
