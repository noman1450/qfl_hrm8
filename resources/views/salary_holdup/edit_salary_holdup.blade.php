<!-- create_designation -->
@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Salary Holdup Type</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {{-- <form class="form-horizontal" method="PUT" action="{{ route('salary_holdup.update', $edit_data->id) }}"> --}}
        {!! Form::open(array('route' => array('salary_holdup.update', $edit_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('holdup_types_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Salary Holdup Name</label>
	                <input type="text" class="form-control" name="holdup_types_name" placeholder="Salary Holdup Name.." value="{{ $edit_data->holdup_types_name ?? old('holdup_types_name') }}"  autofocus >
		            @if ($errors->has('holdup_types_name'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('holdup_types_name') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>
	        <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Salary Holdup Description</label>
	                <input type="text" class="form-control" name="description" placeholder="Description.." value="{{ $edit_data->description ?? old('description') }}" >
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
	                <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
	            </div>
	        </div>
        </div>
	</div>

    {!! Form::close() !!}
</div>
@endsection



