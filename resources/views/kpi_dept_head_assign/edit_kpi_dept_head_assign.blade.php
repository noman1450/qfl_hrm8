@extends('layouts.main')

@section('styles')
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Department Head Assign</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>



<form class="form-horizontal" method="POST" action="{{action('KPIDeptHeadAssignController@update', $edit_data[0]->id)}}">
    {{ csrf_field() }}
    
	<input name="_method" type="hidden" value="PATCH">

	<div class="box-body">
		<div class="row">


		<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-xs-12">
				<label>Select Location</label>
				<select class="form-control" id="hrm_location_name" name="hrm_location_name" style="width: 100%;" required>
					<option value="{{$edit_data[0]->hrm_location_id}}">{{$edit_data[0]->location_name}}</option>
				</select>
			</div>
		</div>


		<div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-xs-12">
				<label>Select Year</label>
				<select class="form-control" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
					<option value="{{$edit_data[0]->hrm_kpi_assesment_date_id}}">{{$edit_data[0]->date_year}}</option>
				</select>
			</div>
		</div>



        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
            <div class="col-lg-6">  
              	<label>Employee Name</label>
             	<select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" required>
             		<option value="{{$edit_data[0]->hrm_employee_id}}">{{$edit_data[0]->employee_name}}</option>
             	</select>
	            @if ($errors->has('employee_name'))
	                <span class="help-block">
	                    <strong>{{ $errors->first('employee_name') }}</strong>
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
		        	    @foreach($dtls_data as $key)
			        	    <tr>
			        	    	@if($key->id==$key->hrm_depertment_id)
			        	    		<td><input type="checkbox" name="id[]" value="{{$key->id}}" checked></td>
			        	    	@else
			        	    		<td><input type="checkbox" name="id[]" value="{{$key->id}}"></td>
			        	    	@endif
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


<script type="text/javascript">
	$(document).ready(function($) {

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
