<!-- create_depertment -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit  Task Details </h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('kpi_task_details')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

        <div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
            <div class="col-lg-6">  
              	<label>Task Department Name</label>
              <select class="form-control" id="task_department_id" name="task_department_id" style="width: 100%;" required readonly>
              	<option value="{{$edit_data[0]->id}}">{{$edit_data[0]->description}}</option>
              </select>

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
					<!--<tbody>
		        	</tbody>-->
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
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script type="text/javascript">
	$(document).ready(function($) {

        // $('#task_department_id').select2({
        //     placeholder: 'Enter Task Department Name',
        //     allowClear: true,
        //     ajax: {
        //       dataType: 'json',
        //       url: '{{URL::to('/')}}/kpi_task_department_listdata',
        //       delay: 250,
        //       data: function(params) {
        //         return {
        //           term: params.term
        //         }
        //       },
        //       processResults: function (data, params) {
        //         params.page = params.page || 1;
        //         return {
        //           results: data
        //         };
        //       },
        //       cache: true
        //     }
        // });




	      $.ajax({
	        type:   'POST', 
	        url :   "{{URL::to('/')}}/kpi_task_deptwise_list",
	        headers:{
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	                },
	           data:{
	           		   task_department_id: $("#task_department_id").val()
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

	            "columns": [
				{ "data": "checkbox",
						"mRender": function (data, type, full) {
						if(full.dtlsVal > 0 ){
							return '<input type="checkbox" name="id[]" value="'+full.id+'" checked>';
						}else{
							return '<input type="checkbox" name="id[]" value="'+full.id+'" >';
						}
					}
				},
	            { "data": "description" },

	            ],
	            "order": [[0,'asc']]
	            });
	        }
	      });

 







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
