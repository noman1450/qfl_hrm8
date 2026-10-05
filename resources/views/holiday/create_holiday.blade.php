<!-- create_designation -->
@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Holiday</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('holiday')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('holiday_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Holiday Name</label>
	                <input type="text" class="form-control" name="holiday_name" placeholder="Holiday Name.." value="{{ old('holiday_name') }}" required autofocus >
		            @if ($errors->has('holiday_name'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('holiday_name') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>			
	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Holiday Description</label>
	                <input type="text" class="form-control" name="holiday_description" placeholder="Description.." value="{{ old('holiday_description') }}" >
		            @if ($errors->has('holiday_description'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('holiday_description') }}</strong>
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
