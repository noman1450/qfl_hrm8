<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border"  style="text-align: center;">
		<h3 class="box-title">Users Logs List</h3>
	</div>


	<div class="box-body">
    <div style="text-align: center; color: red;">
        <h5>User Name: {{$userinfo->name}}</h3>
        <h5>User Email: {{$userinfo->email}}</h3>

    </div>

		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   	
				<table id="list_table" class="table table-success table-striped" cellspacing="0" width="100%">
					<thead>
						<tr>
                            
                            <th style="width: 15%">Activity Time</th>
                            <th style="width: 15%">Working Area</th>
                            <th style="width: 70%">Change Details</th>
							
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

$(document).ready(function() {
    function loadTable() {
        $("#list_table").DataTable({
            destroy:        true,
            responsive:     true,
            processing:     true,
            serverSide:     true,
            paging:         true,
            lengthChange:   true,
            searching:      true,
            ordering:       true,
            info:           true,
            autoWidth:      false,
            width:          "100%",
            aoColumnDefs: [{ "bVisible": true, "aTargets": [0] }],
            ajax: {
                url: "{{ route('users.log', request()->segment(2)) }}",
                type: "GET",
                dataType: "json",
            },
            columns: [
                { data: "updated_at" },
                { data: "screen_from" },
                { data: "details" },
            ],
            order: [[0, 'desc']]
        })
    }

    loadTable()
})











</script>

@endsection