<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Previous Employee Shift Change</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('submitprechangeemployeeshift')}}">
        {{ csrf_field() }}


	<div class="box-body">
		<div class="row">


			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">


		        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Employee Name</label>
		            <div class="col-lg-9">
						<select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required autofocus >
						</select>
			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Department</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="department" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Designation</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="designation" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Current Shift</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="current_shift" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">New Shift</label>
		            <div class="col-lg-9">
		                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" required>
		                </select>
			            @if ($errors->has('working_shift'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('working_shift') }}</strong>
			                </span>
			            @endif 
		            </div>
		        </div>


		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Previous Date</label>
		            <div class="col-lg-9">
			            <div class="input-group date">
			                <div class="input-group-addon">
			                  <i class="fa fa-calendar"></i>
			                </div>		            
			                <input type="text" class="form-control pull-right" id="start_date" name="start_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('start_date') }}" required readonly>
				            @if ($errors->has('start_date'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('start_date') }}</strong>
				                </span>
				            @endif 	                
			            </div>
		            </div>
		        </div>



				<div class="form-group  col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-12">
						<input type="submit" class="btn btn-success block btn-flat pull-right" value="Submit">
					</div>
				</div>

		</div>
	</div>


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

    $('#start_date').datepicker({
      // startDate: new Date() ,	
      autoclose: true
    });     

    $employee = $('#employee_name').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/join_employee_list",
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
    	$("#department").val($(this).select2('data')['0']['depertment_name']);
    	$("#designation").val($(this).select2('data')['0']['designation_name']);
    	$("#current_shift").val($(this).select2('data')['0']['shift_name']);
    });

    $employee.on("select2:unselect", function (e) { 
    	$("#department").val('');
    	$("#designation").val('');
    	$("#current_shift").val('');
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


});
</script>
@endsection
