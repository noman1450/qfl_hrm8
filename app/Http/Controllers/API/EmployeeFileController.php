<?php

namespace App\Http\Controllers\API;

use App\Models\HrmEmployee;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeFile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeFileController extends Controller
{
    public function store(Request $request)
    {
        $success = false;

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrm_employee,id',
            'activity_date' => 'required|date',
            'file_type' => 'required|integer|exists:hrm_file_type,id',
            'file_title' => 'required|string',
            'note' => 'nullable|string|max:145',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = collect($validator->errors())->flatten()->implode(', ');
            $error_code = 422;
        } else {
            try {
                // $fileName = null;

                // if (!empty($request['image'])) {
                //     $image_parts = explode(";base64,", $request['image']);

                //     if (count($image_parts) > 1) {
                //         $image_type_aux = explode("image/", $image_parts[0]);
                //         $image_type = $image_type_aux[1];
                //         $image_base64 = base64_decode($image_parts[1]);
                //     } else {
                //         $image_base64 = base64_decode($image_parts[0]);
                //         $image_type = 'png';
                //     }

                //     $fileName = uniqid() . '.'.$image_type;
                //     $file = public_path().'/employee_file/'.$fileName;
                //     file_put_contents($file, $image_base64);
                // }

                $fileName = null;

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $var_path  = public_path('employee_file');
                    $ext = $file->getClientOriginalExtension();
                    $hash = $this->generateRandomString();
                    $fileName = $hash.'.'.$ext;
                    $file->move($var_path, $fileName);
                }

                $activity_date  = date('Y-m-d', strtotime($request->activity_date));

                $insert     = new HrmEmployeeFile;

                $insert->attached_date         = $activity_date;
                $insert->hrm_employee_id       = $request->employee_id;
                $insert->hrm_file_type_id      = $request->file_type;
                $insert->file_title            = $request->file_title;
                $insert->note                  = $request->note;
                $insert->images                = $fileName;
                $insert->save();

                $success = true;
                $message = 'Data has been successfully added!';
                $error_code = 200;
            } catch (\Exception $e) {
                $success = false;
                $message = 'Something went wrong..!';
                $error = $e->getMessage();
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function list(Request $request)
    {
        $success = false;

        $id = $request->employee_id;

        $employee = HrmEmployee::query()->find($id);

        if (empty($employee)) {
            $message = 'Employee not found';
            $error_code = 404;
        } else {
            try {
                $user_id = auth()->id();

                $data = DB::SELECT("SELECT
                    a.id,
                    c.file_title,
                    c.id as hrm_employee_file_id,
                    c.note,
                    c.attached_date as activity_date,
                    c.images as file_image,
                    f.file_type_name as file_type
                    from  hrm_employee a
                    JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                        AND  b.id in (
                            SELECT max(id) from hrm_employee_job_info
                                WHERE hrm_employee_id = $id
                        )
                    JOIN hrm_employee_file c ON b.hrm_employee_id=c.hrm_employee_id
                    JOIN hrm_file_type f ON c.hrm_file_type_id=f.id
                    JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                ");


                $success = true;
                $message = 'Success';
                $error_code = 200;
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function edit(Request $request)
    {
        $success = false;

        $id = $request->hrm_employee_file;

        $employee_file = HrmEmployeeFile::find($id);

        if (empty($employee_file)) {
            $message = 'File not found';
            $error_code = 404;
        } else {
            try {
                $data = DB::SELECT("SELECT
                        c.id,
                        c.file_title,
                        c.note,
                        c.attached_date,
                        c.images as file_images,
                        f.file_type_name as file_type,
                        f.id as file_type_id
                    from  hrm_employee a
                    JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                    JOIN hrm_employee_file c ON b.hrm_employee_id=c.hrm_employee_id AND c.id = $id
                    JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                    Join hrm_designation e On b.hrm_designation_id=e.id
                    JOIN hrm_file_type f ON c.hrm_file_type_id=f.id
                ")[0];


                $success = true;
                $message = 'Success';
                $error_code = 200;
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $message = 'Something went wrong..!';
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function update(Request $request)
    {
        $employee_file = HrmEmployeeFile::query()->findOrFail($request->hrm_employee_file_id);

        $validator = Validator::make($request->all(), [
            'hrm_employee_file_id' => 'required|integer|exists:hrm_employee_file,id',
            'activity_date' => 'required|date',
            'file_type' => 'required|integer|exists:hrm_file_type,id',
            'file_title' => 'required|string',
            'note' => 'nullable|string|max:145',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = collect($validator->errors())->flatten()->implode(', ');
            $error_code = 422;
        } else {
            try {
                $file = $request->file('image');
                $findImg = public_path().'/employee_file/'.$employee_file->images;

                if ($file) {
                    if (file_exists($findImg)) {
                        @unlink($findImg);
                    }

                    $var_path  = public_path().'/employee_file/';
                    $ext = $file->getClientOriginalExtension();
                    $hash = $this->generateRandomString();
                    $fileName = $hash.'.'.$ext;

                    $file->move($var_path, $fileName);
                    $fileUrl = $fileName;
                } else {
                    $fileUrl = $employee_file->images;
                }

                $activity_date  = date('Y-m-d', strtotime($request->activity_date));

                $employee_file->attached_date     = $activity_date;
                $employee_file->hrm_file_type_id  = $request->file_type;
                $employee_file->file_title        = $request->file_title;
                $employee_file->note              = $request->note;
                $employee_file->images            = $fileUrl;
                $employee_file->save();

                $success = true;
                $message = 'Data has been successfully updated!';
                $error_code = 200;

            } catch (\Exception $e) {
                $success = false;
                $message = 'Something went wrong..!';
                $error = $e->getMessage();
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function delete(Request $request)
    {
        $success = false;

        $employee_file = HrmEmployeeFile::query()->find($request->hrm_employee_file_id);

        if (empty($employee_file)) {
            $message = 'File not found';
            $error_code = 404;
        } else {
            try {
                if ($employee_file->images) {
                    $path = public_path() . '/employee_file/';
                    @unlink($path.$employee_file->images);
                }

                $employee_file->delete();

                $success = true;
                $message = 'Data has been successfully deleted!';
                $error_code = 200;
            } catch (\Exception $e) {
                $success = false;
                $message = 'Something went wrong..!';
                $error = $e->getMessage();
                $error_code = 500;
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message ?? '',
            'data' => $data ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
