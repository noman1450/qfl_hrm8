<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.css')}}">

<style type="text/css">
	.header{
		/*font-size: 17px;*/
	}

</style>

@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Employee Reactive</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<form class="form-horizontal header" id="identicalForm" method="POST" action="{{url('reactive_employee')}}">
		{{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">

					@if ($errors->any())
					    <div class="alert alert-danger">
					        <ul>
					            @foreach ($errors->all() as $error)
					                <li>{{ $error }}</li>
					            @endforeach
					        </ul>
					    </div>
					@endif				
					<div class="col-md-8" >
		
							<input type="hidden" name="inactive_id" value="{{$employee_date[0]->id}}">


							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Employee Name:</label>
								<label class="col-lg-4 control-label"style="text-align: left;">{{$employee_date[0]->employee_name}}</label>	
							</div>

							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Department Name:</label>
								<label class="col-lg-4 control-label" style="text-align: left;">{{$employee_date[0]->depertment_name}}</label>	
							</div>
							
							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Designation Name:</label>
								<label class="col-lg-4 control-label"style="text-align: left;">{{$employee_date[0]->designation_name}}</label>	
							</div>

							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Job Placement:</label>
								<label class="col-lg-4 control-label"style="text-align: left;">{{$employee_date[0]->location_name}}</label>	
							</div>

							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Inactive Date:</label>
								<label class="col-lg-4 control-label"style="text-align: left;">{{  date('d-m-Y', strtotime(str_replace('/', '-', $employee_date[0]->date_from ))) }}</label>	
							</div>			

				            <div class="col-md-12 col-xs-12">

								<label class="col-lg-4 control-label" style="color: red;">Reactive Date:</label>
								<label class="col-lg-6 control-label"style="text-align: left;"><input type="text" class="form-control" id="confirmation_date" style="color: red;" name="reactive_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $employee_date[0]->date_to ))) }}" ></label>
				            </div>	
				            <div class="col-md-12 col-xs-12">

								<label class="col-lg-4 control-label">Note:</label>
								<label class="col-lg-6 control-label"style="text-align: left;"><input type="text" class="form-control" name="note" ></label>
				            </div>	
						<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label"></label>
								<label class="col-lg-6 control-label"style="text-align: left;"><input type="submit" class="btn btn-success btn-flat pull-right mybutton" value="Confirm" ></label>

						</div>

					</div>

				</div>
			</div>




		</div>
	</form>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.js')}}"></script>

@endsection