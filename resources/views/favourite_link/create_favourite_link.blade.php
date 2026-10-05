<!-- create_religion -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Favourite Link</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'favourite_link.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_favourite_link')) !!}
    
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('title') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Title</label>
	                <input type="text" class="form-control" name="title" placeholder="Title" value="{{ old('title') }}" required autofocus >
		            @if ($errors->has('title'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('title') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>	

	        <div class="form-group has-feedback {{ $errors->has('link_address') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Link Address Name</label>
	                <input type="text" class="form-control" name="link_address" placeholder="Copy URL and Paste Here" value="{{ old('link_address') }}" required autofocus >
		            @if ($errors->has('link_address'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('link_address') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>



		</div>
	</div>

    <div class="col-lg-6 col-md-6 col-xs-12" id="alert-success">  
        <div class="col-lg-12">
        </div>
    </div
   
  <div class="col-lg-6 col-md-6 col-xs-12" id="alert-danger">  
        <div class="col-lg-12">
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

@endsection
