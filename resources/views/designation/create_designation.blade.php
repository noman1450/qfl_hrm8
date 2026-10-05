<!-- create_designation -->
@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Designation</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('designation')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Designation</label>
	                <input type="text" class="form-control" name="designation" placeholder="Designation.." value="{{ old('designation') }}" required autofocus >
		            @if ($errors->has('designation'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('designation') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>			
	        <div class="form-group has-feedback {{ $errors->has('alis') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Alis</label>
	                <input type="text" class="form-control" name="alis" placeholder="Alis.." value="{{ old('alis') }}" required>
		            @if ($errors->has('alis'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('alis') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>	

	        <div class="form-group has-feedback {{ $errors->has('priority') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Priority</label>
	                <input type="text" class="form-control" name="priority" placeholder="Priority.." value="{{ old('priority') }}" required>
		            @if ($errors->has('priority'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('priority') }}</strong>
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

	</form>
</div>
@endsection


@section('script')
@endsection
