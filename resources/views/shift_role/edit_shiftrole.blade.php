<!-- edit_location -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Shift Role Name</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('shiftrole.update', $edit_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('shift_role_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Shift Role Name</label>
	                <input type="text" class="form-control" name="shift_role_name" value="{{ $edit_data[0]->shift_role_name}}" required autofocus >
		            @if ($errors->has('shift_role_name'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('shift_role_name') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>


	        <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  

	            <div class="col-lg-6">  
	              	<label>Location Name</label>


					<select class="form-control" id= "location" name="location" ">
						
                     	<option value="{{$edit_data[0]->hrm_location_id}}">{{$edit_data[0]->location_name}}</option>               	

					</select>

		            @if ($errors->has('location'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('location') }}</strong>
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


<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>




	 $('#location').select2({
      placeholder: 'Enter a location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });
</script>
@endsection
