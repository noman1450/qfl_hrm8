<!-- create_depertment -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style type="text/css">
	.textarea {
    resize: none;
    overflow: hidden;
    min-height: 50px;
    max-height: 100px;
}

</style>

@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create New Employee KPI</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<form class="form-horizontal" method="POST" action="{{url('kpi_employee_final_addnote_submit')}}">
		{{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">


					<input type="hidden" name="hrm_kpi_employee_final_summary_id" id="hrm_kpi_employee_final_summary_id" value="{{$data[0]->hrm_kpi_employee_final_summary_id}}">

					
					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Select Year</label>
							<input type="" name="" value="{{$data[0]->kpi_year}}" class="form-control" readonly="">
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Employee Name</label>
							<input type="" name="" value="{{$data[0]->employee_name}}" class="form-control" readonly="">
							
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-6 col-md-6 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Department Name</label>
							<input type="" name="" value="{{$data[0]->depertment_name}}" class="form-control" readonly="">
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-6 col-md-6 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Designation Name</label>
							<input type="" name="" value="{{$data[0]->designation_name}}" class="form-control" readonly="">
						</div>
					</div>





					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-6 col-md-6 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Promotion</label>
							<input type="" name="" value="{{$data[0]->promotion}}" class="form-control" readonly="">
							
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-6 col-md-6 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Increment</label>
							<input type="" name="" value="{{$data[0]->increment}}" class="form-control" readonly="">
							
						</div>
					</div>


				</div>
				<div class="col-md-6">

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-4 col-md-4 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Total Point</label>
							<input type="" name="" value="{{$data[0]->total_point}}" class="form-control" readonly="">
						</div>
					</div>

					<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-4 col-md-4 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Deduct Point</label>
							<input type="" name="" value="{{$data[0]->deduct_point}}" class="form-control" readonly="">
						</div>
					</div>

				<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-4 col-md-4 col-xs-12">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<label>Net Point</label>
							<input type="" name="" value="{{$data[0]->net_point}}" class="form-control" readonly="">
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-xs-12">
						<label>Other Comments/Recommendation</label>
                         <input class="form-control" type="text" name="note" value="{{$data[0]->note}}" placeholder="Write Comments..."  maxlength ="145" >
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
	</form>
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

    var $employee = $('#employee_name').select2({
            placeholder: 'Enter Employee Name',
            allowClear: true,
            ajax: {
              dataType: 'json',
              url: '{{URL::to('/')}}/getemployeejobinfo_details',
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
        $employee.on("select2:select", function (e) {
           $("#joining_date").val($(this).select2('data')['0']['joining_date']);
           $("#confirmation_date").val($(this).select2('data')['0']['confirmation_date']);
           $("#department_name").val($(this).select2('data')['0']['depertment_name']);
           $("#designation_name").val($(this).select2('data')['0']['designation_name']);
           $("#department_id").val($(this).select2('data')['0']['department_id']);
           $("#hrm_employee_job_info_id").val($(this).select2('data')['0']['hrm_employee_job_info_id']);

            dataLoad();
        });

	    $employee.on('select2:unselect', function (e) {
	        $('#employee_name').val(null).trigger("change");
			$("#joining_date").val('');
			$("#confirmation_date").val('');
			$("#department_name").val('');
			$("#designation_name").val('');
			$("#department_id").val(0);
			$("#hrm_employee_job_info_id").val(0);
	        dataLoad();
	    });


	    $('#hrm_kpi_assesment_date_id').select2({
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


        dataLoad = function(){
	      $.ajax({
	        type:   'POST', 
	        url :   "{{URL::to('/')}}/kpi_task_list_by_department",
	        headers:{
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	                },
	        data:   {
	        	      department_id : $("#department_id").val(), 
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
	              },
	            "columns": [

	                //{ "data": "Link",
	                //   "mRender": function (data, type, full) {
	                //     return '<input type="checkbox" name="id[]" value="'+full.id+'">';
	                //   }
	                // },
                        // +'<input type="hidden" name="hrm_kpi_task_details_id[]" value="'+row.id+'">';
                    // +'<input type="hidden" name="hrm_kpi_task_details_id[]" value="'+row.id+'">';
	                { "data": "description" },
	                { "data": null,render:function(data,type,row){
                    return '<select class="form-control mark" style="width: 100%;" name="mark[]" value="'+row.id+'"	 required></select>'+'<input type="hidden" name="hrm_kpi_task_details_id[]" value="'+row.id+'">'; 
                    }
                  },

	            ],
	            "order": [[0,'asc']]
	            });
	        }
	      });
        }
			// table = $('#list_table').DataTable({
			//   destroy:    true,
   //            paging:     false,
   //            searching:  false,
   //            ordering:   false,
   //            bInfo:      false,
			// });

		   $('#example-select-all').on('click', function(){
		      var rows = table.rows({ 'search': 'applied' }).nodes();
		      $('input[type="checkbox"]', rows).prop('checked', this.checked);
		   });


		   $('#list_table tbody').on('change', 'input[type="checkbox"]', function(){
		      if(!this.checked){
		         var el = $('#example-select-all').get(0);
		         if(el && el.checked && ('indeterminate' in el)){
		            el.indeterminate = true;
		         }
		      }
		   });




		});
</script>
@endsection