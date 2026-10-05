<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportSignatoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dataset = DB::select("SELECT  a.* FROM hrm_signatory_config a ");
        return view('report_signatory.index',compact('dataset'));
    }


    public function report_signatory_list_data(Request $request){
        $data = DB::select("SELECT  a.* FROM hrm_signatory_config a ");
        return json_encode(array('data' => $data));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            foreach ($request->report as $key => $val) {
                DB::table('hrm_signatory_config')
                    ->where('id', $key)
                    ->update([
                        'sg1'       => $val['sg1'],
                        'sg2'       => $val['sg2'],
                        'sg3'       => $val['sg3'],
                        'sg4'       => $val['sg4'],
                        'sg5'       => $val['sg5'],
                        'sg6'       => $val['sg6'],
                        'sg7'       => $val['sg7'],
                        'sg8'       => $val['sg8'],
                        'sg9'       => $val['sg9'],
                        'sg10'      => $val['sg10'],
                        'users_id'  => Auth::user()->id,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);

            }

            $data = DB::table('hrm_signatory_config')->get();

            DB::commit();
            $this->recordActivity(
                1,
                'Update Salary Sheet Signatory',
                $data,
                '',
                'hrm_signatory_config'
           );


            return Redirect::to('report_signatory')->with('success', 'Data updated successfully');

        } catch (\Exception $e) {
            // Rollback transaction if an error occurs
            DB::rollback();
            return Redirect::back()->withErrors(['error' => 'Something went wrong!']);
        }
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

}