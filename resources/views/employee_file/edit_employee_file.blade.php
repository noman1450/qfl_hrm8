<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">


<style>

</style>
@endsection


@section('content')


<div class="box box-primary">

	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee File</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
			<div class="col-md-8">
				<div class="panel-body">
					@if ($message = Session::get('success'))
						<div class="alert alert-success" role="alert">
							{{ Session::get('success') }}
						</div>
					@endif

					@if ($message = Session::get('error'))
						<div class="alert alert-danger" role="alert">
						{{ Session::get('error') }}
						</div>
					@endif

					<form style="border: 4px solid #a1a1a1;margin-top: 5px;padding: 5px;" action="{{ URL::to('edit_document_archive') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                		{{ csrf_field() }}
                		
                		<input type="hidden" name="hrm_employee_file_id" value="{{$data[0]->hrm_employee_file_id}}">

				        <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
					 
				            	<label class="control-label">Activity Date</label>
					            <div class="input-group date">
					                <div class="input-group-addon">
					                  <i class="fa fa-calendar"></i>
					                </div>		            
								<input type="text" class="form-control pull-right" id="attached_date" name="attached_date" data-date-format="dd-mm-yyyy"  value="{{  date('d/m/Y', strtotime(str_replace('/', '-', $data[0]->attached_date ))) }}" required readonly> 

					            </div>	                
					       
				        </div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label class="control-label">Employee Name</label>
							<select class="form-control" id="employee_name" name="employee_name"  required>
								<option value="{{$data[0]->id}}">{{$data[0]->employee_name}}</option>
							</select>

						</div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label class="control-label">File Type</label>
								<select class="form-control" id="file_type" name="file_type"  required>
										<option value="{{$data[0]->file_type_id}}">{{$data[0]->file_type_name}}</option>
								</select>
						</div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label class="control-label">File Title</label>
							<input type="text" class="form-control" name="file_title" placeholder="File Title" value="{{$data[0]->file_title}}"  autofocus >

						</div>

						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label class="control-label">Note</label>
							  <input type="text" class="form-control" name="note" placeholder="Note.." value="{{$data[0]->note}}"  autofocus >

						</div>

						<div style="margin-top: 50px;">
							<button class="btn btn-primary">Update</button>
						</div>
									
					</form>
												 
				</div>
			</div>
		</div>
	</div>



</div>

@endsection


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
$(document).ready(function($) {


    $('#attached_date').datepicker({
      // dateFormat: 'dd-mm-yyyy',
      autoclose: true
    });


	$('#employee_name').select2({
	      placeholder: 'Enter Employee Name',
	      allowClear: true,
	      ajax: {
	        dataType: 'json',
	        url: '{{URL::to('/')}}/join_employee_list',
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




	$('#file_type').select2({
	      placeholder: 'Enter File Type',
	      allowClear: true,
	      ajax: {
	        dataType: 'json',
	        url: '{{URL::to('/')}}/file_type_list',
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




});
</script>
@endsection



