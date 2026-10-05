@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Approve Form</h3>
	</div>


	<div class="box-body">
		<form action="{{ url('/employee_joining_approvals') }}" method="post" style="margin: 0;">
            @csrf

            <input type="hidden" name="hrm_employee_job_info_id" value="{{ encrypt($employee->hrm_employee_job_info_id) }}">

            <div class="row">
                <div class="col-md-7">
                    <div class="panel" style="border: 1px solid #00000012; box-shadow: none;">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Personal Info.</h4>

                                    <table class="table table-sm">
                                        <tr>
                                            <th>Name</th>
                                            <td>
                                                <div style="display: flex; align-items: center; column-gap: 20px;">
                                                    <img src="{{ asset('employee_image/'.$employee->image) }}" alt="" style="height: 50px; width: 50px; border-radius: 9999px;">

                                                    <div style="display: flex; flex-direction: column; row-gap: 5px;">
                                                        <strong style="font-size: 14px" id="employee_name">{{ $employee->employee_name }}</strong>
                                                        <span style="font-size: 13px" id="category_name">{{ $employee->category_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Nickname</th>
                                            <td>
                                                {{ $employee->nickname }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Present Address</th>
                                            <td>
                                                {{ $employee->present_address }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Permanent Address</th>
                                            <td>
                                                {{ $employee->permanent_address }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Contact Number</th>
                                            <td>
                                                {{ $employee->contact_number }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                {{ $employee->email }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Gender</th>
                                            <td>
                                                {{ $employee->gender == 1 ? 'Male' : 'Female' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Religion</th>
                                            <td>
                                                {{ $employee->religion }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Marital Status</th>
                                            <td>
                                                {{ $employee->marital_status }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Blood Group</th>
                                            <td>
                                                {{ $employee->blood_group }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Date of Birth</th>
                                            <td>
                                                {{  date('d-m-Y', strtotime(str_replace('-', '/', $employee->dob ))) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>NID / Smart Card</th>
                                            <td>
                                                {{ $employee->nid }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>TIN</th>
                                            <td>
                                                {{ $employee->tin }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Father's Name</th>
                                            <td>
                                                {{ $employee->father_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>
                                                {{ $employee->mother_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Highest Education</th>
                                            <td>
                                                {{ $employee->education_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h4>Official Info.</h4>

                                    <table class="table table-sm">
                                        <tr>
                                            <th>Unique Code</th>
                                            <td>
                                                {{ $employee->unique_Code }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Employee Code</th>
                                            <td>
                                                {{ $employee->employee_code }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>Official Email</th>
                                            <td>
                                                {{ $employee->official_email }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>Official Contact No</th>
                                            <td>
                                                {{ $employee->official_contact_no }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>Department</th>
                                            <td>
                                                {{ $employee->depertment_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Designation</th>
                                            <td>
                                                {{ $employee->designation_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Employee Category</th>
                                            <td>
                                                {{ $employee->category_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Sub-department</th>
                                            <td>
                                                {{ $employee->section_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Job Placement</th>
                                            <td>
                                                {{ $employee->location_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Working Shift</th>
                                            <td>
                                                {{ $employee->shift_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Overtime Status</th>
                                            <td>
                                                {{ $employee->overtime_status == 1 ? 'Yes' : 'No' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Insurance</th>
                                            <td>
                                                {{ $employee->insurance == 1 ? 'Yes' : 'No' }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Plant / Work Place</th>
                                            <td>
                                                {{ $employee->plant_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Reporting to</th>
                                            <td>
                                                {{ $employee->manage_by_name }}
                                            </td>
                                        </tr>

                                        {{-- @if (config('module_config.payroll_module') == 1)
                                            <tr>
                                                <th>Salary Grade</th>
                                                <td>
                                                    {{ $employee->grade_name }}
                                                </td>
                                            </tr>
                                        @endif --}}

                                        <tr>
                                            <th>Gross Salary</th>
                                            <td>
                                                {{ $employee->basic_salary }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Employee Status</th>
                                            <td>
                                                {{ $employee->employeestatus_name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Joining Date</th>
                                            <td>
                                                {{ date('d-m-Y', strtotime(str_replace('-', '/', $employee->joining_date ))) }}
                                            </td>
                                        </tr>

                                        @if ($employee->employeestatus_id == 1)
                                            <tr>
                                                <th>Probation Period</th>
                                                <td>
                                                    {{ $employee->period }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Confirmation Date</th>
                                                <td>
                                                    {{ date('d-m-Y', strtotime(str_replace('-', '/', $employee->confirmation_date ))) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Job Duration</th>
                                                <td>
                                                    {{ $employee->jobduration }}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="forward">Forward <span style="color: red">*</span></label>
                                <select id="forward" class="form-control" name="forward" required>
                                    <option value="1" selected>Forward</option>
                                    <option value="2">Final Decision</option>
                                </select>

                                @error('forward')
                                    <span style="color: red">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="forward_class">
                                <label for="forward_employee_name">Approve By(Forward) <span style="color: red">*</span></label>
                                <select style="width: 100%;" id="forward_employee_name" name="forward_employee_name">
                                </select>

                                @error('forward_employee_name')
                                    <span style="color: red">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comment">Comment <span style="color: red">*</span></label>
                                <textarea class="form-control" placeholder="Write Comment" id="comment" name="comment" rows="5"></textarea>

                                @error('comment')
                                    <span style="color: red">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="panel" style="border: 1px solid #00000012; box-shadow: none;">
                        <h4 class="panel-header" style="padding-left: 10px; font-weight: 600">History of Approval</h4>
                        <hr style="margin-top: 10px; margin-bottom: 10px;">

                        <div class="panel-body">
                            @foreach ($approvalHistories as $history)
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div>
                                            <strong>{{ $loop->index+1 }}.</strong> Forward to <strong>{{ $history->forward_to }}</strong> by <strong>{{ $history->forward_by }}</strong> at <strong>{{ date('d-m-Y | h:ia', strtotime($history->action_time)) }}</strong>
                                        </div>

                                        <div style="padding-left: 18px"><strong>Comment:</strong> {{ $history->comment }}</div>
                                    </div>
                                </div>

                                @if (! $loop->last)
                                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @can('EmployeeJoiningApprovalAction')
                <div class="modal-footer" style="padding-right: 0;">
                    <button type="submit" class="btn btn-sm btn-success" id="action" name="action" value="1">Accept</button>
                    <button type="submit" class="btn btn-sm btn-danger" id="reject" name="action" value="2">Reject</button>
                </div>
            @endcan
        </form>
	</div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready(function($) {
    $('#forward_employee_name').select2({
        placeholder: 'Enter Forward Person Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('/userlist') }}",
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
                }
            },

            cache: true
        }
    });

    $('.forward_class').hide();

    if($("#forward").val() == 1) {
        $('.forward_class').show();
    } else {
        $('.forward_class').hide();
    }

    $('#forward').on('change', function() {
        if($("#forward").val() == 1) {
            $('.forward_class').show();
        } else {
            $('.forward_class').hide();
        }
    });
});
</script>
@endsection
