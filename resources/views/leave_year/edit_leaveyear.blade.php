<!-- edit_depertment -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Leave Type</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('leaveyear.update', $edit_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

<div class="col-md-8">


  <div class="form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-2 control-label">Date From</label>
		            <div class="col-lg-4">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>

			              <input type="text" class="form-control pull-right" id="date_from" name="date_from" data-date-format="dd-mm-yyyy"  value="{{  date('d-m-Y', strtotime(str_replace('-', '/', $edit_data->date_from ))) }}"  required readonly>


			            @if ($errors->has('date_from'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('date_from') }}</strong>
			                </span>
			            @endif
		            </div>
		            </div>
		        </div>




		         <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-2 control-label">Date To</label>
		            <div class="col-lg-4">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>
		               <input type="text" class="form-control pull-right" id="date_to" name="date_to" data-date-format="dd-mm-yyyy"  value="{{  date('d-m-Y', strtotime(str_replace('-', '/', $edit_data->date_to ))) }}"  required readonly>

			            @if ($errors->has('date_to'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('date_to') }}</strong>
			                </span>
			            @endif
		            </div>
		            </div>
		        </div>



		         <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-2 control-label">Leave Year</label>
		            <div class="col-lg-4">
		            <div class="input-group date">
                        <input class="form-control" type="text" id="leave_year" value="{{$edit_data->leave_year}}" name="leave_year"    >
		            </div>
		            </div>
		        </div>





		</div>
	</div>

	<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	                <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Update</button>
	            </div>
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


    $('#date_from').datepicker({
      autoclose: true
    });
    $('#date_to').datepicker({
      autoclose: true
    });

});
</script>



@endsection
