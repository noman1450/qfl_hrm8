<!-- create_location -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Salary Head</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'salaryhead.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_bloodgroup')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">



			<div class="form-group has-feedback {{ $errors->has('salary_head') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Salary Head Group</label>
                     <select class="form-control" id="salaryheadgroup" name="salaryheadgroup" required autofocus>                 	
                     </select>

		            @if ($errors->has('salary_head'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('salary_head') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('salary_head') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Salary Head Name</label>
	                <input type="text" class="form-control" name="salary_head" placeholder="Salary Head" value="{{ old('salary_head') }}" required >
		            @if ($errors->has('salary_head'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('salary_head') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('salary_head') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Apply For</label>
	              	<select class="form-control" name="apply_for">
	              		<option value="1">Salary Sheet</option>
	              		<option value="2">Other Special Benefit</option>
	              	</select>
        
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>

$(document).ready(function($) {

    $('#salaryheadgroup').select2({
    	placeholder: 'Enter a Salary Head Group',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/salaryheadgrouplist",
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