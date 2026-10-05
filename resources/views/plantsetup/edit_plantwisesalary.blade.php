<!-- create_designation -->
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


<form  method="POST" action="{{url('update_plantwise_salary')}}" Id="frm_money_receipt">	

        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

			<input type="hidden" name="id"  value="{{$edit_data[0]->id}}">

	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Plant Name</label>
					<select class="form-control" id= "plant_name" name="plant_name">
                     	<option value="{{$edit_data[0]->hrm_plant_id}}">{{$edit_data[0]->plant_name}}</option>
					</select>
	                
	            </div>
	        </div>	

	        <div class="form-group has-feedback {{ $errors->has('hrm_plant_with_section_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Salary Category</label>
					<select class="form-control" id= "hrm_plant_with_section_id" name="hrm_plant_with_section_id">
                     	<option value="{{$edit_data[0]->hrm_plant_with_section_id}}">{{$edit_data[0]->section_name}}</option>
					</select>
	                
	            </div>
	        </div>	


	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Month Name</label>
					<select class="form-control" id= "month_name" name="month_name">
						@foreach ($month as $keys)
							 @if ($edit_data[0]->hrm_month_id == $keys->id)
							<option value="{{$keys->id}}" selected>{{$keys->month_name}}</option>
							@else
							<option value="{{$keys->id}}">{{$keys->month_name}}</option>
							@endif
						@endforeach
					</select>
	                
	            </div>
	        </div>


	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Year</label>
					<select class="form-control" id= "year" name="year">
                     	<option value="{{$edit_data[0]->year_id}}">{{$edit_data[0]->year_id}}</option>
					</select>
	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Days</label>
					<input type="text" class="form-control"    name="days"   placeholder="Days"  value="{{$edit_data[0]->working_days}}">
	                
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Minimum Rate</label>
					<input type="text" class="form-control"  name="minimum_rate"    placeholder="Minimum Rate" value="{{$edit_data[0]->minimum_rate}}">
	                
	            </div>
	        </div>


	        <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
    			<label>Maximum Rate</label>
					<input type="text" class="form-control"  name="maximum_rate" placeholder="Maximum Rate" value="{{$edit_data[0]->max_rate}}">
	                
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
