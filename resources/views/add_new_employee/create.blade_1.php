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
    .nav-tabs li.disabled a {
        pointer-events: none;
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
        <h3 class="box-title">Create Employee Information</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

    <ul class="nav nav-tabs sidebar-tabs" id="sidebar" role="tablist">
        <li class="disabled active">
            <a href="#tab-1" role="tab" data-toggle="tab">Personal Information</a>
        </li>
        <li class="disabled">
            <a href="#tab-2" role="tab" data-toggle="tab">Official Information</a>
        </li>
        <li class="disabled">
            <a href="#tab-3" role="tab" data-toggle="tab">Card Info</a>
        </li>
        <li class="disabled">
            <a href="#tab-4" role="tab" data-toggle="tab">User Credential</a>
        </li>
        <li class="disabled">
            <a href="#tab-5" role="tab" data-toggle="tab">Final</a>
        </li>
    </ul> <!--/.nav-tabs.sidebar-tabs -->

    <div>
        <div id="alert-danger"></div>
        <div id="alert-success"></div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-1">
            <form  method="POST" action="{{ route('add_new_employee.store') }}" id="step-one" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">

                    <input type="text" id="step_one_employee_id" name="employee_id" class="hidden">

                    <input type="hidden" name="step" value="one">

                    <div class="col-md-9 personal-info">
                        <div class="form-group col-md-4">
                            <label class="control-label">Employee Name *</label>
                            
                                <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name.." autofocus required>
                            
                        </div>
                        <div class="form-group col-md-4">
                            <label class=" control-label">Nickname *</label>
                                <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname.." required>
                            
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Present Address *</label>
                                <input type="text" class="form-control" name="present_address" placeholder="Present Address.." required>
                         
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Permanent Address *</label>
                                <input type="text" class="form-control" name="permanent_address" placeholder="Permanent Address.." required>
                            
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Contact Number *</label>
                                <input type="text" class="form-control" name="contact_number" placeholder="Contact Number.." required>
                           
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Email *</label>
                                <input type="text" class="form-control" name="email" placeholder="Email.." required>
                          
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Gender *</label>
                                <select class="form-control" id="gender" name="gender" style="width: 100%;" required>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Religion *</label>

                                <select class="form-control" id="religion" name="religion" style="width: 100%;" required>
                                </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Marital Status *</label>
                                <select class="form-control" id="marital_status" name="marital_status" style="width: 100%;" required>
                                </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Blood Group *</label>
                                <select class="form-control" id="blood_group" name="blood_group" style="width: 100%;" required>
                                </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Date of Birth</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {{-- <!-- <input type="text" class="form-control pull-right" id="date_of_birth" name="date_of_birth" data-date-format="dd-mm-yyyy"  value="{{  date('d-m-Y', strtotime(str_replace('-', '/', $employee->dob ))) }}"  required readonly> --> --}}
                                    <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
                                    @if ($errors->has('date_of_birth'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('date_of_birth') }}</strong>
                                    </span>
                                    @endif
                                </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">NID / Smart Card</label>
                                {{-- <input type="text" class="form-control" name="nid" placeholder="NID/Smart Card.." value="{{ $employee->nid ?? old('nid') }}" > --}}
                                <input type="text" class="form-control" name="nid" placeholder="NID/Smart Card..">
                                @if ($errors->has('nid'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nid') }}</strong>
                                </span>
                                @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">TIN</label>
                                {{-- <input type="text" class="form-control" name="tin" placeholder="TIN" value="{{ $employee->tin ?? old('tin') }}" > --}}
                                <input type="text" class="form-control" name="tin" placeholder="TIN">
                                @if ($errors->has('tin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tin') }}</strong>
                                </span>
                                @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label class=" control-label">Father's Name</label>
                                {{-- <input type="text" class="form-control" name="father_name" placeholder="Father's Name.." value="{{ $employee->father_name ?? old('father_name') }}" > --}}
                                <input type="text" class="form-control" name="father_name" placeholder="Father's Name..">
                                @if ($errors->has('father_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('father_name') }}</strong>
                                </span>
                                @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Mother's Name</label>
                                {{-- <input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name.." value="{{ $employee->mother_name ?? old('mothers_name') }}" > --}}
                                <input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name..">
                                @if ($errors->has('mothers_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mothers_name') }}</strong>
                                </span>
                                @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Highest Education *</label>
                                <select class="form-control select2" id="education_name" name="hrm_education_id" style="width: 100%;" required>
                                </select>
                        </div>
                    </div>

                    <div class="col-md-3 text-center">
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
            <form  method="POST" action="{{ route('add_new_employee.store') }}" id="step-two">
                {{ csrf_field() }}
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" id="step_two_employee_id" name="employee_id" class="hidden">

                            <input type="hidden" name="step" value="two">

                            <div class="col-md-4 form-group">
                                <label>Employee Code</label>
                                <input type="text" class="form-control" name="employee_code" placeholder="Employee Code" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Department</label>
                                <select class="form-control select2" id="depertment" name="depertment" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Designation</label>
                                <select class="form-control" id="designation" name="designation" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Employee Category</label>
                                <select class="form-control" id="category" name="category" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Job Placement</label>
                                <select class="form-control" id="job_location" name="job_location" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Sub-department</label>
                                <select class="form-control" id="section" name="section" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Working Shift</label>
                                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Overtime</label>
                                <select class="form-control" name="overtime" style="width: 100%;">
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Insurance</label>
                                <select class="form-control" name="insurance" style="width: 100%;">
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Plant/Work Place</label>
                                <select class="form-control" id="plant_name" name="plant_name" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Reporting to</label>
                                <select class="form-control" id="manage_by" name="manage_by" style="width: 100%;" required>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label">Gross Salary</label>
                                <input type="text" class="form-control" name="basic_salary" placeholder="Basic Salary.." required>
                            </div>

                            @if (config('module_config.payroll_module') == 1)
                            <div class="col-md-4 form-group">
                                <label class="control-label">Salary Grade</label>
                                <select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;" required>
                                </select>
                            </div>
                            @endif

                            <div class="col-md-4 form-group">
                                <label class="control-label">Joining Date</label>
                                <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="control-label">Employee Status</label>
                                <select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;" required>
                                </select>
                            </div>

                            <div class="col-md-4 form-group probation">
                                <label class="control-label">Probation Period (Months)</label>
                                <select class="form-control" id="probation_period" name="probation_period" style="width: 100%;">
                                    @foreach ($probations as $probation)
                                    <option value="{{ $probation->id }}">{{ $probation->period }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group confirm">
                                <label class="control-label">Confirmation Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>

                                    <input type="text" class="form-control" id="confirmation_date" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
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
            <form  method="POST" action="{{ route('add_new_employee.store') }}" id="step-three">
                {{ csrf_field() }}
                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="step_three_employee_id" name="employee_id" class="hidden">

                            <input type="hidden" name="step" value="three">

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Card Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="card_code" placeholder="card code.." value="{{ old('card_code') }}" required>
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Old Code</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="old_code" placeholder="Old Code.." value="{{ old('old_code') }}">
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                <label class="col-lg-3 control-label">Device Id</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="device_id" placeholder="Device Id.." value="{{ old('device_id') }}">
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
            <form  method="POST" action="{{ route('add_new_employee.store') }}" id="step-four">
                {{ csrf_field() }}

                <input type="text" id="step_four_employee_id" name="employee_id" class="hidden">

                <input type="hidden" name="step" value="four">

                <div class="box-body tabcontent" id="jobinfo">
                    <div class="row">
                        <div class="col-md-6" >
                            <div class="col-md-12">
                                <div class="col-xs-12 form-group">
                                    <label class="control-label">User Name</label>
                                </div>
                                <input type="text" id="employeeUserName" class="form-control hidden" name="username" placeholder="User Name">

                                <input type="text" class="form-control hidden" id="employeeUserDesignation" name="designation" placeholder="Designation">

                                <input type="text" class="form-control hidden" id="employeeUserLocation" name="location" placeholder="Designation">

                                <div class="col-xs-12 form-group">
                                    <label class="control-label">Email</label>
                                    <input type="text" class="form-control" id="employeeUserEmail" name="email" placeholder="Email" required readonly>
                                </div>

                                <div class="col-xs-12 form-group">
                                    <label class="control-label">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password at least 8 characters"  required>
                                </div>
                                <div class="col-xs-12 form-group">
                                    <label class="control-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password"  required>
                                </div>

                                <div class="col-xs-12 form-group">
                                    <label class="control-label">User Role</label>
                                    <select class="form-control" id="userrole" name="userrole" required>
                                        <option>-- Select Role --</option>
                                        @foreach ($role_lists ?? [] as $keys)
                                        <option value={{$keys->id}}>{{$keys->display_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-xs-12">
                            <button type="button" class="btn btn-primary block btn-flat btn prev-btn">Prev</button>
                            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Next</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-5">
            <form  method="POST" action="{{ route('add_new_employee.store') }}" id="step-final">
                {{ csrf_field() }}
                <div class="box-body tabcontent" id="jobinfo">
                    <input type="text" id="step_final_employee_id" name="employee_id" class="hidden">

                    <input type="hidden" name="step" value="final">

                    <div class="row">
                        <div class="col-md-4">
                            <div>
                                <h3 class="text-info" style="background-color:#ddd;width:max-content;padding:5px;border-radius:5px">Personal Information</h3>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td id="employee_name_final"></td>
                                </tr>
                                <tr>
                                    <th>Nickname</th>
                                    <td id="employee_nickname"></td>
                                </tr>
                                <tr>
                                    <th>Present Address</th>
                                    <td id="present_address"></td>
                                </tr>
                                <tr>
                                    <th>Permanent Address</th>
                                    <td id="permanent_address"></td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td id="contact_number"></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td id="employee_email"></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td id="employee_gender"></td>
                                </tr>
                                <tr>
                                    <th>Religion</th>
                                    <td id="employee_religion"></td>
                                </tr>
                                <tr>
                                    <th>Marital Status</th>
                                    <td id="employee_marital_status"></td>
                                </tr>
                                <tr>
                                    <th>Blood Group</th>
                                    <td id="employee_blood_group"></td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td id="employee_dob"></td>
                                </tr>
                                <tr>
                                    <th>NID / Smart Card</th>
                                    <td id="employee_nid"></td>
                                </tr>
                                <tr>
                                    <th>TIN</th>
                                    <td id="employee_tin"></td>
                                </tr>
                                <tr>
                                    <th>Father's Name</th>
                                    <td id="employee_father_name"></td>
                                </tr>
                                <tr>
                                    <th>Mother's Name</th>
                                    <td id="employee_mother_name"></td>
                                </tr>
                                <tr>
                                    <th>Highest Education</th>
                                    <td id="employee_education_name"></td>
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
                                    <td id="employee_code"></td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td id="employee_department_name"></td>
                                </tr>
                                <tr>
                                    <th>Designation</th>
                                    <td id="employee_designation_name"></td>
                                </tr>
                                <tr>
                                    <th>Employee Category</th>
                                    <td id="employee_category_name"></td>
                                </tr>
                                <tr>
                                    <th>Job Placement</th>
                                    <td id="employee_location_name"></td>
                                </tr>
                                <tr>
                                    <th>Sub-department</th>
                                    <td id="employee_section_name"></td>
                                </tr>
                                <tr>
                                    <th>Working Shift</th>
                                    <td id="employee_shift_name"></td>
                                </tr>
                                <tr>
                                    <th>Overtime</th>
                                    <td id="employee_overtime_status"></td>
                                </tr>
                                <tr>
                                    <th>Insurance</th>
                                    <td id="employee_insurance"></td>
                                </tr>
                                <tr>
                                    <th>Plant/Work Place</th>
                                    <td id="employee_plant_name"></td>
                                </tr>
                                <tr>
                                    <th>Reporting to</th>
                                    <td id="employee_manage_by_name"></td>
                                </tr>
                                <tr>
                                    <th>Gross Salary</th>
                                    <td id="employee_basic_salary"></td>
                                </tr>
                                <tr>
                                    <th>Salary Grade</th>
                                    <td id="employee_grade_name"></td>
                                </tr>
                                <tr>
                                    <th>Joining Date</th>
                                    <td id="employee_joining_date"></td>
                                </tr>
                                <tr>
                                    <th>Employee Status</th>
                                    <td id="employee_employeestatus_name"></td>
                                </tr>

                                <tr id="hide_show_probition">
                                    <th>Probation Period (Months)</th>
                                    <td id="employee_probision">3</td>
                                </tr>

                                <tr>
                                    <th>Confirmation Date</th>
                                    <td id="employee_confirmation_date"></td>
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
                                    <td id="employee_card_code"></td>
                                </tr>
                                <tr>
                                    <th>Old Code</th>
                                    <td id="employee_old_code"></td>
                                </tr>
                                <tr>
                                    <th>Device Id</th>
                                    <td id="employee_device_id"></td>
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
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>

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

    $( "#step-one" ).submit(function(event) {
      	event.preventDefault();

        if(confirm('Do you want to submit?')) {
            var $form   = new FormData(this),
            url         = this.action;
            token       = $("[name='_token']").val();

            $.ajax({
                type        : 'POST',
                url         : url,
                data        : $form,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',

                success: function(data) {
                    if(data.status === true) {
                        var erreurs ='<div class="alert alert-success">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#step_one_employee_id, #step_two_employee_id, #step_three_employee_id, #step_four_employee_id, #step_final_employee_id').val(data.employee_draft_id);

                        $('#alert-success').html(erreurs);

                        $('#alert-success').show().delay(1500).hide(0);

                        $('.nav-tabs li.active').next('li').find('a').attr('data-toggle', 'tab').tab('show');

                        getEmployeeDataBy($('input[name=employee_id]').val())
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show().delay(10000).hide(0);
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
                    $('#alert-danger').show().delay(10000).hide(0);
                }
            })
        }else{
          return;
        }
    });

    $( "#step-two" ).submit(function(event) {
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

                        getEmployeeDataBy($('input[name=employee_id]').val())
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show().delay(10000).hide(0);
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
                    $('#alert-danger').show().delay(10000).hide(0);
                }
            })
        } else {
          return;
        }
    });

    $( "#step-three" ).submit(function(event) {
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

                        getEmployeeDataBy($('input[name=employee_id]').val())
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show().delay(10000).hide(0);
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
                    $('#alert-danger').show().delay(10000).hide(0);
                }
            })
        } else {
          return;
        }
    });

    $( "#step-four" ).submit(function(event) {
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

                        getEmployeeDataBy($('input[name=employee_id]').val())

                       // window.location.reload()
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show().delay(10000).hide(0);
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
                    $('#alert-danger').show().delay(10000).hide(0);
                }
            })
        } else {
          return;
        }
    });

    $("#step-final").submit(function(event) {
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

                        getEmployeeDataBy($('input[name=employee_id]').val())

                       // window.location.reload()
                    } else {
                        var erreurs ='<div class="alert alert-danger">';
                            erreurs += '<div>'+ data.message + '</div>'
                        erreurs += '</div>';

                        $('#alert-danger').html(erreurs);
                        $('#alert-danger').show().delay(10000).hide(0);
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
                    $('#alert-danger').show().delay(10000).hide(0);
                }
            })
        } else {
          return;
        }
    });

    function getEmployeeDataBy(id) {
        $.get("{{ url('add_new_employee') }}/"+id+'/edit')
            .then(function(response) {
                console.log(response.employee.employee_name);

                // personal data
                $('#employee_name_final').text(response.employee.employee_name);
                $('#employeeUserName').val(response.employee.employee_name);

                $('#employeeUserLocation').val(response.employee_info.location_id);

                $('#employee_nickname').text(response.employee.nickname);
                $('#present_address').text(response.employee.present_address);
                $('#permanent_address').text(response.employee.permanent_address);
                $('#contact_number').text(response.employee.contact_number);
                $('#employee_email').text(response.employee.email);
                $('#employeeUserEmail').val(response.employee.email);
                $('#employee_gender').text(response.employee.gender == 1 ? 'Male' : 'Female');
                $('#employee_religion').text(response.employee_info.religion);
                $('#employee_marital_status').text(response.employee_info.marital_status);
                $('#employee_blood_group').text(response.employee_info.blood_group);
                $('#employee_dob').text(response.employee.dob);
                $('#employee_nid').text(response.employee.nid);
                $('#employee_tin').text(response.employee.tin);
                $('#employee_father_name').text(response.employee.father_name);
                $('#employee_mother_name').text(response.employee.mother_name);
                $('#employee_education_name').text(response.employee_info.education_name);

                // official data
                $('#employee_code').text(response.employee_info.employee_code);
                $('#employee_department_name').text(response.employee_info.depertment_name);

                $('#employee_designation_name').text(response.employee_info.designation_name);
                $('#employeeUserDesignation').val(response.employee_info.designation_name);

                $('#employee_category_name').text(response.employee_info.category_name);
                $('#employee_location_name').text(response.employee_info.location_name);
                $('#employee_section_name').text(response.employee_info.section_name);
                $('#employee_shift_name').text(response.employee_info.shift_name);
                $('#employee_overtime_status').text(response.employee_info.overtime_status == 1 ? 'Yes' : 'No');
                $('#employee_insurance').text(response.employee_info.insurance == 1 ? 'Yes' : 'No');
                $('#employee_plant_name').text(response.employee_info.plant_name);
                $('#employee_manage_by_name').text(response.employee_info.manage_by_name);
                $('#employee_basic_salary').text(response.employee_info.basic_salary);
                $('#employee_grade_name').text(response.employee_info.grade_name);
                $('#employee_joining_date').text(response.employee_info.joining_date);
                $('#employee_employeestatus_name').text(response.employee_info.employeestatus_name);
                $('#employee_confirmation_date').text(response.employee_info.confirmation_date);

                if (response.employee_info.employeestatus_id == 1) {
                    $('#hide_show_probition').show()
                    $('#employee_probision').text(response.employee_info.period);
                } else {
                    $('#hide_show_probition').hide()
                    $('#employee_probision').text('');
                }
                //card data
                $('#employee_card_code').text(response.employee_info.card_code);
                $('#employee_old_code').text(response.employee_info.old_code);
                $('#employee_device_id').text(response.employee_info.device_id);

            })
    }

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

    $('#job_location').select2({
      placeholder: 'Enter job location',
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

    $.ajax({
        type: 'POST',
        url : "{{URL::to('/')}}/location_list",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(data) {
                var dataSet = data.data;
                table = $('#list_table').DataTable({
                destroy:    true,
                paging:     false,
                searching:  false,
                ordering:   true,
                bInfo:      false,
                "data":     dataSet,
                "columns": [
                    { "data": "location_name" },
                    { "data": "checkbox",
                        "mRender": function (data, type, full) {
                            return '<input type="checkbox" name="permissionlocation[]" value="'+full.id+'">';
                        }
                    },
                    { "data": "radio",
                        "mRender": function (data, type, full) {
                            return '<input type="radio" name="defaultlocation[]" value="'+full.id+'">';
                        }
                    }
                ],
                "order": [[0,'asc']]
            });
        }
    });
</script>


<script>
$(document).ready(function($) {
    var btnCust = '<button type="button"  class="btn btn-secondary" title="Add picture tags" ' +
        'onclick="alert(\'Call your custom code here.\')">' +
        '<i class="glyphicon glyphicon-tag"></i>' +
    '</button>';

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
        defaultPreviewContent: '<img src="{{asset('img/default_avatar_male.jpg')}}" alt="Your Avatar">',
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
