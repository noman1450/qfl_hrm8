<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use App\Models\HrmKPIMarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KPIMarkController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            $hrm_kpi_set_config_id = decrypt(request()->hrm_kpi_set_config_id);

            $data = DB::select("SELECT
                    a.*,
                    b.is_confirmed
                FROM
                    hrm_kpi_marks a
                        JOIN
                    hrm_kpi_set_config b ON a.hrm_kpi_set_config_id = b.id
                WHERE hrm_kpi_set_config_id = $hrm_kpi_set_config_id
                AND valid = 1
            ");

            return datatables()->of($data)
                ->addColumn('Link', function ($data) {
                    if (! $data->is_confirmed) {
                        return '
                        <a href="'.route('kpi_mark.edit', encrypt($data->id)).'" data-title="Edit Mark" footer-none class="modalLink btn btn-sm btn-info">
                            Edit
                        </a>

                        <form action="'.route('kpi_mark.destroy', encrypt($data->id)).'" method="post" class="deleteMark" style="display:inline-block">
                            '.csrf_field().'
                            '.method_field("delete").'
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Record">Delete</button>
                        </form>';
                    }
                })
                ->rawColumns(['Link'])
                ->make(true);
        }

        return view('kpi_mark.kpi_mark_list');
    }

    public function create()
    {
        $kpiMark = null;

        $hrm_kpi_set_config_id = request()->hrm_kpi_set_config_id;

        return view('kpi_mark._form', compact('kpiMark', 'hrm_kpi_set_config_id'));
    }

    public function kpi_marks_list(Request $request)
    {
        return json_encode(array('data' => HrmKPIMarks::where('valid',1)->get()));
    }

    public function store(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'mark_name' => 'required',
            'point'     => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation.';
        } else {
            try {
                HrmKPIMarks::create([
                    'description' => $request->mark_name,
                    'hrm_kpi_set_config_id' => decrypt($request->hrm_kpi_set_config_id),
                    'point' => $request->point,
                    'valid' => 1,
                    'users_id' => auth()->id()
                ]);

                $status = true;
                $message = 'Mark has been created.';
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

    public function edit($id)
    {
        $kpiMark = HrmKPIMarks::query()->findOrFail(decrypt($id));

        return view('kpi_mark._form', compact('kpiMark'));
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'mark_name' => 'required',
            'point'     => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation.';
        } else {
            try {
                $kpiMark = HrmKPIMarks::query()->findOrFail(decrypt($id));

                $kpiMark->update([
                    'description' => $request->mark_name,
                    'point' => $request->point,
                    'users_id' => auth()->id()
                ]);

                $status = true;
                $message = 'Mark has been updated.';
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

    public function destroy($id)
    {
        $kpiMark = HrmKPIMarks::query()->findOrFail(decrypt($id));

        try {
            $kpiMark->update(['valid' => 0]);

           $status = true;
            $message = 'Mark has been deleted.';
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }
}
