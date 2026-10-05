<?php

namespace App\Imports;

use App\Models\HrmEmployee;
use App\Models\HrmReligion;
use App\Models\HrmBloodGroup;
use App\Models\HrmMaritalStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmployeeImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $i = 0;

        foreach ($rows->toArray() as $row) {

            if ($i === 0) {
                if($row[0] !== 'employee_name') {
                    dd("Sorry Column employee_name Problem");
                }

                if($row[1] !== 'nick_name') {
                    dd("Sorry Column nick_name Problem");
                }

                if($row[2] !== 'contact_number') {
                    dd("Sorry Column contact_number Problem");
                }

                if($row[3] !== 'father_name') {
                    dd("Sorry Column father_name Problem");
                }

                if($row[4] !== 'mother_name') {
                    dd("Sorry Column mother_name Problem");
                }

                if($row[5] !== 'dateof_birth') {
                    dd("Sorry Column dateof_birth Problem");
                }

                if($row[6] !== 'email') {
                    dd("Sorry Column email Problem");
                }

                if($row[7] !== 'nid') {
                    dd("Sorry Column nid Problem");
                }

                if($row[8] !== 'tin') {
                    dd("Sorry Column tin Problem");
                }

                if($row[9] !== 'passport') {
                    dd("Sorry Column passport Problem");
                }

                if($row[10] !== 'present_address') {
                    dd("Sorry Column present_address Problem");
                }

                if($row[11] !== 'permanent_address') {
                    dd("Sorry Column permanent_address Problem");
                }

                if($row[12] !== 'gender') {
                    dd("Sorry Column gender Problem");
                }

                if($row[13] !== 'blood_group') {
                    dd("Sorry Column blood_group Problem");
                }

                if($row[14] !== 'religion') {
                    dd("Sorry Column religion Problem");
                }

                if($row[15] !== 'marital_status') {
                    dd("Sorry Column marital_status Problem");
                }
            }

            else {
                if($row[0] == null) {
                    $employee_name = 'N/A';
                } else {
                    $employee_name = $row[0];
                }

                if($row[1] == null) {
                    $nickname = 'N/A';
                } else {
                    $nickname = $row[1];
                }

                if($row[2] == null) {
                    $contact_number = 'N/A';
                } else {
                    $contact_number = $row[2];
                }

                if($row[3] == null) {
                    $father_name = 'N/A';
                } else {
                    $father_name = $row[3];
                }


                if($row[4] == null) {
                    $mother_name = 'N/A';
                } else {
                    $mother_name = $row[4];
                }

                if ($row[5] == null) {
                    $dateofbirth = null;
                } else {
                    $dateofbirth = $row[5];
                }

                if( $row[6] == null) {
                    $email = 'N/A';
                } else {
                    $email = $row[6];
                }

                if($row[7] == null) {
                    $nid = 'N/A';
                } else {
                    $nid = $row[7];
                }

                if($row[8] == null) {
                    $tin = 'N/A';
                } else {
                    $tin = $row[8];
                }

                if($row[9] == null) {
                    $passport = 'N/A';
                } else {
                    $passport = $row[9];
                }

                if($row[10] == null) {
                    $present_address = 'N/A';
                } else {
                    $present_address = $row[10];
                }

                if ($row[11] == null) {
                    $permanent_address = 'N/A';
                } else {
                    $permanent_address = $row[11];
                }

                //check for Gender
                if($row[12] === 'Female') {
                    $gender = 2;
                } else {
                    $gender = 1;
                }

                //check for blood Group
                $blood_group_id = 0;
                $blood_group = HrmBloodGroup::where('blood_group', $row[13])->first();

                if (empty($blood_group)) {
                    $insert_bloodgroup = new HrmBloodGroup;
                    $insert_bloodgroup->blood_group = $row[13];
                    $insert_bloodgroup->save();

                    $blood_group_id = $insert_bloodgroup->id;
                } else {
                    $blood_group_id = $blood_group->id;
                }

                //check for Religion
                $religion_id = 0;
                $religion = HrmReligion::where('religion', $row[14])->first();

                if (empty($religion)) {
                    $insert_religion = new HrmReligion;
                    $insert_religion->religion = $row[14];
                    $insert_religion->save();

                    $religion_id = $insert_religion->id;
                } else {
                    $religion_id = $religion->id;
                }

                //check for marital_status
                $marital_status_id  = 0;
                $marital_status = HrmMaritalStatus::where('marital_status', $row[15])->first();

                if (empty($marital_status)) {
                    $insert_marital_status = new HrmMaritalStatus;
                    $insert_marital_status->marital_status = $row[15];
                    $insert_marital_status->save();

                    $marital_status_id = $insert_marital_status->id;
                } else {
                    $marital_status_id = $marital_status->id;
                }

                $dob = date('Y-m-d', strtotime(str_replace('/', '-', $dateofbirth)));

                $insert[] = [
                    'employee_name'        => $employee_name,
                    'nickname'             => $nickname,
                    'contact_number'       => $contact_number,
                    'father_name'          => $father_name,
                    'mother_name'          => $mother_name,
                    'dob'                  => $dob,
                    'email'                => $email,
                    'nid'                  => $nid,
                    'tin'                  => $tin,
                    'passport'             => $passport,
                    'present_address'      => $present_address,
                    'permanent_address'    => $permanent_address,
                    'gender'               => $gender,
                    'hrm_blood_group_id'   => $blood_group_id,
                    'hrm_religion_id'      => $religion_id,
                    'hrm_marital_status_id'=> $marital_status_id,
                    'active_status'        => 1,
                    'hrm_education_id'     => 1,
                    'users_id'             => Auth::user()->id,
                    'Images'               => ''
                ];
            }

            $i++;
        }

        if(!empty($insert)) {
            return HrmEmployee::insert($insert);
        }
    }
}

