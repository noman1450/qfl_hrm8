<!-- edit_section -->
@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Sub-department</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('section.update', $edit_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">
	        <div class="form-group has-feedback {{ $errors->has('section') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Sub-department Name</label>
	                <input type="text" class="form-control" name="section" placeholder="Sub-department Name.." value="{{ $edit_data->section_name ?? old('section') }}" required autofocus >
		            @if ($errors->has('section'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('section') }}</strong>
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
