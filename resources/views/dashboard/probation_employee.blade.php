
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrapvalidator/bootstrapValidator.min.css') }}">

<style>
	.header {
		/*font-size: 17px;*/
	}
</style>
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Probation Employee  (Waiting for Confirmation)</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<form class="form-horizontal header" id="identicalForm" method="POST" action="{{url('probation_confirm')}}">
		@csrf
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
						<input type="hidden" name="employee_id" value="{{$probation_employee[0]->id}}">
						<input type="hidden" name="hrm_employee_job_info_id" value="{{$probation_employee[0]->hrm_employee_job_info_id}}">

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Employee Name:</label>
							<label class="col-lg-4 control-label"style="text-align: left;">{{$probation_employee[0]->employee_name}}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Department Name:</label>
							<label class="col-lg-4 control-label" style="text-align: left;">{{$probation_employee[0]->depertment_name}}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Designation Name:</label>
							<label class="col-lg-4 control-label"style="text-align: left;">{{$probation_employee[0]->designation_name}}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Job Placement:</label>
							<label class="col-lg-4 control-label"style="text-align: left;">{{$probation_employee[0]->location_name}}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Joining Date:</label>
							<label class="col-lg-4 control-label"style="text-align: left;">{{  date('d-m-Y', strtotime(str_replace('/', '-', $probation_employee[0]->joining_date ))) }}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label" style="color: red;">Probable Confirm Date:</label>
							<label class="col-lg-6 control-label"style="text-align: left;"><input type="text" class="form-control" id="confirmation_date" style="color: red;" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $probation_employee[0]->probable_date ))) }}" ></label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">Previous Salary:</label>
							<label class="col-lg-4 control-label"style="text-align: left;">{{$probation_employee[0]->basic_salary}}</label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">New Salary:</label>
							<label class="col-lg-6 control-label"style="text-align: left;"><input type="text" class="form-control" placeholder="New Salary Amount" name="new_salary" required></label>
						</div>

						<div class="col-md-12 col-xs-12">
							<label class="col-lg-4 control-label">New Status:</label>
							<label class="col-lg-6 control-label"style="text-align: left;">
								<select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;" required>
								</select>
							</label>
						</div>

						@if (Config::get('module_config.payroll_module') == 1)
							<div class="col-md-12 col-xs-12">
								<label class="col-lg-4 control-label">Salary Grade:</label>
								<label class="col-lg-6 control-label"style="text-align: left;">
									<select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;" required>
									</select>
								</label>
							</div>
						@endif

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
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script type="text/javascript">
$('#salary_grade').select2({
	placeholder: 'Enter a Salary Grade',
	allowClear: true,
	ajax: {
		dataType: 'json',
		url: '{{URL::to('/')}}/salarygrade_list',
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

$('#employeestatus').select2({
	placeholder: 'Enter employee status',
	allowClear: true,
	ajax: {
	dataType: 'json',
	url: '{{URL::to('/')}}/employeestatus_list_data',
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
