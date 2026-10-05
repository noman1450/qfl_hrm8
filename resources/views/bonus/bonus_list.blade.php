<!-- bloodgroup_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Bonus Name List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('bonus/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-6 col-md-6 col-xs-12">   	
				<table id="blood_group_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
                            <th style="width: 40%">Bonus Name</th>
							<th style="width: 40%">Bonus Description</th>
							<th style="width: 10%">Edit</th>
							<th style="width: 10%">Delete</th>
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


        $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/bonus_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#blood_group_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "bonus_name" },
              { "data": "bonus_description" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/bonus/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },                       
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/bonus/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },

            ],
            "order": [[0,'asc']]
            });
        }
      }); 

// $(document).ready(function() {
//   var table = $('#blood_group_list_table').DataTable( {
//     "processing": true,
//     "serverSide": true,
//     "ajax": "{{URL::to('/')}}/getbonuslist",
//       "columns": [
//          { "data": "bonus_name" },
//               { "data": "bonus_description" },
//               { "data": "Link",
//                 "mRender": function (data, type, full) {
//                   return '<a href="{{URL::to('/')}}/bonus/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
//                 }
//               },                       
//               { "data": "Link",
//                 "mRender": function (data, type, full) {
//                     return '<a href="{{URL::to('/')}}/bonus/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
//                 }
//               },
//       ],
//     "order": [[1, 'asc']]
//   } );
// });




</script>

@endsection