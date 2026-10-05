<!-- create_employee -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<style type="text/css">
	/*body {padding:3%;background:#fff}*/

	img{
		width: 210px;
		height: 210px;
	}

	input[type=file]{
		padding:10px;
		/*background:#2d2d2d;*/
	}

	.bold-off {
	    font-weight: normal !important;
	}
	.panel-heading {
	    padding: 0
	}
	.panel-heading a {
	    display: block;
	    padding: 20px 10px;
	}
	.panel-heading a.collapsed {
	    background: #fff
	}
	.panel-heading a {
	    background: #f7f7f7;
	    border-radius: 5px;
	}
	.panel-heading a:after {
	    content: '-'
	}
	.panel-heading a.collapsed:after {
	    content: '+'
	}
	.nav.nav-tabs li a,
	.nav.nav-tabs li.active > a:hover,
	.nav.nav-tabs li.active > a:active,
	.nav.nav-tabs li.active > a:focus {
	    border-bottom-width: 0px;
	    outline: none;
	}
	.nav.nav-tabs li a {
	    padding-top: 20px;
	    padding-bottom: 20px;
	}
	.tab-pane {
	    background: #fff;
	    padding: 10px;
	    border: 1px solid #ddd;
	    margin-top: -1px;
	}

	/* used for sidebar tab/collapse*/
	@media (max-width: 991px) {
	  .visible-tabs {
	    display: none;
	  }
	}

	@media (min-width: 992px) {
	  .visible-tabs {
	    display: block !important;
	  }
	}

	@media (min-width: 992px) {
	  .hidden-tabs {
	    display: none !important;
	  }
	}

</style>



@endsection
@section('content')

<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee Information</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
    </div>



	<ul class="nav nav-tabs sidebar-tabs" id="sidebar" role="tablist">
		<li class="active"><a href="#tab-1" role="tab" data-toggle="tab">Personal Information</a></li>
		<li><a href="#tab-2" role="tab" data-toggle="tab">Official Information</a></li>
    </ul> <!--/.nav-tabs.sidebar-tabs -->

    <div class="tab-content">
        <div class="tab-pane active" id="tab-1">
            <form  method="POST" action="{{ url('modify_employeeinfo') }}" id="frm_money_receipt" enctype="multipart/form-data" >
                {{ csrf_field() }}
                <div class="row">

                    <input type="text" id="employee_id" name="employee_id" value="{{$employee->id}}" class="hidden">
                    <div class="col-lg-8 col-md-8 col-xs-12 personal-info">
                        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Employee Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name.." value="{{$employee->employee_name}}" required autofocus >
                                @if ($errors->has('employee_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('employee_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Nickname</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname.." value="{{$employee->nickname}}" required autofocus >
                                @if ($errors->has('nickname'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nickname') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('present_address') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Present Address</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="present_address" placeholder="Present Address.." value="{{ $employee->present_address ?? old('present_address') }}" required autofocus >
                                @if ($errors->has('present_address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('present_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('permanent_address') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Permanent Address</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="permanent_address" placeholder="Permanent Address.." value="{{ $employee->permanent_address ?? old('permanent_address') }}" required autofocus >
                                @if ($errors->has('permanent_address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('permanent_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('contact_number') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Contact Number</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="contact_number" placeholder="Contact Number.." value="{{ $employee->contact_number ?? old('contact_number') }}" required >
                                @if ($errors->has('contact_number'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Email</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="email" placeholder="Email.." value="{{ $employee->email ?? old('email') }}" required >
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('gender') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Gender</label>
                            <div class="col-lg-9">

                                <select class="form-control" id="gender" name="gender" style="width: 100%;" required >
                                    @if ($employee->gender == 1)
                                    <option value="1" selected>Male</option>
                                    <option value="2">Female</option>
                                    @else
                                    <option value="1">Male</option>
                                    <option value="2" selected>Female</option>
                                    @endif
                                </select>

                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('religion') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Religion</label>

                            <div class="col-lg-9">
                                <select class="form-control" id="religion" name="religion" style="width: 100%;" required >
                                    <option value="{{$editemployee[0]->religion_id}}">{{$editemployee[0]->religion}}</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('marital_status') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Marital Status</label>

                            <div class="col-lg-9">
                                <select class="form-control" id="marital_status" name="marital_status" style="width: 100%;" required >
                                    <option value="{{$editemployee[0]->marital_status_id}}">{{$editemployee[0]->marital_status}}</option>
                                </select>

                            </div>

                        </div>
                        <div class="form-group has-feedback {{ $errors->has('blood_group') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Blood Group</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="blood_group" name="blood_group" style="width: 100%;" required >
                                    <option value="{{$editemployee[0]->blood_group_id}}">{{$editemployee[0]->blood_group}}</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('date_of_birth') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Date of Birth</label>
                            <div class="col-lg-9">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <!-- <input type="text" class="form-control pull-right" id="date_of_birth" name="date_of_birth" data-date-format="dd-mm-yyyy"  value="{{  date('d-m-Y', strtotime(str_replace('-', '/', $employee->dob ))) }}"  required readonly> -->
                                    <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $employee->dob ))) }}" >
                                    @if ($errors->has('date_of_birth'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('date_of_birth') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('nid') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">NID / Smart Card</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="nid" placeholder="NID/Smart Card.." value="{{ $employee->nid ?? old('nid') }}" >
                                @if ($errors->has('nid'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nid') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('tin') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">TIN</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="tin" placeholder="TIN" value="{{ $employee->tin ?? old('tin') }}" >
                                @if ($errors->has('tin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tin') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group has-feedback {{ $errors->has('father_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Father's Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="father_name" placeholder="Father's Name.." value="{{ $employee->father_name ?? old('father_name') }}" >
                                @if ($errors->has('father_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('father_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('mothers_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Mother's Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name.." value="{{ $employee->mother_name ?? old('mothers_name') }}" >
                                @if ($errors->has('mothers_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mothers_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('last_education') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Highest Education</label>
                            <div class="col-lg-9">
                                <select class="form-control select2" id="education_name" name="hrm_education_id" style="width: 100%;" >
                                    <option value="{{$editemployee[0]->hrm_education_id}}">{{$editemployee[0]->education_name}}</option>
                                </select>
                                @if ($errors->has('last_education'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_education') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-xs-12 text-center">
                        <img  id="blah" src="{{asset('employee_image/'.$employee->Images)}}" alt="your image" />
                        <input  type='file' name="images" value="{{$employee->Images}}" onchange="readURL(this);" />
                    </div>

                    <input type="hidden" name="emp_info" value="1">

                    <div class="col-lg-12 col-md-12 col-xs-12 form-group">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <div class="tab-pane" id="tab-2">
            <form  method="POST" action="{{ url('modify_employeeinfo') }}" id="frm_employee_info">
                {{ csrf_field() }}
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <input type="text" id="employee_id" name="employee_id" value="{{$employee->id}}" class="hidden">

                            <input type="text" id="hrm_employee_job_info_id" name="hrm_employee_job_info_id" value="{{$editemployee[0]->hrm_employee_job_info_id}}"" class="hidden">

                            <input type="hidden" name="emp_info" value="2">
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('employee_code') ? ' has-error' : '' }}">
                                <label class="control-label">Employee Code</label>
                                <input type="text" class="form-control" name="employee_code" placeholder="Employee Code" value="{{ $editemployee[0]->employee_code ?? old('employee_code') }}" required  >
                                @if ($errors->has('employee_code'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('employee_code') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('official_contact_no') ? ' has-error' : '' }}">
                                <label class="control-label">Official Contact No</label>
                                <input type="text" class="form-control" name="official_contact_no" placeholder="Official Contact" value="{{ $editemployee[0]->official_contact_no ?? old('official_contact_no') }}" />
                                @if ($errors->has('official_contact_no'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('official_contact_no') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('official_email') ? ' has-error' : '' }}">
                                <label class="control-label">Official Email</label>
                                <input type="text" class="form-control" name="official_email" placeholder="Official Email" value="{{ $editemployee[0]->official_email ?? old('official_email') }}" />

                                @if ($errors->has('official_email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('official_email') }}</strong>
                                    </span>
                                @endif
                            </div>


                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('depertment') ? ' has-error' : '' }}">
                                <label class="control-label">Department</label>
                                <select class="form-control select2" id="depertment" name="depertment" style="width: 100%;" >
                                    <option value="{{$editemployee[0]->department_id}}">{{$editemployee[0]->depertment_name}}</option>
                                </select>
                                @if ($errors->has('depertment'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('depertment') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }}">
                                <label class="control-label">Designation</label>
                                <select class="form-control" id="designation" name="designation" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->designation_id}}">{{$editemployee[0]->designation_name}}</option>
                                </select>
                                @if ($errors->has('designation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('designation') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('category') ? ' has-error' : '' }}">
                                <label class="control-label">Employee Category</label>
                                <select class="form-control" id="category" name="category" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->category_id}}">{{$editemployee[0]->category_name}}</option>

                                </select>
                                @if ($errors->has('category'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('category') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('jod_location') ? ' has-error' : '' }}">
                                <label class="control-label">Job Placement</label>
                                <select class="form-control" id="jod_location" name="jod_location" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->location_id}}">{{$editemployee[0]->location_name}}</option>

                                </select>
                                @if ($errors->has('jod_location'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('jod_location') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('section') ? ' has-error' : '' }}">
                                <label class="control-label">Sub-department</label>
                                <select class="form-control" id="section" name="section" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->sectionid}}">{{$editemployee[0]->section_name}}</option>
                                </select>
                                @if ($errors->has('section'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('section') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('working_shift') ? ' has-error' : '' }}">
                                <label class="control-label">Working Shift</label>
                                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->shift_id}}">{{$editemployee[0]->shift_name}}</option>
                                </select>
                                @if ($errors->has('working_shift'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('working_shift') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('overtime') ? ' has-error' : '' }}">
                                <label class="control-label">Overtime</label>
                                <select class="form-control" name="overtime" style="width: 100%;" required>

                                    @if ($editemployee[0]->overtime_status ?? old('overtime_status') == 1)
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                    @endif
                                </select>
                                @if ($errors->has('overtime'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('overtime') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('insurance') ? ' has-error' : '' }}">
                                <label class="control-label">Insurance</label>
                                <select class="form-control" name="insurance" style="width: 100%;" required>

                                    @if ($editemployee[0]->insurance ?? old('insurance') == 1)
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                    @endif
                                </select>
                                @if ($errors->has('insurance'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('insurance') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('plant_name') ? ' has-error' : '' }}">
                                <label class="control-label">Plant/Work Place</label>
                                <select class="form-control" id="plant_name" name="plant_name" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->hrm_plant_id}}">{{$editemployee[0]->plant_name}}</option>
                                </select>
                                @if ($errors->has('plant_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('plant_name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('manage_by') ? ' has-error' : '' }}">
                                <label class="control-label">Reporting to</label>
                                <select class="form-control" id="manage_by" name="manage_by" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->manage_by_id}}">{{$editemployee[0]->manage_by_name}}</option>
                                </select>
                                @if ($errors->has('manage_by'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('manage_by') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('basic_salary') ? ' has-error' : '' }}">
                                <label class="control-label">Gross Salary</label>
                                <input type="text" class="form-control" name="basic_salary" placeholder="Basic Salary.." value="{{ $editemployee[0]->basic_salary ?? old('basic_salary') }}" required  >
                                @if ($errors->has('basic_salary'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('basic_salary') }}</strong>
                                </span>
                                @endif
                            </div>

                            @if (Config::get('module_config.payroll_module') == 1)
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('salary_grade') ? ' has-error' : '' }}">
                                <label class="control-label">Salary Grade</label>
                                <select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;" required>
                                    <option value="{{$employee_salary_grade[0]->id}}">{{$employee_salary_grade[0]->grade_name}}</option>
                                </select>
                                @if ($errors->has('salary_grade'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('salary_grade') }}</strong>
                                </span>
                                @endif
                            </div>
                            @endif

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('joining_date') ? ' has-error' : '' }}">
                                <label class="control-label">Joining Date</label>

                                    <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $editemployee[0]->joining_date ))) }}" >

                                @if ($errors->has('joining_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('joining_date') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('employeestatus') ? ' has-error' : '' }}">
                                <label class="control-label">Employee Status</label>
                                <select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;" required>
                                    <option value="{{$editemployee[0]->employeestatus_id}}">{{$editemployee[0]->employeestatus_name}}</option>
                                </select>
                                @if ($errors->has('employeestatus'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('employeestatus') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group probation has-feedback {{ $errors->has('probation_period') ? ' has-error' : '' }}">
                                <label class="control-label">Probation Period (Months)</label>
                                <select class="form-control" id="probation_period" name="probation_period" style="width: 100%;" required>
                                    @foreach ($probation as $probation)
                                        @if($editemployee[0]->hrm_probation_period_id==$probation->id)
                                            <option value="{{$probation->id}}" selected>{{$probation->period}}</option>
                                        @else
                                            <option value="{{$probation->id}}" > {{$probation->period}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group confirm has-feedback {{ $errors->has('confirmation_date') ? ' has-error' : '' }}">
                                <label class="control-label">Confirmation Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" id="confirmation_date" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $editemployee[0]->confirmation_date ))) }}" >
                                    @if ($errors->has('confirmation_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('confirmation_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>

                    <div class="col-lg-12 col-md-12 col-xs-12 form-group">




                        <div class="col-lg-9 col-md-9 col-xs-12">


                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Submit</button>
                        </div>
                    </div>
                    </div>
                </div>
                                                                    <div id="alert-danger"></div>
            <div id="alert-success"></div>
            </form>

        </div>


    </div>
@endsection
@section('script')

<script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
<script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script>
  $(function () {
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();

  });
// $(document).ready(function($) {
// $('#date_of_birth').datepicker({
// autoclose: true
// });
// });
</script>
<script type="text/javascript">

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#blah')
			.attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function openCity(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}


    // $('#joining_date').datepicker({
    //   autoclose: true
    // });
    // $('#confirmation_date').datepicker({
    //   autoclose: true
    // });

    $('#depertment').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


    $('#religion').select2({
      placeholder: 'Enter Religion',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/religion_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#marital_status').select2({
      placeholder: 'Enter Marital Status',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/maritalstatus_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

$('#blood_group').select2({
      placeholder: 'Enter Blood Group',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/blood_group_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

$('#plant_name').select2({
      placeholder: 'Enter Plant/Work Place Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/plantname_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });




    $('#designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#category').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/category_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#working_shift').select2({
      placeholder: 'Enter working shift',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shift_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#manage_by').select2({
      placeholder: 'Enter manage by',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employee_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


	$('#salary_grade').select2({
	      placeholder: 'Enter a Salary Grade',
	      allowClear: true,
	        ajax: {
	            dataType: 'json',
	            url: '{{URL::to('/')}}/salarygrade_list?status=1',
	            delay: 250,
	          data: function(params) {
	              return {
	                term: params.term
	              }
	          },
	            processResults: function (data, params) {
	              params.page = params.page || 1;
	              return {
	                results: data,
	                pagination: {
	                  more: (params.page * 30) < data.total_count
	                }
	              };
	            },
	            cache: true
	        }
	  });





    $('#employeestatus').select2({
      placeholder: 'Enter employee status',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employeestatus_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#jod_location').select2({
      placeholder: 'Enter jod location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


    $('#education_name').select2({
      placeholder: 'Enter Education Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/education_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });



    $('#section').select2({
      placeholder: 'Enter Sub-department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/section_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });



    $( "#frm_money_receipt" ).submit(function(event){
      	event.preventDefault();

        if(confirm('Do you want to submit?')){
          var formData   = new FormData(this),
          url         = $(this).attr( "action" );
          token       = $("[name='_token']").val();
          $.ajax({
                type:'POST',
                url: $(this).attr('action'),
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
          })
          .done(function(data) {
              console.log(data);
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);
                $('#alert-success').show(0).delay(1000).hide(0);
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);
                  $('#alert-danger').show(0).delay(1000).hide(0);
              }
          });
        }else{
          return;
        }
    });




    $( "#frm_employee_info" ).submit(function(event){
      	event.preventDefault();

        if(confirm('Do you want to submit?')){
          var $form   = $( this ),
          url         = $form.attr( "action" );
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : url, // the url where we want to POST
            data        : $form.serialize(),
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true,
            _token      : token
          })
          .done(function(data) {
              console.log(data);
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);
                $('#alert-success').show(0).delay(1000).hide(0);
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);
                  $('#alert-danger').show(0).delay(1000).hide(0);
              }
          });
        }else{
          return;
        }
    });



</script>


<script>
$(document).ready(function($) {

    $('.probation').hide();
    $('.confirm').hide();



	if($("#employeestatus").val() == 1){
	    $('.probation').show();
	    $('.confirm').hide();

	}else{
	    $('.confirm').show();
	    $('.probation').hide();

	}



	$('#employeestatus').on('change', function(){

			if($("#employeestatus").val() == 1){
			    $('.probation').show();
			    $('.confirm').hide();

			}else{
			    $('.confirm').show();
			    $('.probation').hide();

			}

			if($("#employeestatus").val() == null){
		 		$('.probation').hide();
		    	$('.confirm').hide();
			}

	});
	});

</script>
@endsection
