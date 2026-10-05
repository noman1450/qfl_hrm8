<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Long Service Employee</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>




    {!! Form::open(array('route'=>'employeelongservice.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">

		        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Employee Name</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
							<option value="{{$data[0]->id}}">{{$data[0]->employee_name}}</option>
						</select>

			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>

<!-- 		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Department</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="department" value="{{$data[0]->depertment_name }}" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Designation</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="designation" value="{{$data[0]->designation_name }}" readonly>
		            </div>
		        </div> -->


               <div class="form-group has-feedback {{ $errors->has('joining_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Joining Date</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="joining_date" name="joining_date" data-date-format="dd-mm-yyyy" value="{{$data[0]->joining_date}}"  required readonly>
			            @if ($errors->has('joining_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('joining_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>

            <div class="form-group has-feedback {{ $errors->has('confirmation_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Confirmation Date</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="confirmation_date" name="confirmation_date" data-date-format="dd-mm-yyyy" value="{{$data[0]->confirmation_date}}"  required readonly>
			            @if ($errors->has('confirmation_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('confirmation_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>

		        <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Current Duration</label>
		            <div class="col-lg-9">
		   			  <input class="form-control" type="text" name="jobduration" value="{{$data[0]->jobduration}}" readonly="">                    
		            </div>
		        </div>

 		        <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Duration(Years)</label>
		            <div class="col-lg-9">
		            	<select class="form-control" name="duration">
		            		<option value="5">5</option>
		            		<option value="10">10</option>
		            		<option value="15">15</option>
		            		<option value="20">20</option>
		            		<option value="25">25</option>
		            	</select>
		            </div>
		        </div>


               <div class="form-group has-feedback {{ $errors->has('service_completed_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Service Completed Date</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="service_completed_date" name="service_completed_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('service_completed_date') }}" required readonly>
			            @if ($errors->has('service_completed_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('service_completed_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Payble Amount</label>
		            <div class="col-lg-9">
		   			  <input class="form-control" type="text" name="payble_amount" placeholder="Payble Amount">                    
		            </div>
		        </div>


              <div class="form-group has-feedback {{ $errors->has('payment_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Payment Date</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="payment_date" name="payment_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('payment_date') }}" required readonly>
			            @if ($errors->has('payment_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('payment_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>



		        <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Comment</label>
		            <div class="col-lg-9">
		   			  <textarea class="form-control" rows="4" width="100%" name="comment" placeholder="Comment."></textarea>                      
		            </div>
		        </div>

			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat" value="Submit">
					<span></span>
				</div>
			</div>

		</div>
	</div>


	{!! Form::close() !!}
</div>
@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#applied').datepicker({
      autoclose: true
    });

    $('#service_completed_date').datepicker({
      autoclose: true
    });
    $('#payment_date').datepicker({
      minDate: new Date(2019,02,28),
      autoclose: true
    });    

// $('#date_range').daterangepicker({
// "locale": {
//         "format": "DD/MM/YYYY",
//         startDate: new Date('2018-1-5'),
//     	endDate: new Date('2018-1-12')
//       }
// });

 // $employee = $('#employee_name').select2({
 //    	placeholder: 'Enter an Employee Name',
 //    	allowClear: true,
 //    		ajax: {
	// 	        dataType: 'json',
	// 	        url: "{{URL::to('/')}}/join_employee_list",
	// 	        delay: 250,    			
 //    			data: function(params) {
	// 	          return {
	// 	            term: params.term
	// 	          }
 //    			},
	// 	        processResults: function (data, params) {
	// 	          params.page = params.page || 1;
	// 	          return {
	// 	            results: data,
	// 	            pagination: {
	// 	              more: (params.page * 30) < data.total_count
	// 	            }
	// 	          };
	// 	        },
	// 	        cache: true    			
 //    		}
 //    });

 //    $employee.on("select2:select", function (e) {
 //    	$("#department").val($(this).select2('data')['0']['depertment_name']);
 //    	$("#designation").val($(this).select2('data')['0']['designation_name']);
 //    	$("#current_shift").val($(this).select2('data')['0']['shift_name']);
 //    });

 //    $employee.on("select2:unselect", function (e) { 
 //    	$("#department").val('');
 //    	$("#designation").val('');
 //    	$("#current_shift").val('');
 //    });





});
</script>
@endsection
