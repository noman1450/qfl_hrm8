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
		<h3 class="box-title">Employee Document Upload</h3>
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

					{{-- @if ($message = Session::get('error'))
						<div class="alert alert-danger" role="alert">
						{{ Session::get('error') }}
						</div>
					@endif --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin-bottom:0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

					<form style="border: 4px solid #a1a1a1;margin-top: 5px;padding: 5px;"  action="{{ URL::to('document_archive') }}"   class="form-horizontal" method="post" enctype="multipart/form-data">

                		<input type="hidden" name="status" value="1">

				        <div class="col-lg-12 col-md-12 col-xs-12 form-group">

				            	<label >Activity Date</label>
					            <div class="input-group date">
					                <div class="input-group-addon">
					                  <i class="fa fa-calendar"></i>
					                </div>
					                <input type="text" class="form-control pull-right" id="attached_date" name="attached_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('attached_date') }}" required readonly>
					            </div>

				        </div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label control-label">Employee Name</label>
							<select class="form-control" id="employee_name" name="employee_name"  required>
							</select>

						</div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label control-label">File Type</label>
								<select class="form-control" id="file_type" name="file_type"  required>
								</select>
  							  {{-- @permission('DocumentArchiveFileTypeSetup') --}}
						      <button type="button" class=" btn btn-sm button pull-left btn-flat" data-toggle="modal" data-target="#modal_create_file_type" style="font-size: 12px; font-weight: bold;">+</button>
						      {{-- @endpermission --}}
						</div>


						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label control-label">File Title</label>
							<input type="text" class="form-control" name="file_title" placeholder="File Title" value="{{ old('file_title') }}"  autofocus >

						</div>

						<div class="col-lg-12 col-md-12 col-xs-12 form-group">
							<label control-label">Note</label>
							  <input type="text" class="form-control" name="note" placeholder="Note.." value="{{ old('note') }}"  autofocus >

						</div>

						<div style="margin-top: 50px;">
							<input type="file" name="image" />
							{{ csrf_field() }}
							<br/>
							<button class="btn btn-primary">Submit</button>
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





<!--  Create New Salary Grade Modal-->
<div class="modal fade" id="modal_create_file_type" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="groupAddLabel">Create New File Type</h4>
      </div>

      <form method="POST" action="{{url('filetypecreate')}}">
        {{ csrf_field() }}
        <div class="modal-body">

          <div class="row">

            <div class="form-group">
              <div class="col-md-12">
                <label>File Type Name</label>
                <input class="form-control" type="text" placeholder="File Type Name" name="file_type_name" required>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button_sunmit" class="btn btn-success btn-flat">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End of Create New Salary Grade Modal-->
