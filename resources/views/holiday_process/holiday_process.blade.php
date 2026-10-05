<!-- attendance_data_process -->
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
		<h3 class="box-title">Holiday Process</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    {!! Form::open(array('route' => 'holiday_process.store','method'=>'POST')) !!}
        {{ csrf_field() }}
		<div class="box-body">
			<div class="row">

				<div class="col-lg-12 col-md-12 col-xs-12 form-group">  
			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }}">  
			        	<label control-label">Location</label>
			            <select class="form-control" id="location" name="location" style="width: 100%;" required>
			            </select>
			            @if ($errors->has('location'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('location') }}</strong>
			                </span>
			            @endif 		                	                
			        </div> 
		        </div> 

				<div class="col-lg-12 col-md-12 col-xs-12 form-group">  
			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('holiday_name') ? ' has-error' : '' }}">  
			        	<label control-label">Holiday Name</label>
			            <select class="form-control" id="holiday_name" name="holiday_name" style="width: 100%;" required>
			            </select>
			            @if ($errors->has('holiday_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('holiday_name') }}</strong>
			                </span>
			            @endif 		                	                
			        </div> 
		        </div> 

		        <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
			        <div class="col-lg-3 col-md-3 col-xs-12 form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }}">  
		            	<label control-label">Date From</label>
			            <div class="input-group date">
			                <div class="input-group-addon">
			                  <i class="fa fa-calendar"></i>
			                </div>		            
			                <input type="text" class="form-control pull-right" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('joining_date') }}" required readonly>
				            @if ($errors->has('date_from'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('date_from') }}</strong>
				                </span>
				            @endif 	                
			            </div>	                
			        </div>

			        <div class="col-lg-3 col-md-3 col-xs-12 form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }}">  
		            	<label control-label">Date To</label>
			            <div class="input-group date">
			                <div class="input-group-addon">
			                  <i class="fa fa-calendar"></i>
			                </div>		            
			                <input type="text" class="form-control pull-right" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('joining_date') }}" required readonly>
				            @if ($errors->has('date_to'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('date_to') }}</strong>
				                </span>
				            @endif 	                
			            </div>	                
			        </div>
		        </div>

		        <div class="col-lg-12 col-md-12 col-xs-12 form-group"> 
		        	<div class="col-lg-6 col-md-6 col-xs-12 form-group">  
		        		<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" style="margin-right: 0px;">
		        	</div>
		        </div>
			</div>
		</div>
	{!! Form::close() !!}

</div>

@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#date_from').datepicker({
      autoclose: true
    });

    $('#date_to').datepicker({
      autoclose: true
    });




	$('#location').select2({
      placeholder: 'Enter Branch location',
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


    $('#holiday_name').select2({
      placeholder: 'Enter a Holiday Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/holiday_list_data',
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