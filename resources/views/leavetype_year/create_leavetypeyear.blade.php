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
		<h3 class="box-title">Create Leave Type Year</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'leavetypeyear.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


			<div class="col-lg-6 col-md-6 col-xs-12 personal-info">


               <div class="form-group has-feedback {{ $errors->has('leave_year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Year</label>
		            <div class="col-lg-9">
		            <!-- <div class="input-group"> -->
		               <select  class="form-control col-lg-12" name="leave_year" style="width: 100%;" >
							@foreach ($leavetypeyear as $keys)
							<option value={{$keys->id}}>{{$keys->leave_year}}</option>
							@endforeach
						</select>          
		            <!-- </div> -->
		            </div>
		        </div>




               <div class="form-group has-feedback {{ $errors->has('leave_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Leave Type</label>
		            <div class="col-lg-9">
		            <!-- <div class="input-group"> -->
		               <select  class="form-control col-lg-12" name="leave_type" style="width: 100%;" >
						
							@foreach ($leavetype as $keys)
							<option value={{$keys->id}}>{{$keys->leave_type}}</option>
							@endforeach
						</select>          
		            <!-- </div> -->
		            </div>
		        </div>



		         <div class="form-group has-feedback {{ $errors->has('days') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Days</label>
		            <div class="col-lg-9">
		            <!-- <div class="input-group"> -->
			       	<input class="form-control col-lg-12" type="days" name="days" required>     	                
		            <!-- </div> -->
		            </div>
		        </div>


				<div class="col-lg-12">
					<input type="submit" class="btn btn-success block btn-flat" style="margin-left: 340px;"  value="Submit">
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
