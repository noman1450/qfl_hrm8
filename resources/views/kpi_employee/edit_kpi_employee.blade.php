<!-- create_depertment -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee KPI</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
    {!! Form::open(array('route' => array('kpi_employee.update', $data[0]->hrm_kpi_employee_master_id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Selected Year</label>
							<input class="form-control" type="" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id"  value="{{$data[0]->kpi_date}}" readonly="">

						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Employee Name</label>
							<input class="form-control" type="" name="employee_name"  value="{{$data[0]->employee_name}}" readonly="">
						</div>
					</div>


					<!-- <input type="text" name="department_id" id="department_id" value="{{$data[0]->department_id}}" hidden=""> -->
					<input type="text" name="hrm_employee_job_info_id" id="hrm_employee_job_info_id"  value="{{$data[0]->hrm_employee_job_info_id}}" hidden="">
					<input type="text" name="hrm_kpi_employee_master_id" id="hrm_kpi_employee_master_id"  value="{{$data[0]->hrm_kpi_employee_master_id}}" hidden="">


					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Department Name</label>
							<input class="form-control" type="" name="" id="department_name" value="{{$data[0]->depertment_name}}" readonly="">
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Designation Name</label>
							<input class="form-control" type="" name="" id="designation_name" value="{{$data[0]->designation_name}}" readonly="">
						</div>
					</div>

                    <!--
					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Joining Date</label>
							<input class="form-control" type="" name="" id="joining_date" readonly="">
						</div>
					</div>
					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Confirmation Date</label>
							<input class="form-control" type="" name="" id="confirmation_date" readonly="">
						</div>
					</div> -->

				</div>
				<div class="col-md-6">
					<div class="form-group has-feedback {{ $errors->has('task_depertment_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12">
							<table class="table table-bordered table-hover" id="list_table" cellspacing="0" width="100%">
								<thead>
									<tr style="background: #DCDCDC;">
										<!-- <td style="width: 20%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /> All</td> -->
										<td style="width: 60%">Personal Characteristics & Performance</td>
										<td style="width: 40%">Select Mark</td>
									</tr>
								</thead>
								<!--<tbody>
								</tbody>-->
							</table>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-xs-12">
						<label>Other Comments/Recommendation</label>
						<input class="form-control" type="text" name="comments" value="{{$data[0]->comments}}" placeholder="Write Comments..." >
				    </div>
				</div>
			</div>
			<!-- <div class="row"> -->

			<!-- </div> -->
		</div>
		<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
			<div class="row">
				<div class="form-group col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-12">
						<button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
					</div>
				</div>
			</div>
		</div>
	{!! Form::close() !!}
</div>
@endsection
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script type="text/javascript">
	$(document).ready(function($) {

	      $.ajax({
	        type:   'POST',
	        url :   "{{URL::to('/')}}/kpi_task_list_by_emp_jobid",
	        headers:{
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	                },
	        data:   {
	        	      // department_id              : $("#department_id").val(),
	        	      // hrm_kpi_assesment_date_id  : $("#hrm_kpi_assesment_date_id").val(),
	        	      // hrm_employee_job_info_id   : $("#hrm_employee_job_info_id").val(),
	        	      hrm_kpi_employee_master_id : $("#hrm_kpi_employee_master_id").val(),

	                },
	        dataType: 'json',
	        success: function(data) {
	            var dataSet = data.data;

	            table = $('#list_table').DataTable( {
	              destroy:    true,
	              paging:     false,
	              searching:  false,
	              ordering:   true,
	              bInfo:      true,
	              "data":     dataSet,
	              drawCallback: function() {
	                  $('.mark').select2({
	                    placeholder: 'Select Mark',
	                    allowClear: true,
	                    ajax: {
	                      dataType: 'json',
	                      url: '{{URL::to('/')}}/get_kpi_mark_list',
	                      delay: 250,
	                      data: function(params) {
	                        return {
	                          term: params.term,
	                          hrm_kpi_set_config_id: dataSet[0].hrm_kpi_set_config_id
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
	              },
	            "columns": [

	                { "data": "description" },
	                { "data": null,render:function(data,type,row){
                    return '<select class="form-control mark" style="width: 100%;" name="mark[]" 	 required> <option value="'+row.hrm_kpi_marks_id+'">'+row.marks+'</option>	 </select>'
                    +'<input type="hidden" name="hrm_kpi_task_details_id[]" value="'+row.hrm_kpi_task_details_id+'">';
                    }
                  },

	            ],
	            "order": [[0,'asc']]
	            });
	        }
	      });
		});
</script>
@endsection
