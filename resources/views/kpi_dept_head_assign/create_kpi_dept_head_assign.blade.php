<!-- create_depertment -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create KPI Dept Head Assign</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('kpi_deptheadassign')}}">
    {{ csrf_field() }}
	<div class="box-body">
		<div class="row">

		<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-xs-12">
				<label>Select Location</label>
				<select class="form-control" id="hrm_location_name" name="hrm_location_name" style="width: 100%;" required>
				</select>
			</div>
		</div>


		<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-xs-12">
				<label>Select Year</label>
				<select class="form-control" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
				</select>
			</div>
		</div>



        <div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
            <div class="col-lg-6">  
              	<label>Employee Name</label>
             	<select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" required></select>
	            @if ($errors->has('task_department_name'))
	                <span class="help-block">
	                    <strong>{{ $errors->first('task_department_name') }}</strong>
	                </span>
	            @endif 	                
            </div>
        </div>			

        <div class="form-group has-feedback {{ $errors->has('task_depertment_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	        <div class="col-lg-6"> 
		        <table class="table table-bordered table-hover" id="list_table" cellspacing="0" width="100%">
		        	<thead>
		        		<tr style="background: #DCDCDC;">
		        			<td style="width: 20%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /> All</td>
		        			<td style="width: 80%">Department Name</td>
		        		</tr>
		        	</thead>
		        	<tbody>
		        	    @foreach($data as $key)
			        	    <tr>
			        	    	<td><input type="checkbox" name="id[]" value="{{$key->id}}"></td>
			        	    	<td>{{$key->depertment_name}}</td>
			        	    </tr>
		        	    @endforeach
		        	</tbody>
		        </table>
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
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>



<script type="text/javascript">
	$(document).ready(function($) {

	 $('#hrm_location_name').select2({
	            placeholder: 'Enter Location Name',
	            allowClear: true,
	            ajax: {
	              dataType: 'json',
	              url: '{{URL::to('/')}}/location_list_data',
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


			table = $('#list_table').DataTable({
			  destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   false,
              bInfo:      false,
			});

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
