@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('content')
<div class="box box-default">
    @if (session('message'))
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-success alert-dismissable">
                    {{ session('message') }}

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            </div>
        </div>
    @endif

    <div class="box-header with-border">
		<h3 class="box-title">Filter List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ url('custom_filter/create') }}" class="btn-success btn btn-sm pull-left btn-flat">
                Create New
            </a>
	    </div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <div class="box-body">
        <div class="row">
            <div class="form-group col-md-6" style="overflow: auto;">
                <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Filter Name</th>
                            <th>Table Name</th>
                            <th>Action</th>
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

@section('script')
<script>
dataLoad()

function dataLoad() {
    var table = $('#list_table').DataTable({
        "destroy":    true,
        "processing": true,
        "serverSide": true,
        "searching":  true,
        "ordering":   true,
        "bInfo":      true,
        "paging":     true,
        "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
        "ajax": {
            "url": "{{ url('/custom_filter') }}",
            "type": "GET",
        },

        "columns": [
            { "data": "id" },
            { "data": "filter_name" },
            { "data": "description" },
            { "data": "Action", orderable: false, searchable: false },
        ],

        "order": [[0, 'asc']]
    });
}
</script>
@endsection
