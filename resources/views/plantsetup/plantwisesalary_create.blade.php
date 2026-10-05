@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Plant Wise Salary </h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('plantwisesalary')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	

	        <div class="form-group has-feedback {{ $errors->has('plant_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Plant Name</label>
					<select class="form-control" id= "plant_name" name="plant_name">
					</select>
	                
	            </div>
	        </div>	


	        <div class="form-group has-feedback {{ $errors->has('hrm_plant_with_section_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Select Salary Category</label>
					<select class="form-control" id= "hrm_plant_with_section_id" name="hrm_plant_with_section_id">
					</select>
	                
	            </div>
	        </div>	




	        <div class="form-group has-feedback {{ $errors->has('month_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Month Name</label>
					<select class="form-control" id= "month_name" name="month_name">
						@foreach ($month as $keys)
							<option value={{$keys->id}}>{{$keys->month_name}}</option>
						@endforeach
					</select>
	                
	            </div>
	        </div>


	        <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Year</label>
					<select class="form-control" id= "year" name="year">
					</select>
	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('days') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Days</label>
					<input type="text" class="form-control"    name="days" placeholder="Days">
	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('minimum_rate') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Partial Attendance Rate</label>
					<input type="text" class="form-control"  name="minimum_rate" placeholder="Partial Attendance Rate">
	                
	            </div>
	        </div>



	        <div class="form-group has-feedback {{ $errors->has('maximum_rate') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Full Attendance Rate</label>
					<input type="text" class="form-control"  name="maximum_rate" placeholder="Full Attendance Rate">
	                
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

  for (i = new Date().getFullYear(); i > 2015; i--){
    $('#year').append($('<option />').val(i).html(i));
  }

 });


	 $('#plant_name').select2({
      placeholder: 'Enter a Plant Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/plantname_list_data',
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



	 $('#hrm_plant_with_section_id').select2({
      placeholder: 'Enter a Salary Category',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/plantwithsection_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term,
            hrm_plant_id:$("#plant_name").val(),
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
