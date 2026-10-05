@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Draft Employee List</h3>
        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

	        {{-- @permission('NewEmployeeCreateAndJoin') --}}
          <a href="{{ URL::to('add_new_employee/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
          {{-- @endpermission --}}
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
				<table id="list_table" class="table table-bordered table-hover" style="overflow: auto;" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width: 5%"></th>
							<th style="width: 20%">Employee Name</th>
							<th style="width: 20%">Contact Number</th>
							<th style="width: 20%">Email</th>
							<th style="width: 10%">Religion</th>
							<th style="width: 10%">Gender</th>
                            {{-- @permission('NewEmployeeCreateAndJoin') --}}
                            <th style="width: 05%">Status</th>
							<th style="width: 05%">Edit</th>
							<th style="width: 05%">Delete</th>
							{{-- <th style="width: 05%">Join</th> --}}
                            {{-- @endpermission --}}
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

<script>
$(document).ready(function($) {
    $.ajax({
        type:   'POST',
        url :   "{{ url('/draft_employee_list') }}",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable({
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,

            "columns": [

              { "data": "Link",
                "mRender": function (data, type, full) {
                  // return '<a href="{{URL::to('/')}}/maritalstatus/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                  // return '<img src="{{URL::to('/')}}/public/employee_image/'+full.Images+'" style="height:50px;width:50px;"/>';
                  // return '<img src="public/employee_image/'+full.Images+'" style="height:50px;width:50px;"/>';
                  return '<img src="{{ asset("employee_image") }}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              { "data": "employee_name" },
              { "data": "contact_number" },
              { "data": "email" },
              { "data": "religion" },
              { "data": "gender" },
              {
                    "data": "status",
                    "mRender": function (data, type, full) {
                        var badgeClass = '';

                        if (full.status_value == 1) {
                            badgeClass = 'label-default';
                        }
                        else if (full.status_value == 2) {
                            badgeClass = 'label-warning';
                        }

                        return '<span class="label ' + badgeClass + '">' + data + '</span>';
                    }
                },
            //   @permission('NewEmployeeCreateAndJoin')
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{ url('/add_new_employee/') }}/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{ url('/add_new_employee/') }}/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                }
              },
            //   { "data": "Link",
            //     "mRender": function (data, type, full) {
            //         return '<a href="{{URL::to('/')}}/employee/'+full.id+'"  class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-ok"></span> Join</a>';
            //     }
            //   },
            //   @endpermission
            ],
            "order": [[0,'asc']]
            });
        }
    });
});

</script>

@endsection
