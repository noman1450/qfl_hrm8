<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\{ HrmAssetBrand, HrmAssetInformation };

class AssetBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        if ($request->brand_id) {
            $validator = Validator::make($request->all(), [
                'brand_name' => 'required|string|max:255|unique:hrm_asset_brand,brand_name,'.$request->brand_id
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'brand_name' => 'required|string|max:255|unique:hrm_asset_brand,brand_name'
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
            $brand = HrmAssetBrand::find($request->brand_id);

            if ($brand==null){
                $brand = new HrmAssetBrand;
            }

            $brand->brand_name = $request->brand_name;
            $brand->users_id = auth()->user()->id;
            $brand->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 $request->brand_id ? 'Updated Brand Name' : 'Created Brand Name',
                 $request->brand_id ? $brand->getChanges() : $brand,
                 $brand->id,
                 'hrm_asset_brand'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return Redirect()->back()->with('error', $e->getMessage());
        }

        return response()->json(array(
            'success'   => true,
            'message'   => 'Success!'
        ));
    }

    public function destroy($id)
    {
        $HrmAssetBrand = HrmAssetBrand::where('id', $id)->first();
        $HrmAssetInformation = HrmAssetInformation::where('brand_id', $id)->first();
        // dd($HrmAssetInformation, $HrmAssetBrand);
        if ($HrmAssetInformation==null){
            // dd($HrmAssetBrand);
            $HrmAssetBrand->delete();

            $this->recordActivity(
                 1,
                 'Deleted Brand Name',
                 $HrmAssetBrand,
                 $id,
                 'hrm_asset_brand'
            );

            session()->flash('success', 'data has been successfully deleted!');
            return redirect()->route('asset.index');
        }else{
            session()->flash('warning', 'data has a dependency');
            return redirect()->route('asset.index');
        }

    }

    public function brand_list()
    {
        $brand_list = DB::select("SELECT * FROM hrm_asset_brand");
        return json_encode(array('data' => $brand_list));
    }

    public function brand_list_data(Request $request){
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,brand_name as text FROM hrm_asset_brand
                                WHERE brand_name LIKE '%$request->term%' ;");

        }else{
            $data = DB::select("SELECT id,brand_name as text FROM hrm_asset_brand
                                    WHERE brand_name LIKE '%$request->term%' ;");
        }
            return response()->json($data);
    }
}
