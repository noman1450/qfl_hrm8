<!-- create_employee -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<style type="text/css">
	.kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
        text-align: center;
    }
    .kv-avatar {
        display: inline-block;
    }
    .kv-avatar .file-input {
        display: table-cell;
        width: 213px;
    }
    .kv-reqd {
        color: red;
        font-family: monospace;
        font-weight: normal;
    }
    .btn-secondary {
        margin-top: 5px;
    }
    .btn-file{
        margin-top: 5px;
    }

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
    /* .nav-tabs li.disabled a {
        pointer-events: none;
    } */
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
		<li class="active">
            <a href="#tab-1" role="tab" data-toggle="tab">Personal Information</a>
        </li>
		<li class="">
            <a href="#tab-2" role="tab" data-toggle="tab">Official Information</a>
        </li>
		<li class="">
            <a href="#tab-3" role="tab" data-toggle="tab">Card Info</a>
        </li>
        <li class="">
            <a href="#tab-4" role="tab" data-toggle="tab">User Credential</a>
        </li>
        <li class="">
            <a href="#tab-5" role="tab" data-toggle="tab">Final</a>
        </li>
    </ul> <!--/.nav-tabs.sidebar-tabs -->


    <div>
        <div id="alert-danger"></div>
        <div id="alert-success"></div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-1">
            <form  method="POST" action="{{ route('add_new_employee.update', $employee->id) }}" id="step-one" enctype="multipart/form-data">
                {{ csrf_field() }}
                @method('patch')
                <div class="row">
                    <input type="hidden" name="step" value="one">

                    <div class="col-lg-8 col-md-8 col-xs-12 personal-info">
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Employee Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name.." value="{{ $employee->employee_name }}">
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Nickname</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname.." value="{{$employee->nickname}}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Present Address</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="present_address" placeholder="Present Address.." value="{{ old('present_address', $employee->present_address) }}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Permanent Address</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="permanent_address" placeholder="Permanent Address.." value="{{ old('permanent_address', $employee->permanent_address) }}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Contact Number</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="contact_number" placeholder="Contact Number.." value="{{ old('contact_number', $employee->contact_number) }}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Email</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="email" placeholder="Email.." value="{{ old('email', $employee->email) }}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Gender</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="gender" name="gender" style="width: 100%;">
                                    <option value="1" {{ $employee->gender == 1 ? 'selected' : '' }}>Male</option>
                                    <option value="2" {{ $employee->gender == 2 ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Religion</label>

                            <div class="col-lg-9">
                                <select class="form-control" id="religion" name="religion" style="width: 100%;">
                                    <option value="{{ $employee_info->religion_id }}">{{ $employee_info->religion }}</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Marital Status</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="marital_status" name="marital_status" style="width: 100%;">
                                    <option value="{{ $employee_info->marital_status_id }}">{{ $employee_info->marital_status }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Blood Group</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="blood_group" name="blood_group" style="width: 100%;">
                                    <option value="{{ $employee_info->blood_group_id }}">{{ $employee_info->blood_group }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Date of Birth</label>
                            <div class="col-lg-9">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    @if ($employee->dob)
                                        <input type="text" class="form-control pull-right" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="{{ date('d-m-Y', strtotime($employee->dob) ) }}">
                                    @else
                                        <input type="text" class="form-control pull-right" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">NID / Smart Card</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="nid" placeholder="NID/Smart Card.." value="{{ old('nid', $employee->nid) }}" >
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">TIN</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="tin" placeholder="TIN" value="{{ old('tin', $employee->tin) }}" >
                            </div>
                        </div>


                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Father's Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="father_name" placeholder="Father's Name.." value="{{ old('father_name', $employee->father_name) }}">
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Mother's Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name.." value="{{ old('mothers_name', $employee->mother_name) }}" >
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Highest Education</label>
                            <div class="col-lg-9">
                                <select class="form-control select2" id="education_name" name="hrm_education_id" style="width: 100%;" >
                                    <option value="{{ $employee_info->hrm_education_id }}">{{ $employee_info->education_name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-xs-12 text-center">
                        <div class="kv-avatar">
                            <div class="file-loading">
                                <input id="avatar-1" name="images" type="file">
                            </div>
                        </div>

                        <div class="kv-avatar-hint"><small>Select file < 1500 KB</small></div>
                    </div>

                    <div class="col-lg-8 col-md-8 col-xs-12 form-group">
                        <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Next</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-2">
            <form  method="POST" action="{{ route('add_new_employee.update', $employee->id) }}" id="step-two">
                {{ csrf_field() }}
                @method('patch')
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <input type="text" id="step_two_employee_id" name="employee_id" class="hidden">

                            <input type="hidden" name="step" value="two">
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Employee Code</label>
                                <input type="text" class="form-control" name="employee_code" placeholder="Employee Code" value="{{ $employee_info->employee_code }}" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Department</label>
                                <select class="form-control select2" id="depertment" name="depertment" style="width: 100%;" >
                                    <option value="{{ $employee_info->department_id}}">{{ $employee_info->depertment_name }}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Designation</label>
                                <select class="form-control" id="designation" name="designation" style="width: 100%;">
                                    <option value="{{$employee_info->designation_id}}">{{$employee_info->designation_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Employee Category</label>
                                <select class="form-control" id="category" name="category" style="width: 100%;">
                                    <option value="{{$employee_info->category_id}}">{{$employee_info->category_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Job Placement</label>
                                <select class="form-control" id="job_location" name="job_location" style="width: 100%;">
                                    <option value="{{$employee_info->location_id}}">{{$employee_info->location_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Sub-department</label>
                                <select class="form-control" id="section" name="section" style="width: 100%;">
                                    <option value="{{ $employee_info->sectionid }}">{{$employee_info->section_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Working Shift</label>
                                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;">
                                    <option value="{{$employee_info->working_shift_id}}">{{$employee_info->shift_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Overtime</label>
                                <select class="form-control" name="overtime" style="width: 100%;">
                                    <option value="1" {{ $employee_info->overtime_status == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $employee_info->overtime_status == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Insurance</label>
                                <select class="form-control" name="insurance" style="width: 100%;">
                                    <option value="1" {{ $employee_info->insurance == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $employee_info->insurance == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Plant/Work Place</label>
                                <select class="form-control" id="plant_name" name="plant_name" style="width: 100%;">
                                    <option value="{{$employee_info->hrm_plant_id}}">{{$employee_info->plant_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Reporting to</label>
                                <select class="form-control" id="manage_by" name="manage_by" style="width: 100%;">
                                    <option value="{{$employee_info->manage_by_id}}">{{$employee_info->manage_by_name}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Gross Salary</label>
                                <input type="text" class="form-control" name="basic_salary" placeholder="Basic Salary.." value="{{ $employee_info->basic_salary }}" required>
                            </div>

                            @if (config('module_config.payroll_module') == 1)
                                <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                    <label control-label">Salary Grade</label>
                                    <select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;">
                                        <option value="{{$employee_info->salary_grade_id}}">{{$employee_info->grade_name}}</option>
                                    </select>
                                </div>
                            @endif

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label control-label">Joining Date</label>
                                @if ($employee_info->joining_date)
                                    <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime( $employee_info->joining_date )) }}">
                                @else
                                    <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                <label class="control-label">Employee Status</label>
                                <select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;">
                                    <option value="{{$employee_info->employeestatus_id}}">{{$employee_info->employeestatus_name}}</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group probation">
                                <label>Probation Period (Months)</label>
                                <select class="form-control" id="probation_period" name="probation_period" style="width: 100%;">
                                    @foreach ($probations as $probation)
                                        <option value="{{ $probation->id }}">{{ $probation->period }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-xs-12 form-group confirm">
                                <label control-label">Confirmation Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>

                                    @if ($employee_info->confirmation_date)
                                        <input type="text" class="form-control" id="confirmation_date" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime( $employee_info->confirmation_date )) }}">
                                    @else
                                    <input type="text" class="form-control" id="confirmation_date" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-xs-12 form-group">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <button type="button" class="btn btn-primary block btn-flat btn prev-btn">Prev</button>
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Next</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-3">
            <form  method="POST" action="{{ route('add_new_employee.update', $employee->id) }}" id="step-three">
                {{ csrf_field() }}
                @method('patch')
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="step_three_employee_id" name="employee_id" class="hidden">

                            <input type="hidden" name="step" value="three">

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Card Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="card_code" placeholder="card code.." value="{{ $employee_info->card_code }}" required>
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Old Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="old_code" placeholder="Old Code.." value="{{ $employee_info->old_code }}">
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Device Id</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="device_id" placeholder="Device Id.." value="{{ $employee_info->device_id }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-xs-12 form-group">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <button type="button" class="btn btn-primary block btn-flat btn prev-btn">Prev</button>
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Next</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-4">
            <form  method="POST" action="{{ route('add_new_employee.update', $employee->id) }}" id="step-three">
                {{ csrf_field() }}
                @method('patch')
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="step_three_employee_id" name="employee_id" class="hidden">

                            <input type="hidden" name="step" value="three">

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Card Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="card_code" placeholder="card code.." value="{{ $employee_info->card_code }}" required>
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Old Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="old_code" placeholder="Old Code.." value="{{ $employee_info->old_code }}">
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Device Id</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="device_id" placeholder="Device Id.." value="{{ $employee_info->device_id }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-xs-12 form-group">
                        <div class="col-lg-9 col-md-9 col-xs-12">
                            <button type="button" class="btn btn-primary block btn-flat btn prev-btn">Prev</button>
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Next</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-5">
            <form  method="POST" action="{{ route('add_new_employee.update', $employee->id) }}" id="step-four">
                {{ csrf_field() }}
                @method('patch')

                <div class="box-body tabcontent" id="jobinfo">
                    <input type="hidden" name="step" value="final">

                    <div class="row">
                        <div class="col-md-4">
                            <div>
                                <h3 class="text-info" style="background-color:#ddd;width:max-content;padding:5px;border-radius:5px">Personal Information</h3>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $employee->employee_name }}</td>
                                </tr>
                                <tr>
                                    <th>Nickname</th>
                                    <td>{{ $employee->nickname }}</td>
                                </tr>
                                <tr>
                                    <th>Present Address</th>
                                    <td>{{ $employee->present_address }}</td>
                                </tr>
                                <tr>
                                    <th>Permanent Address</th>
                                    <td>{{ $employee->permanent_address }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $employee->contact_number }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $employee->email }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $employee->gender == 1 ? 'Male' : 'Female' }}</td>
                                </tr>
                                <tr>
                                    <th>Religion</th>
                                    <td>{{ $employee_info->religion }}</td>
                                </tr>
                                <tr>
                                    <th>Marital Status</th>
                                    <td>{{ $employee_info->marital_status }}</td>
                                </tr>
                                <tr>
                                    <th>Blood Group</th>
                                    <td>{{ $employee_info->blood_group }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>
                                        @if ($employee->dob)
                                            {{ date('d-m-Y', strtotime($employee->dob) ) }}
                                        @else
                                            Not Given
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>NID / Smart Card</th>
                                    <td>{{ $employee->nid }}</td>
                                </tr>
                                <tr>
                                    <th>TIN</th>
                                    <td>{{ $employee->tin }}</td>
                                </tr>
                                <tr>
                                    <th>Father's Name</th>
                                    <td>{{ $employee->father_name }}</td>
                                </tr>
                                <tr>
                                    <th>Mother's Name</th>
                                    <td>{{ $employee->mother_name }}</td>
                                </tr>
                                <tr>
                                    <th>Highest Education</th>
                                    <td>{{ $employee_info->education_name }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <div>
                                <h3 class="text-info" style="background-color:#ddd;width:max-content;padding:5px;border-radius:5px">Official Information</h3>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th>Emoloyee Code</th>
                                    <td>{{ $employee_info->employee_code }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $employee_info->depertment_name }}</td>
                                </tr>
                                <tr>
                                    <th>Designation</th>
                                    <td>{{ $employee_info->designation_name }}</td>
                                </tr>
                                <tr>
                                    <th>Employee Category</th>
                                    <td>{{ $employee_info->category_name }}</td>
                                </tr>
                                <tr>
                                    <th>Job Placement</th>
                                    <td>{{ $employee_info->location_name }}</td>
                                </tr>
                                <tr>
                                    <th>Sub-department</th>
                                    <td>{{ $employee_info->section_name }}</td>
                                </tr>
                                <tr>
                                    <th>Working Shift</th>
                                    <td>{{ $employee_info->shift_name }}</td>
                                </tr>
                                <tr>
                                    <th>Overtime</th>
                                    <td>{{ $employee_info->overtime_status == 1 ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Insurance</th>
                                    <td>{{ $employee_info->insurance == 1 ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Plant/Work Place</th>
                                    <td>{{ $employee_info->plant_name }}</td>
                                </tr>
                                <tr>
                                    <th>Reporting to</th>
                                    <td>{{ $employee_info->manage_by_name }}</td>
                                </tr>
                                <tr>
                                    <th>Gross Salary</th>
                                    <td>{{ $employee_info->basic_salary }}</td>
                                </tr>
                                <tr>
                                    <th>Salary Grade</th>
                                    <td>{{ $employee_info->grade_name }}</td>
                                </tr>
                                <tr>
                                    <th>Joining Date</th>
                                    <td>
                                        @if ($employee_info->joining_date)
                                            {{ date('d-m-Y', strtotime($employee_info->joining_date) ) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Employee Status</th>
                                    <td>{{ $employee_info->employeestatus_name }}</td>
                                </tr>
                                <tr>
                                    <th>Probation Period (Months)</th>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <th>Confirmation Date</th>
                                    <td>
                                        @if ($employee_info->confirmation_date)
                                            {{ date('d-m-Y', strtotime($employee_info->confirmation_date) ) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <div>
                                <h3 class="text-info" style="background-color:#ddd;width:max-content;padding:5px;border-radius:5px">Card Information</h3>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th>Card Code</th>
                                    <td>{{ $employee_info->card_code }}</td>
                                </tr>
                                <tr>
                                    <th>Old Code</th>
                                    <td>{{ $employee_info->old_code }}</td>
                                </tr>
                                <tr>
                                    <th>Device Id</th>
                                    <td>{{ $employee_info->device_id }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row form-group">
                        <div class="col-xs-12">
                            <button type="button" class="btn btn-primary block btn-flat btn prev-btn">Prev</button>
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
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

    $('.prev-btn').click(function(e) {
        e.preventDefault();

        $('.nav-tabs li.active').prev('li').find('a').attr('data-toggle', 'tab').tab('show');
    })


    $("#step-one").submit(function(event) {
      	event.preventDefault();

        if(confirm('Do you want to submit?')) {
            var $form   = new FormData(this),
            url         = this.action;
            token       = $("[name='_token']").val();

            $.ajax({
                type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url         : url, // the url where we want to POST
                data        : $form,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',

                success: function(data) {
                    console.log(data);

                    if(data.status === true) {
                        var erreurs ='<div class="alert alert-success">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        var iid = $('#step_two_employee_id').val(data.employee_draft_id);

                        $('#alert-success').html(erreurs);

                        $('#alert-success').show(0).delay(1500).hide(0);

                        $('.nav-tabs li.active').next('li').find('a').attr('data-toggle', 'tab').tab('show');
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show();
                    }
                },

                error: function (data) {
                    console.log(data.responseJSON.errors);

                    var errors = data.responseJSON.errors;

                    var erreurs = '<div class="alert alert-danger"><ul>';

                    $.each(errors, function(i, error) {
                        erreurs += '<li>'+error+'</li>';
                    });

                    erreurs += '</ul></div>';
                    $('#alert-danger').html(erreurs);
                    $('#alert-danger').show();
                }
            })
        }else{
          return;
        }
    });

    $("#step-two").submit(function(event) {
      	event.preventDefault();

        if(confirm('Do you want to submit?')) {
            var $form   = $( this ),
            url         = $form.attr("action");
            token       = $("[name='_token']").val();
            $.ajax({
                type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url         : url, // the url where we want to POST
                data        : $form.serialize(),
                dataType    : 'json', // what type of data do we expect back from the server
                encode      : true,
                _token      : token,

                success: function(data) {
                    console.log(data);
                    if(data.status === true) {
                        var erreurs ='<div class="alert alert-success">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-success').html(erreurs);

                        $('#alert-success').show(0).delay(1500).hide(0);

                        $('.nav-tabs li.active').next('li').find('a').attr('data-toggle', 'tab').tab('show');
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show();
                    }
                },

                error: function (data) {
                    console.log(data.responseJSON.errors);

                    var errors = data.responseJSON.errors;

                    var erreurs = '<div class="alert alert-danger"><ul>';

                    $.each(errors, function(i, error) {
                        erreurs += '<li>'+error+'</li>';
                    });

                    erreurs += '</ul></div>';
                    $('#alert-danger').html(erreurs);
                    $('#alert-danger').show();
                }
            })
        } else {
          return;
        }
    });

    $("#step-three").submit(function(event) {
      	event.preventDefault();

        if(confirm('Do you want to submit?')) {
            var $form   = $( this ),
            url         = $form.attr("action");
            token       = $("[name='_token']").val();
            $.ajax({
                type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url         : url, // the url where we want to POST
                data        : $form.serialize(),
                dataType    : 'json', // what type of data do we expect back from the server
                encode      : true,
                _token      : token,

                success: function(data) {
                    console.log(data);
                    if(data.status === true) {
                        var erreurs ='<div class="alert alert-success">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-success').html(erreurs);

                        $('#alert-success').show(0).delay(1500).hide(0);

                        $('.nav-tabs li.active').next('li').find('a').attr('data-toggle', 'tab').tab('show');
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show();
                    }
                },

                error: function (data) {
                    console.log(data.responseJSON.errors);

                    var errors = data.responseJSON.errors;

                    var erreurs = '<div class="alert alert-danger"><ul>';

                    $.each(errors, function(i, error) {
                        erreurs += '<li>'+error+'</li>';
                    });

                    erreurs += '</ul></div>';
                    $('#alert-danger').html(erreurs);
                    $('#alert-danger').show();
                }
            })
        } else {
          return;
        }
    });


    $('#depertment, #depertment_final').select2({
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

    $('#religion, #religion_final').select2({
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

    $('#marital_status, #marital_status_final').select2({
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

    $('#blood_group, #blood_group_final').select2({
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

    $('#plant_name, #plant_name_final').select2({
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

    $('#designation, #designation_final').select2({
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

    $('#category, #category_final').select2({
      placeholder: 'Enter category',
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

    $('#working_shift, #working_shift_final').select2({
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

    $('#manage_by, #manage_by_final').select2({
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


	$('#salary_grade, #salary_grade_final').select2({
	      placeholder: 'Enter a Salary Grade',
	      allowClear: true,
	        ajax: {
	            dataType: 'json',
	            url: '{{URL::to('/')}}/salarygrade_list',
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


    $('#employeestatus, #employeestatus_final').select2({
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

    $('#job_location, #job_location_final').select2({
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


    $('#education_name, #education_name_final').select2({
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

    $('#section, #section_final').select2({
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
</script>


<script>
$(document).ready(function($) {
    var btnCust = '<button type="button"  class="btn btn-secondary" title="Add picture tags" ' +
        'onclick="alert(\'Call your custom code here.\')">' +
        '<i class="glyphicon glyphicon-tag"></i>' +
    '</button>';

    var employeeImage = @json($employee_info->Images)

    var defaultImage = '';

    if (employeeImage) {
        defaultImage = "{{ asset('employee_image/') }}/"+employeeImage
    } else {
        defaultImage = "{{ asset('img/default_avatar_male.jpg') }}"
    }

    $("#avatar-1").fileinput({
        overwriteInitial: true,
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img src="'+defaultImage+'" alt="Your Avatar">',
        layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

    $('.probation').hide();
    $('.confirm').hide();



	if($("#employeestatus").val() == 1){
	    $('.probation').show();
	    $('.confirm').hide();

	}else{
	    $('.confirm').show();
	    $('.probation').hide();

	}

    if($("#employeestatus_final").val() == 1){
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

        $('#employeestatus_final').on('change', function(){

            if($("#employeestatus_final").val() == 1){
                $('.probation').show();
                $('.confirm').hide();

            }else{
                $('.confirm').show();
                $('.probation').hide();

            }

            if($("#employeestatus_final").val() == null){
                $('.probation').hide();
                $('.confirm').hide();
            }
        });

	});
</script>
@endsection
