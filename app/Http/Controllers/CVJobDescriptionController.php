<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HRMCVJobDescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Response;

class CVJobDescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function loadList()
    {
        $jobDescriptions = DB::table('hrm_cv_job_description as a')
            ->join('hrm_cv_job_description_group as b', 'a.hrm_cv_job_description_group_id', '=', 'b.id')
            ->leftjoin('hrm_depertment as c', 'a.hrm_depertment_id', '=', 'c.id')
            ->SELECT('a.*', 'b.id as job_description_group_id', 'b.job_description_group_name', 'c.id as hrm_depertment_id', 'c.depertment_name', DB::raw(
             '(CASE WHEN a.allow_points_calculation = "0" THEN "No" WHEN a.allow_points_calculation = "1" THEN "Yes" ELSE "Blank" END) AS status_lable')
        )->where('is_active', 1)->get();



            // ,'(CASE
            // WHEN hrm_cv_job_description.allow_points_calculation = "0" THEN "No"
            // ELSE "Yes"
            // END) AS allow_point)'

        $jobDescriptions = collect($jobDescriptions);

        return datatables()->of($jobDescriptions)->make(true);
    }

    public function store(Request $request)
    {

        if (empty($request->desc_id)) {
            $validator = Validator::make($request->all(), [
                'hrm_cv_job_description_group_id' => 'required',
                'hrm_depertment_id' => 'required',
                'job_description' => 'required|string|max:245|unique:hrm_cv_job_description,job_description',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'hrm_cv_job_description_group_id' => 'required',
                'hrm_depertment_id' => 'required',
                'job_description' => 'required|string|max:245|unique:hrm_cv_job_description,job_description,'.$request->desc_id
            ]);
        }

        if($validator->fails()) {
            return Response::json([
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ]);
        }

        $id = $request->desc_id;
        $jobDescription = HRMCVJobDescription::find($id);

        if ($jobDescription == null) {
            $jobDescription = new HRMCVJobDescription;
        } else {
            $check_data = DB::SELECT("SELECT
            a.id
            FROM
            hrm_cv_job_description a
                JOIN
            hrm_cv_job_requsition_details b ON a.id = b.hrm_cv_job_description_id
                AND b.is_active = 1 AND a.id=$id
                JOIN
            hrm_cv_job_requsition c ON b.hrm_cv_job_requsition_id = c.id
                AND c.is_active = 1 and c.status=2 LIMIT 1");

            if (!empty( $check_data)) {
                return Response::json(array(
                    'success'   => false,
                    'message'  => 'Sorry, this job description already published.!'
                ));
            }
        }

        $jobDescription->hrm_cv_job_description_group_id = $request->hrm_cv_job_description_group_id;
        $jobDescription->hrm_depertment_id = $request->hrm_depertment_id;
        $jobDescription->job_description = $request->job_description;
        $jobDescription->allow_points_calculation = $request->allow_points_calculation == "on" ? 1 : 0;
        $jobDescription->is_active = 1;
        $jobDescription->users_id = auth()->id();
        // dd($jobDescription);

        $jobDescription->save();

        $this->recordActivity(
             1,
             $id ? 'Updated CV Job Description' : 'Created CV Job Description',
             $id ? $jobDescription->getChanges() : $jobDescription,
             $jobDescription->id,
             'hrm_cv_job_description'
        );


        return Response::json(array(
            'success'   => true,
            'message'   => 'Success',
        ));
    }

    public function softDelete($id)
    {
        $check_data = DB::SELECT("SELECT
                a.id
                FROM
                hrm_cv_job_description a
                    JOIN
                hrm_cv_job_requsition_details b ON a.id = b.hrm_cv_job_description_id
                    AND b.is_active = 1 AND a.id=$id
                    JOIN
                hrm_cv_job_requsition c ON b.hrm_cv_job_requsition_id = c.id
                    AND c.is_active = 1 and c.status=2 LIMIT 1");

        if (! empty( $check_data)) {
            return Response::json(array(
                'success'   => false,
                'message'  => 'Sorry, this job description already published.!'
            ));
        }

        $jobDescription = HRMCVJobDescription::query()->find($id);

        $jobDescription->update([
            'is_active' => 0
        ]);

        $this->recordActivity(
             1,
             'Deleted Cv Job Description Name',
             $jobDescription,
             $id,
             'hrm_cv_job_description'
        );

        return back();
    }
}
