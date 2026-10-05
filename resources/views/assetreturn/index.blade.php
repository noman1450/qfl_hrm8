@extends('layouts.main')

@section('styles')
	<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
			<h3 class="box-title">Returned Asset List</h3>

			<div class="box-tools pull-right">
				<a type="button" href="{{ URL('/') }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
			</div>
		</div>

		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group col-lg-12 col-md-12 col-xs-12">
						<a href="{{ route('assetreturn.create')}}"><input type="button" value="Return Asset" class="btn-success btn btn-sm button pull-left" style="font-size: 12px; font-weight: bold;"></a>
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group col-lg-12 col-md-12 col-xs-12">
						<table id="data-table" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="width: 20%">Employee/Department</th>
									<th style="width: 20%">Asset</th>
									<th style="width: 10%">#Return No</th>
									<th style="width: 20%">Return Date</th>
									<th style="width: 15%">Return Type</th>
									<th style="width: 15%">Action</th>
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


@section('script')
	<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
$(document).ready(function($) {
    table = $('#data-table').DataTable({
        destroy:    true,
        paging:     true,
        searching:  true,
        ordering:   true,
        bInfo:      true,
        ajax: {
            type: 'POST',
            url : "{{URL::to('/')}}/asset_return_list",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataType: 'json'
        },

        "columns": [
            { "data": "employee" },
            { "data": "asset" },
            { "data": "return_no" },
            { "data": "created_at" },
            { "data": "return_type" },
            { "data": "Link" }
        ],

        "order": [[0,'asc']]
    });

    $(document).on('click', '.lock_asset', function(e) {
        e.preventDefault()
        console.log($(this).attr("href"));

        let confirmation = confirm('Are you sure to lock this..? make sure after lock, you can not edit or delete this.');

        if (confirmation) {
            $.get($(this).attr("href"))
                .then(function(data) {
                    alert(data.success)
                    window.location.href = "{{ url('/assetreturn') }}"
                })
        }
    })
});
</script>

@endsection
