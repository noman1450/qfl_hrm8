<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{ HrmAssetType, HrmAssetInformation };

class AssetTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        if ($request->asset_type_id) {
            $validator = Validator::make($request->all(), [
                'asset_type_name'    => 'required|string|max:255|unique:hrm_asset_type,asset_type_name,'.$request->asset_type_id,
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'asset_type_name'    => 'required|string|max:255|unique:hrm_asset_type,asset_type_name',
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
        $asset_type = HrmAssetType::find($request->asset_type_id);

        if ($asset_type==null){
            $asset_type = new HrmAssetType;

        }

        $asset_type->asset_type_name = $request->asset_type_name;
        $asset_type->users_id = auth()->user()->id;

        $asset_type->save();
        DB::commit();

        $this->recordActivity(
             1,
             $request->asset_type_id ? 'Updated Asset Type Name' : 'Created Asset Type Name',
             $request->asset_type_id ? $asset_type->getChanges() : $asset_type,
             $asset_type->id,
             'hrm_asset_type'
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
            'message'   => 'Success',
        ));
    }

    public function destroy($id)
    {
        $hrmAssetType = HrmAssetType::where('id', $id)->first();
        $HrmAssetInformation = HrmAssetInformation::where('asset_type_id', $id)->first();
        // dd($HrmAssetInformation, $id);
        if ($HrmAssetInformation==null){
            // dd($HrmAssetBrand);
            $hrmAssetType->delete();

            $this->recordActivity(
                 1,
                 'Deleted Asset Type',
                 $hrmAssetType,
                 $id,
                 'hrm_asset'
            );
            session()->flash('success', 'data has been successfully deleted!');
            return redirect()->route('asset.index');
        }else{
            session()->flash('warning', 'Data has a dependency!');
            return redirect()->route('asset.index');
        }
    }

    public function asset_type_list()
    {
        $asset_type_list = DB::select("SELECT * FROM hrm_asset_type");
        return json_encode(array('data' => $asset_type_list));
    }

    public function asset_type_list_data(Request $request){
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,asset_type_name as text FROM hrm_asset_type
                                WHERE asset_type_name LIKE '%$request->term%' ;");

        }else{
            $data = DB::select("SELECT id,asset_type_name as text FROM hrm_asset_type
                                    WHERE asset_type_name LIKE '%$request->term%' ;");
        }
            return response()->json($data);
    }
}
