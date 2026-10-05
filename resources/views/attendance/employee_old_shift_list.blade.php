<!-- shift_list -->

@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Old Shift List</h3>
	   <!--  <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('employeeshiftlist/employeeshiftlist_old')}}"><input type="button" value="Old Shift" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div> -->
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
		<table id="designation_list_table" class="table table-bordered table-hover" style="overflow: auto;" cellspacing="0" width="100%">
			<thead>
				<tr>
          <th style="width: 20%">Employee Name</th>
          <th style="width: 13%">Department</th>
          <th style="width: 15%">Designation</th>
          <th style="width: 12%">Shift Name</th>
          <th style="width: 10%">Start Date</th>
          <th style="width: 10%">End Date</th>
          <th style="width: 10%">Start Time</th>
          <th style="width: 10%">End Time</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

@endsection
<!-- 175.29.166.86 -->


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
$(document).ready(function($) {
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/employeeoldshiftlist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              { "data": "start_date" },
              { "data": "end_date" },
              { "data": "start_time" },
              { "data": "end_time" },
             

            ],
            "order": [[0,'asc']]
            });
        }
      }); 
});

</script>

@endsection