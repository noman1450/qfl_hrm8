<!-- edit_religion -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Religion</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('religion.update', $religion->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">
	        <div class="form-group has-feedback {{ $errors->has('religion') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Religion Name</label>
                    <input type="text" class="form-control" name="religion" placeholder="Religion Name.." value="{{ $religion->religion ?? old('religion') }}" required autofocus >

		            @if ($errors->has('religion'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('religion') }}</strong>
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
