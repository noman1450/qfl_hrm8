<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Traits\HasTree;

class PermissionsController extends Controller
{
    use HasTree;
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $permissionTreeView = $this->simpleTreeView();
        return view("role_permission.permissionlist", compact('permissionTreeView'));
    }

    public function getpermissionlist()
    {
        $view_data = DB::table('permissions as a')
            ->leftJoin('permissions as b', 'a.permission_id', '=', 'b.id')
            ->select('a.id', 'a.name', 'b.name as under')
            ->where('a.isActive', 1)
            ->get();

        return datatables()->of($view_data)
            ->addColumn('Link', function($view_data) {
                return '
                <a href="'. route('permission.edit', encrypt($view_data->id)) .'" class="btn btn-info btn-sm btn-block">
                    <span class="glyphicon glyphicon-edit"></span> Edit
                </a>
                <a href="'. route('permission.delete', encrypt($view_data->id)) .'" onclick="return confirm(\'Are you sure to delete this..!!\')" class="btn btn-danger btn-sm btn-block">
                    <span class="glyphicon glyphicon-trash"></span> Delete
                </a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function create()
    {
        return view("role_permission.permission");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.*' => [
                    'required', 'string', 'max:255',
                    Rule::unique('permissions', 'name')
                        ->where('permission_id', $request->permission_id)
                ],
            'permission_id' => 'nullable|integer|exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {

            $data = [];

            foreach ($request->name as $key => $value) {
                array_push($data, [
                    'name' => $request->name[$key],
                    'permission_id' => $request->permission_id,
                    'guard_name' => 'web',
                    'isActive' => 1,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ]);
            }

            DB::table('permissions')
                ->insert($data);

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Permission',
                 null,
                 $request->permission_id,
                 'permissions'
            );

            $type = 'success';
            $message = 'Permission created successfully..!';
        } catch (\Exception $e) {
            DB::rollback();

            $type = 'danger';
            $message = $e->getMessage();
        }

        return redirect()->to('/permission')->with([
            'type' => $type,
            'message' => $message
        ]);
    }

    public function edit($id)
    {
        $permission = DB::table('permissions as a')
            ->leftJoin('permissions as b', 'a.permission_id', '=', 'b.id')
            ->select('a.id', 'a.name', 'b.name as under', 'b.id as parent_id')
            ->where('a.id', decrypt($id))
            ->first();

        return view("role_permission.permission_edit", compact('permission'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::table('permissions')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'permission_id' => $request->permission_id,
                ]);

                $this->recordActivity(
                     1,
                     'Updated Permission',
                     null,
                     $id,
                     'permissions'
                );

            $type = 'success';
            $message = 'Permission updated successfully..!';
        } catch (\Exception $e) {

            $type = 'danger';
            $message = $e->getMessage();
        }

        return redirect()->to('/permission')->with([
            'type' => $type,
            'message' => $message
        ]);
    }

    public function delete($id)
    {
        try {
            DB::table('permissions')
                ->where('id', decrypt($id))
                ->delete();

                $this->recordActivity(
                     1,
                     'Deleted Permission',
                     null,
                     decrypt($id),
                     'permissions'
                );

            $type = 'success';
            $message = 'Permission deleted successfully..!';
        } catch (\Exception $e) {

            $type = 'danger';
            $message = $e->getMessage();
        }

        return back()->with([
            'type' => $type,
            'message' => $message
        ]);
    }

    public function permissions_drop_list(Request $request)
    {
        $permissions = DB::table('permissions as a')
            ->leftJoin('permissions as b', 'a.permission_id', '=', 'b.id')
            ->leftJoin('permissions as c', 'b.permission_id', '=', 'c.id')
            ->where('a.isActive', 1)
            ->selectRaw('
                a.id,
                concat_ws(" | ", a.name, b.name, c.name) as text
            ')
            ->having('text', 'like', "%{$request->term}%")
            ->get();

        return response()->json($permissions);
    }
}
