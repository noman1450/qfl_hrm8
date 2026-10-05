<!-- edit_depertment -->
@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Leave Type</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('leavetype.update', $edit_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('leave_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Leave Type</label>
	                <input type="text" class="form-control" name="leave_type" placeholder="leave_type" value="{{ $edit_data->leave_type}}" required autofocus >
		            @if ($errors->has('leave_type'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('leave_type') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>	



	        <div class="form-group has-feedback {{ $errors->has('leave_status') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Leave Status</label>
	           


	              <select  class="form-control" name="leave_status">
								@if ($edit_data->leave_status== 1)
	                	         <option value="1" selected>Company Policy</option>
	                	         <option value="2" >Holiday Against Leave</option>

								@else
	                	         <option value="2" selected>Holiday Against Leave</option>
	                	         <option value="1" >Company Policy</option>

								 @endif 
					</select>	



		            @if ($errors->has('leave_status'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('leave_status') }}</strong>
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
	                <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Update</button>
	            </div>
	        </div>			
        </div>			
	</div>

	{!! Form::close() !!}
	

</div>
@endsection


@section('script')
@endsection
