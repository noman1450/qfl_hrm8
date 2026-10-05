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
		<h3 class="box-title">Edit Leave Type Year</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('leavetypeyear.update', $edit_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


			<div class="col-lg-4 col-md-4 col-xs-12 personal-info">


               <div class="form-group has-feedback {{ $errors->has('leave_year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-4 control-label">Year</label>
		            <div class="col-lg-8">
		            <div class="input-group">
		               <select  class="form-control" name="leave_year" style="width: 100%;" >
						
							@foreach ($leaveyear as $keys)
								@if ($edit_data[0]->leave_year_id== $keys->id)
								<option value={{$keys->id}} selected>{{$keys->leave_year}}</option>
								@else
								<option value={{$keys->id}}>{{$keys->leave_year}}</option>
								@endif
							@endforeach


						</select>          
		            </div>
		            </div>
		        </div>




               <div class="form-group has-feedback {{ $errors->has('leave_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-4 control-label">Leave Type</label>
		            <div class="col-lg-8">
		            <div class="input-group">
		               <select  class="form-control" name="leave_type" style="width: 100%;" >
						

							@foreach ($leavetype as $keys)
								@if ($edit_data[0]->leave_type_id== $keys->id)
								<option value={{$keys->id}} selected>{{$keys->leave_type}}</option>
								@else
								<option value={{$keys->id}}>{{$keys->leave_type}}</option>
								@endif
							@endforeach





						</select>          
		            </div>
		            </div>
		        </div>



		         <div class="form-group has-feedback {{ $errors->has('days') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-4 control-label">Days</label>
		            <div class="col-lg-8">
		            <div class="input-group">
			       <input class="form-control" type="days" name="days" value={{$edit_data[0]->leave_days}} required>     	                
		            </div>
		            </div>
		        </div>


			<div class="form-group">

					<input type="submit" class="btn btn-success block btn-flat" style="margin-left: 340px;"  value="Submit">

					<span></span>
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


    $('#date_from').datepicker({
      autoclose: true
    });
    $('#date_to').datepicker({
      autoclose: true
    });



  for (i = new Date().getFullYear(); i > 2015; i--){
    $('#year').append($('<option />').val(i).html(i));
  }





});
</script>
@endsection
