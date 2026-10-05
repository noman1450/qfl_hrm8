<!-- edit_location -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Location</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('location.update', $edit_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Location Name</label>
	                <input type="text" class="form-control" name="location" placeholder="Location Name.." value="{{ $edit_data->location_name ?? old('location') }}" required autofocus >
		            @if ($errors->has('location'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('location') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Description</label>
	                <input type="text" class="form-control" name="description" placeholder="Description.." value="{{ $edit_data->location_description ?? old('description') }}" required autofocus >
		            @if ($errors->has('description'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('description') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Location Type</label>
	              	<select name="location_type" class="form-control">
	              		@if($edit_data->location_type==1)
	              			<option value="1" selected>Head Office</option>
	              			<option value="2">Factory / Branch</option>
	              			<option value="3">Depot / Sub-branch</option>
	              		@elseif($edit_data->location_type==2)
	              			<option value="1">Head Office</option>
	              			<option value="2"selected>Factory / Branch</option>
	              			<option value="3">Depot / Sub-branch</option>
	              		@else
	              			<option value="1">Head Office</option>
	              			<option value="2">Factory / Branch</option>
	              			<option value="3"selected>Depot / Sub-branch</option>
	              		@endif

	              	</select>
		            @if ($errors->has('description'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('description') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>





		</div>
	</div>

	<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Update</button>
	            </div>
	        </div>
        </div>
	</div>

	{!! Form::close() !!}
</div>
@endsection


@section('script')


<script>
</script>

@endsection
