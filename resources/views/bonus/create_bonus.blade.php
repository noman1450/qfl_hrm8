<!-- create_bloodgroup -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Bonus</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'bonus.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_bloodgroup')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('bonus_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Bonus Name</label>
	                <input type="text" class="form-control" name="bonus_name" placeholder="Bonus Name.." value="{{ old('bonus_name') }}" required autofocus >
		            @if ($errors->has('bonus_name'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('bonus_name') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('bonus_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Bonus Description</label>
	                <input type="text" class="form-control" name="bonus_description" placeholder="Bonus Description.." value="{{ old('bonus_description') }}"  autofocus >
		            @if ($errors->has('bonus_description'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('bonus_description') }}</strong>
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
	                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Submit</button>
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
