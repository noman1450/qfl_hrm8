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
		<h3 class="box-title">Designation List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('designation/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th style="width: 20%">Designation</th>
					<th style="width: 20%">Alis</th>
					<th style="width: 20%">Priority</th>
					<th style="width: 05%">Edit</th>
					<th style="width: 05%">Delete</th>
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
	// var table = $('#financial_year_data_table').DataTable( {
	//     "ajax": "{{URL::to('/')}}/view_data",
	//     "columns": [
	// 	    { "data": "financial_year" },
	// 	    { "data": "start_date" },
	// 	    { "data": "end_date" },
	// 	    { "data": "financial_year_status" },
	// 	    { "data": "Link",
	// 	      "mRender": function (data, type, full) {
	// 	        return '<a href="{{URL::to('/')}}/financial_year/'+full.id+'/edit"     class="glyphicon glyphicon-edit"></a>';
	// 	      }
	// 	    },
	// 		{ "data": "Link",
	// 			"mRender": function (data, type, full) {
	// 		  		return '<a href="{{URL::to('/')}}/financial_year/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash">Delete</a>';
	// 			}
	// 		},
	//     ],
	//     "order": [[0, 'asc']]
	// });
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/designation_list",
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
              { "data": "designation_name" },
              { "data": "alis" },
              { "data": "priority" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/designation/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },                       
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/designation/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },

            ],
            "order": [[ 2,'asc']]
            });
        }
      }); 
});

</script>

@endsection