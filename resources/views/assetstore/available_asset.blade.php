@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">

<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px;
    }

    table.dataTable thead > tr > th {
        padding-right: 25px;
    }
    .table>tbody{
        font-size: small;
    }
    .table>thead{
        font-size: smaller;
    }

    td.details-control {
        background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
    }
</style>
@stop

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
            <h3 class="box-title">Available Asset List</h3>


		</div>

		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group col-lg-8 col-md-8 col-xs-12">
						<table id="branch-table" class="table table-bordered table-hover">
							 <thead>
                                    <tr>
                                        <th>Asset Name</th>
                                        <th>Price</th>
                                        <th>Serial No</th>
                                        <th>Available Qty</th>
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

    <!-- DataTables Buttons & JSZip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
		$(document).ready(function($) {

            table = $("#branch-table").DataTable({
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
                // aoColumnDefs: [{ "bVisible": false, "aTargets": [0] }],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: { columns: ':visible' }
                    }
                ],
                ajax: {
                    url: "{{ route('available_assets.index') }}",
                    type: "GET",
                    dataType: "json",
                },

                columns: [
                    { "data": "asset_name" },
                    { "data": "price" },
                    { "data": "serial_no" },
                    { "data": "current_stock" },
                ],

                order: [[0, 'desc']]
            })
		});
	</script>

	<script>
		function format(d) {

            return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
                    '<tr>'+
                    '<td>'+"Item Description"+'</td>'+
                    '<td>'+"Depreciation Rate"+'</td>'+
                    '<td>'+"Serial"+'</td>'+
                    '<td>'+"Price"+'</td>'+
                    '<td>'+"Quantity"+'</td>'+
                    '<td>'+"Total"+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>'+d.item_description+'</td>'+
                    '<td>'+d.depreciation_rate+'</td>'+
                    '<td>'+d.serial+'</td>'+
                    '<td>'+d.price+'</td>'+
                    '<td>'+d.qty2+'</td>'+
                    '<td>'+d.total2+'</td>'+
                    '</tr>'+
                '</table>';
		}

		$('#branch-table tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format(row.data()) ).show();
				tr.addClass('shown');
			}
        });

        $(document).on('click', '.lock_asset', function(e) {
            e.preventDefault()

            let confirmation = confirm('Are you sure to lock this..? make sure after lock, you can not edit or delete this.');

            if (confirmation) {
                $.get($(this).attr("href"))
                    .then(function(data) {
                        alert(data.success)
                        window.location.href = "{{ url('/assetstore') }}"
                    })
            }
        })
	</script>
@endsection
