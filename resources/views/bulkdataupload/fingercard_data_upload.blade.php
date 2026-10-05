<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style>
</style>
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Finger/Card Bulk Data - Import excel file into database</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
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


				<h4>Download Employee Finger/Card Sample Excel File: </h4>
				<div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;">
					<a href="{{ url('downloadExcel_fingercard') }}"><button class="btn btn-success ">Download Sample Excel File</button></a>
				</div>


				<br>
				<br>
				<br>
				<br>


				<h4>Import Employee Finger/Card File:</h4>
				
				<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;" action="{{ URL::to('importExcel_fingercard') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
					<input type="file" name="import_file" />
					{{ csrf_field() }}
					<br/>
					<button class="btn btn-primary">Import Excel File</button>
				</form>
				<br/>


<!-- 			    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
			        <a href="{{ URL::to('jobinfotoemployeecard')}}"><input type="button" value="Job Info to Employee Card" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
			    </div> -->



				
			</div>
			<!-- </div> -->
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>

<script>
$(document).ready(function($) {
});
</script>
@endsection