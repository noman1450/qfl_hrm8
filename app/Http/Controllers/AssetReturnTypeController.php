<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{ HrmAssetReturnType};

class AssetReturnTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function store(Request $request)
    {
        if ($request->asset_return_type_id) {
            $validator = Validator::make($request->all(), [
                'asset_return_type_name' => 'required|string|max:255|unique:hrm_asset_return_type,asset_return_type_name,'.$request->asset_return_type_id
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'asset_return_type_name' => 'required|string|max:255|unique:hrm_asset_return_type,asset_return_type_name'
            ]);
        }

        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'message'  => implode(",", $validator->getMessageBag()->all())
            ));
        }

        DB::beginTransaction();
        try {

            $asset_return_type = HrmAssetReturnType::find($request->asset_return_type_id);
            if ($asset_return_type==null){
                $asset_return_type = new HrmAssetReturnType;
            }
            $asset_return_type->return_status = $request->return_status;
            $asset_return_type->asset_return_type_name = $request->asset_return_type_name;
            $asset_return_type->users_id = auth()->user()->id;

            $asset_return_type->save();

            DB::commit();

            $this->recordActivity(
                 1,
                 $request->asset_return_type_id ? 'Updated Asset Return Type Name' : 'Created Asset Return Type Name',
                 $request->asset_return_type_id ? $asset_return_type->getChanges() : $asset_return_type,
                 $asset_return_type->id,
                 'hrm_asset_return_type'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(array(
                'success'   => false,
                'message'   => $e->getMessage()
            ));
        }
        return response()->json(array(
            'success'   => true,
            'message'   => 'Success!'
        ));
    }

    public function destroy($id)
    {
        $HrmAssetReturnType = HrmAssetReturnType::where('id', $id)->first();
        $HrmAssetReturnType->delete();

        $this->recordActivity(
             1,
             'Deleted Asset Return Type Name',
             $HrmAssetReturnType,
             $HrmAssetReturnType,
             'hrm_asset_return_type'
        );

        return redirect()->route('asset.index')->with('success', 'destroyed! with lightning speed');
    }

    public function return_type_list()
    {
        $return_type_list = DB::select("SELECT *,IF(return_status=1,'Reuse','Destroy') as return_status_des  FROM hrm_asset_return_type");

        return json_encode(array('data' => $return_type_list));
    }

    public function return_type_list_data(Request $request)
    {
        $data = DB::select("SELECT
                id,
                concat_ws(' | ', asset_return_type_name, if(return_status = 1, 'Reuse', 'Destroy')) as text
            FROM hrm_asset_return_type
            WHERE asset_return_type_name LIKE '%$request->term%'
        ");

        return response()->json($data);
    }
}
