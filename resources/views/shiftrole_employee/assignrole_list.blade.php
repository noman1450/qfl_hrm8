<!-- designation_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title"> Shift Role Assign List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('shiftrole_assign')}}"><input type="button" value="New Shift Role Assign" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
<!-- 	    </div>

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;"> -->
          <a href="{{ URL::to('previou_shiftrole_assign')}}"><input type="button" value="Previous Data" class="btn-sucess btn btn-sm button pull-right btn-flat" style="font-size: 12px; font-weight: bold; "></a>
      </div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
   <div class="row">
     <div class="col-md-12" style="overflow: auto;">
  		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
  			<thead>
  				<tr>
  					<th style="width: 35%">Role Name</th>
            <th style="width: 25%">Running Shift Name</th>
            <th style="width: 20%">Shifting Time</th>
  					<!-- <th style="width: 30%">End Time</th> -->
  					<th style="width: 20%">Start Date</th>
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
        url :   "{{URL::to('/')}}/assignrolelist",
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
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              { "data": "shift_role_name" },
              { "data": "shift_name" },
              { "data": "shift_time" },
              // { "data": "end_time" },
              { "data": "start_date" }
            ],
            "order": [[0,'asc']]
            });
        }
      }); 
});

</script>

@endsection