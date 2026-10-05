<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Route;
use Log;

class UserController extends Controller
{
    public $successStatus = 200;

    public function appVersion(Request $request) 
    {
        $return = 1;
        if($request->app_version){
            if(DB::table('company_information')->where('app_version', $request->app_version)->exists()){
                $return = 1;
            }
        }
        return ["status" => $return];
    }
    
    public function login(Request $request)
    {




        $validator = Validator::make($request->all(), [
             'email' => 'required|email',
             'password' => 'required',
         ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Invalid Credentials',
                'data'    => '',
                'error'   => $validator->errors(),
                'error_code' => 404,
            ];
        } else {


            $isEmployee = User::where('email', request('email'))->first()->hrm_employee_id;
                
            // dd($isEmployee);

            if($isEmployee) {
                if (Auth::attempt(['email' => request('email'), 'password' => request('password')]) && $isEmployee) {
                    $user_id = Auth::user()->id;

                    $UserInfos = DB::SELECT("SELECT id,name,designation,email,valid,hrm_employee_id FROM users
                    WHERE id=$user_id LIMIT 1");

                    $UserInfo = [
                             'id' => json_decode($UserInfos[0]->id),
                             'name' => $UserInfos[0]->name,
                             'designation' => $UserInfos[0]->designation,
                             'email' => $UserInfos[0]->email,
                             'valid' => json_decode($UserInfos[0]->valid) ,
                             'hrm_employee_id' => json_decode($UserInfos[0]->hrm_employee_id),
                    ];

                      // Log::debug($UserInfo);

                    $data['user'] = $UserInfo;

                    // $data['user'] = Auth::user();

                    if($UserInfos[0]->valid == 0) {
                        $response = [
                            'success' => false,
                            'message' => 'User is not Active',
                        ];
                        return response()->json($response, 404);
                    }

                    $responses = $this->getTokenAndRefreshToken($request);

                    if(isset($responses->error)) {
                        $response = [
                          'success' => false,
                          'message' => 'Unauthorised',
                          'data'    => '',
                          'error'   => 'Token Error.',$responses->error,
                          'error_code' => 404,
                        ];
                    } else {
                        $response = [
                          'success' => true,
                          'message' => 'User login successful',
                          'accessToken' => "Bearer " . $responses->access_token,
                          'refreshToken' => $responses->refresh_token,
                          'expires_in' => $responses->expires_in,
                          'data'    => $data,
                          'error'   => '',
                          'error_code' => 200,
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Incorrect email or password..!',
                        'data'    => '',
                        'error'   => '',
                        'error_code' => 401,
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Incorrect email..!',
                    'data'    => '',
                    'error'   => '',
                    'error_code' => 401,
                ];
            }


            // Log::debug($response);

            
        }

        return response()->json($response);
    }
    /**
     * Refresh Token
     */
    public function refreshToken(Request $request){
        $oClient = OClient::where('password_client', 1)->first();

        $request->request->add([
            'grant_type'    => 'refresh_token',
            'client_id'     => $oClient->id,
            'client_secret' => $oClient->secret,
            'refresh_token' => $request->refresh_token,
            'scope'         => '*',
        ]);
        $tokenRequest = $request->create(
            '/oauth/token',
            'post'
        );

        $instance  = Route::dispatch($tokenRequest);
        $responses = json_decode($instance->getContent());

        if(isset($responses->error)){
            $response = [
                'success' => false,
                'message' => 'Unauthorised',
                'data'    => '',
                'error'   => 'Token Error.',$responses->error,
                'error_code' => 401,
            ];
        }else{
            $response = [
                'success' => true,
                'message' => 'success',
                'accessToken' => "Bearer " . $responses->access_token,
                'refreshToken' => $responses->refresh_token,
                'expires_in' => $responses->expires_in,
                'data'    => '',
                'error'   => '',
                'error_code' => 200,
            ];
        }
        return response()->json($response);
    }

    public function details() {
        $user = Auth::user();
        return response()->json($user, $this->successStatus);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens->each(function($token, $key) {
                $token->delete();
            });

            $response = [
                'success' => true,
                'message' => 'Successfully logged out',
                'error_code' => 200
            ];

            return response()->json($response);

        } catch (Exception $e) {
            return response()->json("unauthorized", 401);
        }
    }

    public function unauthorized()
    {
        return response()->json("unauthorized", 401);
    }

    //---------------------------------------------------------------------------
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['user_name'] = $input['name'];
        $input['password'] = bcrypt($input['password']);

        User::create($input);
        //
        $responses = $this->getTokenAndRefreshToken($request);
        if(isset($responses->error)){
            $response = [
              'success' => false,
              'message' => 'Unauthorised',
              'data'    => '',
              'error'   => 'Token Error.',$responses->error,
              'error_code' => 404,
            ];
        }else{
            $user = Auth::user();
            $response = [
              'success' => true,
              'message' => 'User Register successful',
              'accessToken' => $responses->access_token,
              'refreshToken' => $responses->refresh_token,
              'expires_in' => $responses->expires_in,
              'data'    => $user,
              'error'   => '',
              'error_code' => 200,
            ];
        }
        return response()->json($response);
    }
    /**
     * Get Token And Refresh Token
     */
    public function getTokenAndRefreshToken(Request $request) {
        $oClient = OClient::where('password_client', 1)->first();
        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $oClient->id,
            'client_secret' => $oClient->secret,
            'username'      => request('email'),
            'password'      => request('password'),
            'scope'         => '*',
        ]);
        $tokenRequest = $request->create(
            '/oauth/token',
            'post'
        );

        $instance  = Route::dispatch($tokenRequest);

        return json_decode($instance->getContent());
    }
    /**
     * Upload user Image
     */
    public function userImageUpload(Request $request)
    {
        $user = []; //-------need to remove and comment our others
        $response = [];
        $error_code = '';

        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = md5(time()).'.'.$extension;
            $file->move(public_path('userImage'),$filename);
            $user->image=$filename;
            $user->save();
            if($user->save()) {
                $response = [
                  'success' => true,
                  'message' => 'success',
                  'data' => $user,
                  'error' =>'',
                ];
                $error_code = 200;
            }
        } else {
            return $request;
        }

        return response()->json($response, $error_code);
    }

    //--------------------------------------------------------------------------
    //-------------------------InterviewLink
    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'email' => 'required|email',
             'password' => 'required',
         ]);

        if ($validator->fails()) {
            $response = [
               'success' => false,
               'message' => 'Invalid Credentials',
               'data'    => '',
               'error'   => $validator->errors(),
               'error_code' => 404,
            ];
        } else {//chech is active
            $isEmployee = User::where('email', request('email'))->first()->employees_id;
            if($isEmployee) {
                if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                    $data['user'] = $user = Auth::user();
                    if($user->is_active == 0){
                        $response = [
                            'success' => false,
                            'message' => 'User is not Active',
                        ];
                        return response()->json($response, 404);
                    }
                    $oClient = OClient::where('password_client', 1)->first();
                    $responses = $this->getTokenAndRefreshToken($request);
                    if(isset($responses->error)){
                        $response = [
                          'success' => false,
                          'message' => 'Unauthorised',
                          'data'    => '',
                          'error'   => 'Token Error.',$responses->error,
                          'error_code' => 404,
                        ];
                    }
                    else {
                        $response = [
                          'success' => true,
                          'message' => 'User login successful',
                          'accessToken' => "Bearer " . $responses->access_token,
                          'refreshToken' => $responses->refresh_token,
                          'expires_in' => $responses->expires_in,
                          'data'    => $data,
                          'error'   => '',
                          'error_code' => 200,
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Incorrect email or password..!',
                        'data'    => '',
                        'error'   => '',
                        'error_code' => 401,
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Incorrect email..!',
                    'data'    => '',
                    'error'   => '',
                    'error_code' => 401,
                ];
            }
        }
        return response()->json($response);
    }
}
