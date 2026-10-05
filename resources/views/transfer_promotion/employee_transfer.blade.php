@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- <link rel="stylesheet" href="../../bower_components/select2/dist/css/select2.min.css"> -->
<style>
img{
width: 210px;
height: 210px;
}
input[type=file]{
padding:10px;
/*background:#2d2d2d;*/
}
</style>
@endsection
@section('content')
<div class="box box-primary">
  <div class="box-header with-border">
    <h2 class="box-title" style="color: blue;">Employee Transfer Create</h2>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>


  <form  method="POST" action="{{url('employee_transfer')}}"> 
  {{ csrf_field() }}
  <div class="box-body">





    <div class="row">
      
      <div  class="col-md-6">
          <div class="col-xs-12 form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }}">
            <label class="control-label">Employee Name</label>
            <select class="form-control select2" id="employee_name" name="employee_name" style="width: 100%;" >
            </select>
            @if ($errors->has('employee_name'))
            <span class="help-block">
              <strong>{{ $errors->first('employee_name') }}</strong>
            </span>
            @endif
          </div>
      </div>
    </div>


    <div class="row">
      
      <div class="col-md-6">

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('olddepartment') ? ' has-error' : '' }}">
            <label class="control-label">Employee Code</label>
            <input type="text" class="form-control" id="oldemployeecode" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('olddepartment') ? ' has-error' : '' }}">
            <label class="control-label">Department</label>
           <input type="text" class="form-control" id="olddepartment" readonly>

          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('olddesignation') ? ' has-error' : '' }}">
            <label class="control-label">Designation</label>
             <input type="text" class="form-control" id="olddesignation" readonly>

          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldcategory') ? ' has-error' : '' }}">
            <label class="control-label">Employee Category</label>
           <input type="text" class="form-control" id="oldcategory" name="oldcategory" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldjob_location') ? ' has-error' : '' }}">
            <label class="control-label">Job Location</label>
             <input type="text" class="form-control" id="oldjob_location" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldSection') ? ' has-error' : '' }}">
            <label class="control-label">Section</label>
           <input type="text" class="form-control" id="oldSection" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldworking_shift') ? ' has-error' : '' }}">
            <label class="control-label">Working Shift</label>
             <input type="text" class="form-control" id="oldworking_shift" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldovertime') ? ' has-error' : '' }}">
            <label class="control-label">Overtime</label>
            <select class="form-control" id="oldovertime" name="oldovertime" style="width: 100%;" required disabled>
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
            
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldovertime') ? ' has-error' : '' }}">
            <label class="control-label">Insurance</label>
            <select class="form-control" id="oldinsurance" name="oldinsurance" style="width: 100%;" required disabled>
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
            
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldmanage_by') ? ' has-error' : '' }}">
            <label class="control-label">Reporting To</label>
            <input type="text" class="form-control" id="oldmanage_by" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldemployeestatus') ? ' has-error' : '' }}">
            <label class="control-label">Employee Status</label>
           <input type="text" class="form-control" id="oldemployeestatus" readonly>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldbasic_salary') ? ' has-error' : '' }}">
            <label class="control-label">Basic Salary</label>
            <input type="text" class="form-control" id="oldbasic_salary" name="oldbasic_salary" required readonly >
            
          </div>

        @if (Config::get('module_config.payroll_module') == 1)
          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldbasic_salary') ? ' has-error' : '' }}">
            <label class="control-label">Salary Grade</label>
            <input type="text" class="form-control" id="oldsalary_grade" name="oldsalary_grade" required readonly >           
          </div>
       @endif

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldjoining_date') ? ' has-error' : '' }}">
            <label class="control-label">Joining Date</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="oldjoining_date" name="oldjoining_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('oldjoining_date') }}" required readonly>
            </div>
          </div>

          <div class="col-xs-12 form-group has-feedback {{ $errors->has('oldconfirmation_date') ? ' has-error' : '' }}">
            <label class="control-label">Confirmation Date</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="oldconfirmation_date" name="oldconfirmation_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('oldconfirmation_date') }}" required readonly>
            </div>
          </div>
      </div>
      <div class="col-md-6">

         <div class="form-group has-feedback {{ $errors->has('depertment') ? ' has-error' : '' }}">
            <label class="control-label">Employee Code(NEW)</label>
            <input type="text" class="form-control" name="employee_code" id="employee_code" placeholder="Employee Code" required>
          </div>

          <div class="form-group has-feedback {{ $errors->has('depertment') ? ' has-error' : '' }}">
            <label class="control-label">Department(NEW)</label>
            <select class="form-control select2" id="depertment" name="depertment" style="width: 100%;" >
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }}">
            <label class="control-label">Designation(NEW)</label>
            <select class="form-control select2" id="designation" name="designation" style="width: 100%;" >
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('category') ? ' has-error' : '' }}">
            <label class="control-label">Employee Category (NEW)</label>
            <select class="form-control select2" id="category" name="category" style="width: 100%;" >
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('job_location') ? ' has-error' : '' }}">
            <label class="control-label">Job Location(NEW)</label>
            <select class="form-control select2" id="job_location" name="job_location" style="width: 100%;" >
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('section') ? ' has-error' : '' }}">
            <label class="control-label">Section(NEW)</label>
            <select class="form-control select2" id="section" name="section" style="width: 100%;" >
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('working_shift') ? ' has-error' : '' }}">
            <label class="control-label">Working Shift(NEW)</label>
            <select class="form-control select2" id="working_shift" name="working_shift" style="width: 100%;" >
            </select>
          </div>

          <div class=" form-group has-feedback {{ $errors->has('overtime') ? ' has-error' : '' }}">
            <label class="control-label">Overtime(NEW)</label>
            <select class="form-control" id="overtime" name="overtime" style="width: 100%;" required>
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>

          <div class=" form-group has-feedback {{ $errors->has('insurance') ? ' has-error' : '' }}">
            <label class="control-label">Insurance(NEW)</label>
            <select class="form-control" id="insurance" name="insurance" style="width: 100%;" required>
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>

          <div class="form-group has-feedback {{ $errors->has('plant') ? ' has-error' : '' }}">
            <label class="control-label">Work Place/Plant (NEW)</label>
            <select class="form-control select2" id="plant_name" name="plant_name" style="width: 100%;" >
            </select>
          </div>


          <div class="form-group has-feedback {{ $errors->has('manage_by') ? ' has-error' : '' }}">
            <label class="control-label">Reporting To (NEW)</label>
            <select class="form-control select2" id="manage_by" name="manage_by" style="width: 100%;" >
            </select>
          </div>


          <div class="form-group has-feedback {{ $errors->has('employeestatus') ? ' has-error' : '' }}">
            <label class="control-label">Employee Status(NEW)</label>
            <select class="form-control select2" id="employeestatus" name="employeestatus" style="width: 100%;" >
            </select>
          </div>

         
          <div class="form-group has-feedback {{ $errors->has('basic_salary') ? ' has-error' : '' }}">
            <label class="control-label">Basic Salary(NEW)</label>
            <input type="text" class="form-control" name="basic_salary" placeholder="Basic Salary.." value="{{ old('basic_salary') }}" required  >
            
          </div>
        @if (Config::get('module_config.payroll_module') == 1)
          <div class="form-group has-feedback {{ $errors->has('salary_grade') ? ' has-error' : '' }}">
            <label class="control-label">Salary Grade(NEW)</label>
            <select class="form-control select2" id="salary_grade" name="salary_grade" style="width: 100%;" >
            </select>
            
          </div>
        @endif


          <div class="form-group has-feedback {{ $errors->has('note') ? ' has-error' : '' }}">
            <label class="control-label">Comment</label>
            <input type="text" class="form-control" name="comment" placeholder="Comment" value="{{ old('note') }}" required  >
            
          </div>


          <div class="form-group has-feedback {{ $errors->has('depertment') ? ' has-error' : '' }}">
            <label class="control-label">Transfer Date</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                  </div>
                   <input type="text" class="form-control pull-right" id="transfer_date" name="transfer_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('transfer_date') }}" required readonly>
                </div> 
          </div>


         <div class="form-group">
                    <input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" style="margin-right: 10px;">
         </div>



        </div>
    </div>
  </div>  
  <input type="text" id="hrm_employee_job_info_id" name="hrm_employee_job_info_id" class="hidden">
</form>
</div>


  @endsection
  @section('script')
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script>


$(document).ready(function($) {
     $('#transfer_date').datepicker({
      autoclose: true
    });

    var $employee = $('#employee_name').select2({
      placeholder: 'Enter an Employee Name',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/getemployeejobinfo_details",
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

    $employee.on("select2:select", function (e) {

      $("#oldemployeecode").val($(this).select2('data')['0']['employee_code']);
      $("#oldinsurance").val($(this).select2('data')['0']['insurance']);
      $("#olddepartment").val($(this).select2('data')['0']['depertment_name']);
      $("#olddesignation").val($(this).select2('data')['0']['designation_name']);
      $("#oldcategory").val($(this).select2('data')['0']['category_name']);
      $("#oldjob_location").val($(this).select2('data')['0']['location_name']);
      $("#oldSection").val($(this).select2('data')['0']['section_name']);
      $("#oldworking_shift").val($(this).select2('data')['0']['shift_name']);
      $("#oldovertime").val($(this).select2('data')['0']['overtime_status']);
      $("#oldmanage_by").val($(this).select2('data')['0']['manage_by_name']);
      $("#oldemployeestatus").val($(this).select2('data')['0']['employeestatus_name']);
      $("#oldbasic_salary").val($(this).select2('data')['0']['basic_salary']);
      $("#oldjoining_date").val($(this).select2('data')['0']['joining_date']);
      $("#oldconfirmation_date").val($(this).select2('data')['0']['confirmation_date']);
      $("#hrm_employee_job_info_id").val($(this).select2('data')['0']['hrm_employee_job_info_id']);
      $("#oldsalary_grade").val($(this).select2('data')['0']['grade_name']);
     
    });

    $employee.on("select2:unselect", function (e) { 
      $("#olddepartment").val('');
      $("#olddesignation").val('');
      $("#oldcategory").val('');
      $("#oldjob_location").val('');
      $("#oldSection").val('');
      $("#oldworking_shift").val('');
      $("#oldovertime").val('');
      $("#oldmanage_by").val('');
      $("#oldemployeestatus").val('');
      $("#oldbasic_salary").val('');
      $("#oldjoining_date").val('');
      $("#oldconfirmation_date").val('');
      $("#hrm_employee_job_info_id").val('');
      $("#oldsalary_grade").val('');
    
    });



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
      placeholder: 'Enter Employee Category',
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
      placeholder: 'Enter Job location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data_all',
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
      placeholder: 'Enter section',
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









});
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
  
</script>


  @endsection