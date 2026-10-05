@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">KPI Report</h3>
		<div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
			<div class="col-xs-4 col-md-4">
			</div>
		</div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	
	
	<div class="box-body">
		
		<div class="col-lg-6 col-md-6 col-xs-12">
			<div class="col-md-10">
				<div class="form-group">
					<button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_kpi_summary">KPI Summary Report</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modal_kpi_summary"  role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h3 class="modal-title" id="groupAddLabel">KPI Report</h3>
				</div>
				
				{!! Form::open(['method'=>'POST', 'action'=>['KPIReportController@kpi_summary_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
				<div class="modal-body">
					
					<div class="row">


            <div class="form-group">
              <div class="col-md-12">
                <label>Select Report Name</label>
                <select  class="form-control" name="report_for" style="width: 100%;" >
                  <option value="1">KPI Summary Report</option>
                  <option value="2">KPI Summary Report(Not Done Yet)</option>
                  <option value="3">KPI Summary Report(Done & Not Yet Done)</option>
                  <option value="4">Increment Range Wise Summary Report</option>
                </select>
              </div>
            </div>



						
						<div class="form-group">
							<div class="col-md-12">
								<label>Location</label>
								<select  class="form-control" name="location" style="width: 100%;" >
                  <option value="0">- All -</option>
                  <option value="999">-- All Depot --</option>

									@foreach ($location as $keys)
									     <option value={{$keys->id}}>{{$keys->location_name}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-12">
								<label>Year/Date Range</label>
								<select  class="form-control hrm_kpi_assesment_date_id" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id" style="width: 100%;" required="">
								</select>
							</div>
						</div>
          
          <div class="col-md-12"></div>

						<div class="form-group">
							<div class="col-md-6">
								<label>Employee Name</label>
								<select class="form-control employee_name" name="employee_name" style="width: 100%;" >
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label>Sub-department</label>
								<select class="form-control section" name="section" style="width: 100%;" >
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-md-6">
								<label>Department</label>
								<select class="form-control depertment" name="depertment" style="width: 100%;" >
								</select>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<label>Employee Category</label>
								<select class="form-control category" name="category" style="width: 100%;" >
								</select>
							</div>
						</div>

            <div class="form-group">
              <div class="col-md-6">
                <label>Designation</label>
                <select class="form-control designation" name="designation" style="width: 100%;" >
                </select>
              </div>
            </div>


            <div class="form-group">
              <div class="col-md-6">
                <label>Increment Range</label>
                <select class="form-control increment_range" name="increment_range" style="width: 100%;" >
                </select>
              </div>
            </div>




						<div class="form-group">
							<div class="col-md-6">
								<label>Generate Type</label>
								<select  class="form-control" name="generate_type" style="width: 100%;" >
									<option value="pdf">PDF</option>
									<option value="xls">Excel</option>
								</select>
							</div>
						</div>


					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="button_sunmit" class="btn btn-primary btn-flat">View</button>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
$(document).ready(function($) {



  $('.employee_name').select2({
        placeholder:'Enter an Employee Name',
        allowClear: true,

          ajax: {
              dataType: 'json',
              url: "{{URL::to('/')}}/join_employee_list",
              delay: 250,         
            data: function(params) {
                return {
                  term: params.term,
                  apply_old_info:$('input[name=apply_old_info]:checked').val()
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


  $('.depertment').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
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

    $('.category').select2({
      placeholder: 'Enter Employee Category',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/category_list_data',
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


    $('.section').select2({
      placeholder: 'Enter Sub-department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/section_list_data',
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


    $('.hrm_kpi_assesment_date_id').select2({
              placeholder: 'Select Year/Month',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/kpi_assesment_date_list_data',
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


   $('.increment_range').select2({
              placeholder: 'Select Increment Range ',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/increment_range_list_data',
                delay: 250,
                data: function(params) {
                  return {
                    term: params.term,
                    hrm_kpi_assesment_date_id : $('#hrm_kpi_assesment_date_id').val(),
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
   

    $('.designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
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