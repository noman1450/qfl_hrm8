<!-- create_category -->

@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Leave Type</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('leavetype')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('leave_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Leave Type </label>
	                <input type="text" class="form-control" name="leave_type" placeholder="Ex. Sick Leave.." value="{{ old('leave_type') }}" required autofocus >
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
	           

	                <select class="form-control" name="leave_status">
	                	<option value="1">Company Policy</option>
	                	<option value="2">Holiday Against Leave</option>
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
	                <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
	            </div>
	        </div>			
        </div>			
	</div>

	</form>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#month_from').select2({
    	placeholder: 'Enter Month From',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/monthlist",
		        delay: 250,    			
    			data: function(params) {
		          return {
		            term: params.term
		          }
    			},
		        processResults: function (data, params) {
		          params.page = params.page || 1;
		          return {
		            results: data,
		            pagination: {
		              more: (params.page * 30) < data.total_count
		            }
		          };
		        },
		        cache: true    			
    		}
    });


 $('#month_to').select2({
    	placeholder: 'Enter Month To',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/monthlist",
		        delay: 250,    			
    			data: function(params) {
		          return {
		            term: params.term
		          }
    			},
		        processResults: function (data, params) {
		          params.page = params.page || 1;
		          return {
		            results: data,
		            pagination: {
		              more: (params.page * 30) < data.total_count
		            }
		          };
		        },
		        cache: true    			
    		}
    });

});
</script>
@endsection
