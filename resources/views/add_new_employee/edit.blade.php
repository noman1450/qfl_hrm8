@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style>
    body {
        margin-top:30px;
    }
    .stepwizard-step p {
        margin-top: 0px;
        color:#666;
    }
    .stepwizard-row {
        display: table-row;
    }
    .stepwizard {
        display: table;
        width: 100%;
        position: relative;
    }
    .stepwizard-step button[disabled] {
        /*opacity: 1 !important;
        filter: alpha(opacity=100) !important;*/
    }
    .stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
        opacity:1 !important;
        color:#bbb;
    }
    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content:" ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-index: 0;
    }
    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }
    span.required {
        color: red;
    }
</style>
@endsection
@section('content')

<div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        <div class="stepwizard-step col-xs-3"> 
            <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
            <p><small>Personal Information</small></p>
        </div>
        <div class="stepwizard-step col-xs-3"> 
            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
            <p><small>Official Information</small></p>
        </div>
        <div class="stepwizard-step col-xs-3"> 
            <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
            <p><small>Card Info</small></p>
        </div>
        <div class="stepwizard-step col-xs-3"> 
            <a href="#step-4" type="button" class="btn btn-default btn-circle final-step" disabled="disabled">4</a>
            <p><small>Final</small></p>
        </div>
    </div>
</div>
<span class="frmMsg"></span>
<div class="panel panel-primary setup-content" id="step-1">
    <div class="panel-body">
        <form method="post" class="dynamicFormSubmit" action="{{ route('add_new_employee.store') }}" id="step-one" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <input type="hidden" id="step_one_employee_id" name="employee_id">
                <input type="hidden" name="step" value="one">

                <div class="col-md-8">
                    <div class="form-group col-md-6">
                        <label class="control-label">Employee Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name.." autofocus required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Nickname <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname.." required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Father's Name</label>
                        <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father's Name..">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Mother's Name</label>
                        <input type="text" class="form-control" id="mothers_name" name="mothers_name" placeholder="Mother's Name..">
                    </div>


                    <div class="form-group col-md-6">
                        <label class="control-label">Contact Number <span class="required">*</span></label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Contact Number.." required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Email <span class="required">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email.." required>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label">Highest Education <span class="required">*</span></label>
                        <select class="form-control" id="education_name" name="hrm_education_id" style="width: 100%;" required></select>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label">Date of Birth</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">NID / Smart Card</label>
                        <input type="text" class="form-control" id="nid" name="nid" placeholder="NID/Smart Card..">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">TIN</label>
                        <input type="text" class="form-control" id="tin" name="tin" placeholder="TIN">
                    </div>

                    <div class="form-group col-md-3">
                        <label class="control-label">Gender <span class="required">*</span></label>
                        <select class="form-control select-two" id="gender" name="gender" style="width: 100%;" required>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label">Religion <span class="required">*</span></label>
                        <select class="form-control" id="religion" name="religion" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label">Marital Status <span class="required">*</span></label>
                        <select class="form-control" id="marital_status" name="marital_status" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label">Blood Group <span class="required">*</span></label>
                        <select class="form-control" id="blood_group" name="blood_group" style="width: 100%;" required></select>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Present Address <span class="required">*</span></label>
                        <textarea class="form-control" id="present_address" name="present_address" placeholder="Present Address.." required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Permanent Address <span class="required">*</span></label>
                        <textarea class="form-control" id="permanent_address" name="permanent_address" placeholder="Permanent Address.." required></textarea>
                    </div>
                    <div class="text-center">
                        <div class="kv-avatar">
                            <div class="file-loading">
                                <input id="avatar_1" name="images" type="file">
                            </div>
                        </div>
                        <div class="kv-avatar-hint"><small style="color: red;">Select file < 1500 KB</small></div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary nextBtn pull-right" type="submit">Next</button>
        </form>
    </div>
</div>
<!----------------------------------------------------------------------->
<div class="panel panel-primary setup-content" id="step-2">
    <div class="panel-body">
        <form method="POST" class="dynamicFormSubmit" action="{{ route('add_new_employee.store') }}" id="step-two">
            {{ csrf_field() }}
            <div class="box-body" id="jobinfo">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="step_two_employee_id" name="employee_id">
                        <input type="hidden" name="step" value="two">

                        <div class="col-md-4 form-group">
                            <label>Employee Code <span class="required">*</span></label>
                            <input type="text" class="form-control" id="employee_code" name="employee_code" placeholder="Employee Code" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Department <span class="required">*</span></label>
                            <select class="form-control" id="depertment" name="depertment" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Sub-department <span class="required">*</span></label>
                            <select class="form-control" id="section" name="section" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Designation <span class="required">*</span></label>
                            <select class="form-control" id="designation" name="designation" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Employee Category <span class="required">*</span></label>
                            <select class="form-control" id="category" name="category" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Job Placement <span class="required">*</span></label>
                            <select class="form-control" id="job_location" name="job_location" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Plant/Work Place <span class="required">*</span></label>
                            <select class="form-control" id="plant_name" name="plant_name" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Reporting to <span class="required">*</span></label>
                            <select class="form-control" id="manage_by" name="manage_by" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Working Shift <span class="required">*</span></label>
                            <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" required>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="control-label">Joining Date <span class="required">*</span></label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask required>
                            </div>
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="control-label">Gross Salary <span class="required">*</span></label>
                            <input type="text" class="form-control" id="basic_salary" name="basic_salary" placeholder="Basic Salary.." required>
                        </div>

                        @if (config('module_config.payroll_module') == 1)
                            <div class="col-md-3 form-group">
                                <label class="control-label">Salary Grade <span class="required">*</span></label>
                                <select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;" required>
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3 form-group">
                            <label class="control-label">Overtime</label>
                            <select class="form-control select-two" id="overtime" name="overtime" style="width: 100%;">
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="control-label">Insurance</label>
                            <select class="form-control select-two" id="insurance" name="insurance" style="width: 100%;">
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="control-label">Employee Status <span class="required">*</span></label>
                            <select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;" required>
                            </select>
                        </div>

                        <div class="col-md-3 form-group probation">
                            <label class="control-label">Probation Period (Months)</label>
                            <select class="form-control select-two" id="probation_period" name="probation_period" style="width: 100%;">
                                @foreach ($probations as $probation)
                                    <option value="{{ $probation->id }}">{{ $probation->period }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group confirm">
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
            </div>
            <button class="btn btn-primary nextBtn pull-right" type="submit">Next</button>
        </form>
    </div>
</div>

<div class="panel panel-primary setup-content" id="step-3">
    <div class="panel-body">
        <form method="POST" class="dynamicFormSubmit" action="{{ route('add_new_employee.store') }}" id="step-three">
            {{ csrf_field() }}
            <div class="box-body" id="jobinfo">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" id="step_three_employee_id" name="employee_id">
                        <input type="hidden" name="step" value="three">

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Card Code <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="card_code" name="card_code" placeholder="Card Code.." value="{{ old('card_code') }}" required>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Old Code</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="old_code" name="old_code" placeholder="Old Code.." value="{{ old('old_code') }}">
                            </div>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label class="col-lg-3 control-label">Device Id</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="device_id" name="device_id" placeholder="Device Id.." value="{{ old('device_id') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>                
            <button class="btn btn-primary nextBtn pull-right" type="submit">Next</button>
        </form>
    </div>
</div>

<div class="panel panel-primary setup-content" id="step-4">
    <div class="panel-body">
        <form method="POST" class="dynamicFormSubmit" action="{{ route('add_new_employee.store') }}" id="step-final">
            {{ csrf_field() }}
            <div class="box-body" id="jobinfo">
                <input type="hidden" id="step_final_employee_id" name="employee_id">
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
                                <td id="employee_present_address"></td>
                            </tr>
                            <tr>
                                <th>Permanent Address</th>
                                <td id="employee_permanent_address"></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td id="employee_contact_number"></td>
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
                                <td id="employee_code_"></td>
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

                        <div class="row" style="background-color: #DAF7A6;">
                            <div class="col-xs-12 form-group">
                                <label class="control-label">User Name</label>
                                <input type="text" id="employeeUserName" class="form-control" name="username" placeholder="User Name" readonly>
                            </div>
                            <div class="col-xs-12 form-group">
                                <label class="control-label">Email</label>
                                <input type="text" class="form-control" id="employeeUserEmail" name="email" placeholder="Email" required readonly>
                            </div>
                            <div class="col-xs-12 form-group">
                                <label class="control-label">Password</label>
                                <input type="password" class="form-control" id="employeePassword" name="password" placeholder="Password at least 8 characters" autocomplete="off">
                            </div>
                            <div class="col-xs-12 form-group">
                                <label class="control-label">Confirm Password</label>
                                <input type="password" class="form-control" id="employeeConfirmPassword" name="password_confirmation" placeholder="Confirm Password">
                            </div>

                            <div class="col-xs-12 form-group">
                                <label class="control-label">User Role</label>
                                <select class="form-control select-two" id="userrole" name="userrole" required>
                                    <option>-- Select Role --</option>
                                    @foreach ($role_lists ?? [] as $keys)
                                    <option value={{$keys->id}}>{{$keys->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-success pull-right" type="submit">Finish!</button>
        </form>
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
    $(document).ready(function($) {
        var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn');
        allWells.hide();
        //--
        navListItems.click(function (e) {
            e.preventDefault();
            var $target = $($(this).attr('href')),
            $item = $(this);

            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-success').addClass('btn-default');
                $item.addClass('btn-success');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
            }
        });
        //--
        
        allNextBtn.click(function () {
            var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("select[required],input[required],textarea[required]"),
            isValid = true;

            $(".form-group").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
                if (!curInputs[i].validity.valid) {
                    isValid = false;
                    $(curInputs[i]).closest(".form-group").addClass("has-error");
                }
            }
            if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
        });
        //--
        $('div.setup-panel div a.btn-success').trigger('click');
        
        //----------------------------------------------------------------------
        //--------------------
        //----------------------------------------------------------------------
        
        
        $(document).on('submit', '.dynamicFormSubmit', function (e) {
            e.preventDefault();
            var $form   = new FormData(this),
                url     = this.action;
                token   = $("[name='_token']").val();
            var employee_id = $("input[name='employee_id']").val();
            var step = $("input[name='step']").val();
            
            if(employee_id == '' && step != 'one'){
                alert("This Employee Personal Info Is not found.");
                return false;
            }else{
                $.ajax({
                    type       : 'POST',
                    url        : url,
                    data       : $form,
                    cache      : false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (data) {
                        $(".frmMsg").html("");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                        if(data['status'] == true){
                            $(".frmMsg").html("<div class='alert alert-success alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> "+ data['message'] +"</div>");
                            $("input[name='employee_id']").val(data['employee_draft_id']);
                            if(step == 'final'){
                                location.reload();
                            }
                        }else{
                           $(".frmMsg").html("<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Failed!</strong> "+ data['message'] +"</div>"); 
                        }
                        setTimeout(function() {// wait for 2 secs(2)
                            $(".frmMsg").html("");
                        }, 10000);
                    }
                });
            }
        });
        

        
        
        
        
        
        
        
        $('.select-two').select2();
        $(function () {
            $("[data-mask]").inputmask();//Datemask dd/mm/yyyy
        });
        
        $('#employeestatus').on('change', function(){
            if($("#employeestatus").val() == 1){
                $('.probation').show();
                $('.confirm').hide();
            }else{
                $('.probation').hide();
                $('.confirm').hide();
            }
        });
        
        select2Dropdown("#religion","{{URL::to('/')}}/religion_list_data","Search Religion");
        select2Dropdown("#depertment","{{URL::to('/')}}/depertment_list_data","Search depertment");
        select2Dropdown("#marital_status","{{URL::to('/')}}/maritalstatus_list_data","Search Marital Status");
        select2Dropdown("#blood_group","{{URL::to('/')}}/blood_group_list_data","Search Blood Group");
        select2Dropdown("#plant_name","{{URL::to('/')}}/plantname_list_data","Search Plant/Work Place Name");
        select2Dropdown("#designation","{{URL::to('/')}}/designation_list_data","Search designation");
        select2Dropdown("#category","{{URL::to('/')}}/category_list_data","Search category");
        select2Dropdown("#working_shift","{{URL::to('/')}}/shift_list_data","Search working shift");
        select2Dropdown("#manage_by","{{URL::to('/')}}/employee_list_data","Search manage by");
        select2Dropdown("#salary_grade","{{URL::to('/')}}/salarygrade_list","Search Salary Grade");
        select2Dropdown("#employeestatus","{{URL::to('/')}}/employeestatus_list_data","Search employee status");
        select2Dropdown("#job_location","{{URL::to('/')}}/location_list_data","Search job location");
        select2Dropdown("#education_name","{{URL::to('/')}}/education_list_data","Search Education Name");
        select2Dropdown("#section","{{URL::to('/')}}/section_list_data","Search Sub-department");
        
        
        
        $("#avatar_1").fileinput({
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
            layoutTemplates: {main2: '{preview} ' + ' {remove} {browse}'},
            allowedFileExtensions: ["jpg", "png", "gif"]
        });
        
        $(".final-step").click(function() {
            getEmployeeDataBy();
        });
        //-----------------------------------------------------------------------
        
        function getEmployeeDataBy() {
            // personal data
            $('#employee_name_final').text($("#employee_name").val());
            $('#employee_nickname').text($("#nickname").val());
            $('#employee_present_address').text($("#present_address").val());
            $('#employee_permanent_address').text($("#permanent_address").val());
            $('#employee_contact_number').text($("#contact_number").val());
            $('#employee_email').text($("#email").val());
            $('#employee_gender').text($("#gender option:selected").text());
            $('#employee_religion').text($("#religion option:selected").text());
            $('#employee_marital_status').text($("#marital_status option:selected").text());
            $('#employee_blood_group').text($("#blood_group option:selected").text());
            $('#employee_dob').text($("#date_of_birth").val());
            $('#employee_nid').text($("#nid").val());
            $('#employee_tin').text($("#tin").val());
            $('#employee_father_name').text($("#father_name").val());
            $('#employee_mother_name').text($("#mothers_name").val());
            $('#employee_education_name').text($("#education_name option:selected").text());

            // official data
            $('#employee_code_').text($("#employee_code").val());
            $('#employee_department_name').text($("#depertment option:selected").text());
            $('#employee_designation_name').text($("#designation option:selected").text());
            $('#employee_category_name').text($("#category option:selected").text());
            $('#employee_location_name').text($("#job_location option:selected").text());
            $('#employee_section_name').text($("#section option:selected").text());
            $('#employee_shift_name').text($("#working_shift option:selected").text());
            $('#employee_overtime_status').text($("#overtime option:selected").text());
            $('#employee_insurance').text($("#insurance option:selected").text());
            $('#employee_plant_name').text($("#plant_name option:selected").text());
            $('#employee_manage_by_name').text($("#manage_by option:selected").text());
            $('#employee_basic_salary').text($("#basic_salary").val());
            $('#employee_grade_name').text($("#salary_grade option:selected").text());
            $('#employee_joining_date').text($("#joining_date").val());
            $('#employee_employeestatus_name').text($("#employeestatus option:selected").text());
            $('#employee_confirmation_date').text($("#confirmation_date").val());
            $('#employee_probision').text($("#probation_period option:selected").text());

            //card data
            $('#employee_card_code').text($("#card_code").val());
            $('#employee_old_code').text($("#old_code").val());
            $('#employee_device_id').text($("#device_id").val());
            
            //
            $('#employeeUserName').val($("#employee_name").val());
            $('#employeeUserEmail').val($("#email").val());
            $('#employeePassword').val('');
            $('#employeeConfirmPassword').val('');
        }
    });
</script>
@endsection