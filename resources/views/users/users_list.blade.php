<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Users List </h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('users/create')}}"><input type="button" value="Create New User" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
		<div class="row">
    <div class="col-md-12"> 
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   	
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							
							<th style="width: 20%">User Name</th>
              <th style="width: 20%">Email</th>
							<th style="width: 15%">Current Role</th>
              <th style="width: 20%">Default Branch</th>
              <th style="width: 5%">Edit</th>
							<th style="width: 5%">Password</th>
              <th style="width: 5%">Status</th>
              <th style="width: 5%">Logs</th>
							<th style="width: 5%">Delete</th>
             
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
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

<script>
$(document).ready(function($) {
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/users_list",
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
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
                                             
              { "data": "name" },                     
              { "data": "email" },                     
              { "data": "display_name" },                     
              { "data": "location_name" },                     
                    
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/users/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/users/'+full.id+'/reset"> <span class="glyphicon glyphicon glyphicon-share-alt"></span>Reset pwd</a>';
                }
              },
              { "data": "status" }, 

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/users/'+full.id+'/usersLog"> <span class="glyphicon glyphicon glyphicon-th"></span> Log </a>';
                }
              },

              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/users/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
              //   }
              // },

              { "data": "Link",
                "mRender": function (data, type, full) {
                if(full.valid == 1 ){
                  return '<a href="{{URL::to('/')}}/users/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span></a>';
                  
                }else{
                  return '<a href="{{URL::to('/')}}/users/'+full.id+'/reactive"   class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Reactive</a>';
                }
              }
            },






                           
            ],
            "order": [[0,'asc']]
            });
        }
      }); 
});

</script>

@endsection