<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;
use Response;

use App\Models\HrmFavouriteLink;
use App\Models\HrmMyNote;

class FavouriteLinkController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('favourite_link.favourite_link_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('favourite_link.create_favourite_link');
    }

    public function favourite_link_list(){

        $user_id = Auth::user()->id;
        $query = DB::SELECT("SELECT id,title,link_address FROM hrm_favourite_link WHERE users_id = $user_id ");
        return json_encode(array('data' => $query));
    }

    public function mynote_list(){

        $user_id = Auth::user()->id;
        $query = DB::SELECT("SELECT id,my_note FROM hrm_mynote WHERE users_id = $user_id ");
        return json_encode(array('data' => $query));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'link_address' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }
            $user_id = Auth::user()->id;

            $insert = new HrmFavouriteLink;
            $insert->title        = $request->title;
            $insert->link_address = $request->link_address;
            $insert->users_id     = $user_id;
            $insert->save();



        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('favourite_link');
    }


    public function mynote_store(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'mynote'        => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }
            $user_id = Auth::user()->id;

            $insert = new HrmMyNote;
            $insert->my_note        = $request->mynote;
            $insert->users_id     = $user_id;
            $insert->save();

            return Response::json(array('massages' => true ));
    }






    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $favourite_link      = HrmFavouriteLink::find($id);

        if (empty($favourite_link)){
            session()->flash('alert-danger', 'Invalid favourite_link !!');
            return Redirect()->back();
        }

        return view('favourite_link.edit_favourite_link')->with('favourite_link',$favourite_link);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'link_address' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $user_id = Auth::user()->id;

        $Update_data = HrmFavouriteLink::find($id);
        $Update_data->title          = $request->title;
        $Update_data->link_address   = $request->link_address;
        $Update_data->users_id       = $user_id;
        $Update_data->save();

        $request->session()->flash('alert-success', 'successfully updated');
        return Redirect::to('favourite_link');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancel(Request $request,$id){

        $cancel = HrmFavouriteLink::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid favourite_link !!');
            return Redirect()->back();
        }


        DB::table('hrm_favourite_link')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('favourite_link');

    }

    public function mynote_cancel(Request $request,$id){

        $cancel = HrmMyNote::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }


        DB::table('hrm_mynote')->where('id', '=', $id)->delete();

        // $request->session()->flash('alert-success', 'successfully deleted !');
        // return Redirect::to('favourite_link');

       return Response::json(array('massages' => true ));


    }



}
