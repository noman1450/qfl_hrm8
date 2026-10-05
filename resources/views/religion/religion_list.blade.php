<!-- religion_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')




<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Religion List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('religion/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-6 col-md-6 col-xs-12" style="overflow: auto;">

				<table id="list_table" class="table table-bordered table-hover">
					<thead>
						<tr>
							<th style="width: 80%">Religion Name</th>
							<th style="width: 05%">Edit</th>
							<!-- <th style="width: 05%">Delete</th> -->
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
<!-- <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script> -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
$(document).ready(function() {
  var table = $('#list_table').DataTable( {
    "processing": true,
    "serverSide": true,
    "paging": true,
    "ajax": "{{URL::to('/')}}/religion_list",
    "columns": [
        { "data": "religion" },
        { "data": "Link", name: 'action', orderable: false, searchable: false},
    ],
    "order": [[0, 'asc']]
  });

});

</script>

@endsection

