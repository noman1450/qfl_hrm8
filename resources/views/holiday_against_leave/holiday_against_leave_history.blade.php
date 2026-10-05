<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Holiday Against Leave History</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
		<div class="row">

        <div class="col-lg-12 col-md-12 col-xs-12">   	
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							
              <th style="width: 20%">Employee Name</th>
              <th style="width: 15%">Department Name</th>
							<th style="width: 20%">Designation Name</th>
							<th style="width: 15%">Reserve Holiday</th>
              <th style="width: 15%">Taken Leave</th>
              <th style="width: 15%">Remaining Days</th>
							
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

		</div>
	</div>
  
</div>

@endsection



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
$(document).ready(function($) {

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/holidayagainstleavehistorylist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },                              
              { "data": "depertment_name" },                     
              { "data": "designation_name" },                     
              { "data": "WorkOnHoliday" },                     
              { "data": "takenLeave" },                     
              { "data": "RemainingDays" },                     
            ],
            "order": [[0,'asc']]
            });
        }
      });


});

</script>

@endsection