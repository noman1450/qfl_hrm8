<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\HrmUserLocation;
use App\Traits\HasTree;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class UsersController extends Controller
{
    use HasTree;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        return view('users.users_list');
    }

    public function userslocationlist(Request $request)
    {
        $locationlist = DB::select("SELECT a.id,a.location_name,b.users_id,b.default_location FROM hrm_location a LEFT JOIN user_location b ON a.id=b.hrm_location_id AND a.valid=1 AND b.users_id=$request->user_id ");

        return json_encode(array('data' => $locationlist));
    }

    public function users_list()
    {
        $userslists = DB::select("SELECT
                                        a.id,
                                        a.name,
                                        a.email,
                                        c.location_name,
                                        GROUP_CONCAT(e.name) AS display_name,
                                        IF(a.valid = 1,'Active','Deactive') as status,
                                        a.valid
                                    FROM
                                        users a
                                            LEFT JOIN
                                        user_location b ON a.id = b.users_id
                                            AND b.default_location = 1
                                            LEFT JOIN
                                        hrm_location c ON b.hrm_location_id = c.id
                                            LEFT JOIN
                                        model_has_roles d ON a.id = d.model_id
                                            LEFT JOIN
                                        roles e ON e.id = d.role_id
                                        WHERE a.valid=1
                                    GROUP BY a.id , a.name , a.email , c.location_name,a.valid");


        return json_encode(array('data' => $userslists));
    }

    public function reset($id)
    {
        $user_data= DB::SELECT("SELECT id,name,email,designation FROM users WHERE id=$id");
        return view('users.reset_password')
            ->with('user_data',$user_data);

        // return view('auth.passwords.reset');
    }

    public function usersLog($id)
    {
        $userinfo = User::find($id);

        // dd($userinfo);
        if (request()->ajax()) {

            $current_date = date('Y-m-d H:i:s');
            $previous_month  = date('Y-m-d H:i:s', strtotime("-1 months", strtotime($current_date)));

            $userLogs = DB::table('users_logs as a')
                ->selectRaw("
                    a.id,
                     a.updated_at,
                    concat(
                        'Details: ', ifnull(a.change_details, '[]'), ' <br /> '

                    ) as details,
                    a.master_table_id,
                    a.master_table_name,
                    a.login_from_ip,
                    a.screen_from

                ")
                ->where('users_id', $id)
                ->where('a.updated_at','>', $previous_month)
                ->get();

            return datatables()->of($userLogs)
                ->rawColumns(['details'])
                ->make(true);
        }


        return view('users.users_log')->with('userinfo',$userinfo);
    }

    /*public function usersLog($id)
    {
        // $user_data= DB::SELECT("SELECT id,name,email,designation FROM users WHERE id=$id");
        // return view('users.reset_password')
        //     ->with('user_data',$user_data);

         //return view('users.users_log');

        if (request()->ajax()) {
            $userLogs = DB::table('users_logs as a')
                ->join('config_modules as b', 'a.config_modules_id', '=', 'b.id')
                ->selectRaw("
                    a.id,
                    concat(
                        'Module Name: ', b.module_name, ' <br /> ',
                        'Title: ', a.screen_from, ' <br /> ',
                        'Change Details: ', ifnull(a.change_details, '[]'), ' <br /> ',
                        'Table Id: ', a.master_table_id, ' <br /> ',
                        'Table Name: ', a.master_table_name, ' <br /> ',
                        'Login Address: ', a.login_from_ip, ' <br /> ',
                        'Operation Time: ', a.updated_at

                    ) as details
                ")
                ->where('users_id', decrypt($id))
                ->get();

            return datatables()->of($userLogs)
                ->rawColumns(['details'])
                ->make(true);

        }
        return view('users.users_log');
    }*/



    public function resetmypassword()
    {
        $user_id=Auth::user()->id;

        $user_data= DB::SELECT("SELECT id,name,email,designation FROM users WHERE id=$user_id");
        return view('users.reset_password')
            ->with('user_data',$user_data);

        // return view('auth.passwords.reset');
    }

    public function password_reset(Request $request)
    {
        //dd($request->all());

        $validator = Validator::make($request->all(), [
            'password'          => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('users')
                ->withErrors($validator)
                ->withInput();
        }

        $bcrypt= bcrypt($request->password);

        DB::update("UPDATE users SET password = '$bcrypt' WHERE id = $request->user_id");


        $this->recordActivity(
             1,
             'Updated Password Reset',
             null,
             $request->user_id,
             'users'
        );


        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return redirect()->to('home');

    }

    public function create(Request $request)
    {

        $user_allow = DB::table('company_information')
            ->select('user_allow_to_access')
            ->first()->user_allow_to_access;

        $active_users = DB::SELECT("SELECT count(id) as user FROM users where valid = 1")[0]->user;



        if($user_allow<=$active_users){
            $request->session()->flash('alert-danger', 'Your user limit has been exceed !!!');
            return redirect()->to('users');

        };


        $role_lists = DB::select("SELECT id, name as display_name from roles");

        return view('users.create_users')
            ->with('role_list',$role_lists);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'          => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|min:6|confirmed',
            'user_type'          => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $check_data= DB::SELECT("SELECT id from users WHERE email='$request->email' AND valid=1");

        if (!empty($check_data)) {
            return back();
        }


        DB::beginTransaction();
        try {
            $userdata = User::create([
                'name'              => $request->username,
                'email'             => $request->email,
                'designation'       => $request->designation,
                'password'          => bcrypt($request->password),
                'hrm_employee_id'   => $request->employee_name,
                'user_type'   => $request->user_type,
                'created_by_users_id'   => auth()->id(),
            ]);

            $count_row=count($request->permissionlocation);

            if (!empty($count_row)){

                foreach ($request->permissionlocation as $keys ) {

                    $insert     = new HrmUserLocation;
                    $insert->users_id          = $userdata->id;
                    $insert->hrm_location_id   = $keys;
                    $insert->default_location  = 0;
                    $insert->save();


                    $this->recordActivity(
                         1,
                         'Created New User',
                         $insert,
                         $insert->hrm_location_id,
                         'user_location'
                    );
                }

                $defaultlocation=$request->defaultlocation[0];
                DB::update("UPDATE user_location SET default_location = 1
                                                WHERE hrm_location_id = $defaultlocation and users_id=$userdata->id");
            }else{
                return back();
            }

            //Create User Role

            $userdata->assignRole($request->input('userrole'));

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created New User',
                 $userdata,
                 $userdata->id,
                 'users'
            );



        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return redirect()->to('users');
    }

    public function edit($id)
    {
        $userslists = DB::select("SELECT a.id,
                a.name,
                a.email,
                a.user_type,
                c.location_name,
                a.designation,
                e.name display_name,
                e.id User_role_id,
                f.id employee_id,
                f.employee_name
            FROM
                users a
                    LEFT JOIN
                user_location  b ON a.id = b.users_id
                    AND b.default_location = 1
                    LEFT JOIN
                hrm_location c ON b.hrm_location_id = c.id
                    LEFT JOIN
                model_has_roles d ON a.id = d.model_id
                    LEFT JOIN
                roles e ON e.id = d.role_id
                    LEFT JOIN
                hrm_employee f ON a.hrm_employee_id = f.id
            WHERE a.id = $id
        ");

        $role_lists = DB::select("SELECT id, name as display_name from roles");

        return view('users.edit_users')
            ->with('role_list',$role_lists)
            ->with('edit_data',$userslists);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'user_type' => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return redirect('users')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try{

            DB::table('user_location')->where('users_id', $request->user_id)->delete();
            DB::table('model_has_roles')->where('model_id', $request->user_id)->delete();


            //Create User Location
            $count_row=count($request->permissionlocation);

            if (!empty($count_row)) {

                foreach ($request->permissionlocation as $keys ) {

                    $insert     = new HrmUserLocation;
                    $insert->users_id          = $request->user_id;
                    $insert->hrm_location_id   = $keys;
                    $insert->default_location  = 0;
                    $insert->save();

                    $this->recordActivity(
                         1,
                         'Updated User Locations',
                         $insert,
                         $insert->hrm_location_id,
                         'user_location'
                    );
                }

                $defaultlocation=$request->defaultlocation[0];
                DB::update("UPDATE user_location SET default_location = 1
                                                WHERE hrm_location_id = $defaultlocation and users_id=$request->user_id");
            } else {
                return redirect('users/create');
            }

            if($request->employee_name==null) {
                $employee_id = 'null';
            } else {
                $employee_id = $request->employee_name;
            }

            $userdata = User::query()->findOrFail($request->user_id);

            $userdata->update([
                'designation' =>  $request->designation,
                'name' => $request->username,
                'email' => $request->email,
                'hrm_employee_id' => $employee_id,
                'user_type'   => $request->user_type,
                'created_by_users_id'   => auth()->id(),
            ]);

            $userdata->assignRole($request->input('userrole'));

            //DB::update("UPDATE users SET designation =  '$request->designation',name= '$request->username',email='$request->email',hrm_employee_id=$employee_id WHERE id = $request->user_id");

            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated User Information',
                 $userdata->getChanges(),
                 $request->user_id,
                 'users'
            );

        }catch (\Exception $e) {
            DB::rollback();
            $validator->errors()->add('field', $e->getMessage());
            return response()->json($validator->errors()->all());
        }

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return redirect()->to('users');
    }

    public function cancel($id)
    {
        $password = bcrypt('zax!1%$l:)^'.$id);
        DB::update("UPDATE users SET valid = 0,password='$password' WHERE id = $id");

        $this->recordActivity(
             1,
             'Deleted User',
             null,
             $id,
             'users'
        );

        session()->flash('alert-success', 'data has been successfully deleted!');
        return redirect()->to('users');
    }

    public function reactive($id)
    {
        DB::update("UPDATE users SET valid = 1 WHERE id = $id");

        session()->flash('alert-success', 'data has been successfully deleted!');
        return redirect()->to('users');
    }
    public function role()
    {
        return view('users.role');
    }

    public function roleCreate()
    {
        $permissionTreeView = $this->permissionTreeView();

        return view('users.role_create', compact('permissionTreeView'));
    }

    public function roleStore(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);

            $role->syncPermissions($request->permission_id);

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Role',
                 null,
                 $role->id,
                 'roles'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->to('/role')->with('success', 'Role created successfully');
    }

    public function roleShow($id)
    {
        $role = Role::query()->findOrFail($id);

        $permissionTreeView = $this->simpleTreeView($role);

        return view('users.role_show', compact('permissionTreeView', 'role'));
    }

    public function roleEdit($id)
    {

        $role = Role::query()->findOrFail($id);

        $permissionTreeView = $this->permissionTreeView($role);

        return view('users.role_edit', compact('permissionTreeView', 'role'));
    }

    public function roleUpdate(Request $request, $id)
    {


        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'permission_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::query()->findOrFail($id);

            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permission_id);




            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated Role',
                 $role->getChanges(),
                 $role->id,
                 'roles'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->to('/role')->with('success', 'Role updated successfully');
    }

    public function roleDelete($id)
    {
        DB::beginTransaction();
        try {

            DB::table('role_has_permissions')->where('role_id', $id)->delete();

            DB::table('roles')->where('id', $id)->delete();

            DB::commit();

            $this->recordActivity(
                 1,
                 'Deleted Role',
                 null,
                 $id,
                 'roles'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors($e->getMessage())->withInput();
        }

        return back()->with('success', 'Role deleted successfully');
    }

    public function role_list()
    {
        $role_lists = DB::select("SELECT id, name from roles");
        return json_encode(array('data' => $role_lists));
    }

    public function role_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('role')
                ->withErrors($validator)
                ->withInput();
        }

        $role= new Role;
        $role->name = $request->name;
        $role->guard_name = 'web';
        $role->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return redirect()->back();
    }
}
