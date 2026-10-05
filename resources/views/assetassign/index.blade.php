@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker.css') }}">
@endsection

@section('content')

<div class="box box-default">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#Asset_List" data-toggle="tab" aria-expanded="true">Active List</a></li>
                <li><a href="#Asset_Type" data-toggle="tab" aria-expanded="false">Reject/Reuse List</a></li>
            </ul>

            <div class="tab-content" id="myTab">

                <!-- Active List Tab -->
                <div class="tab-pane active" id="Asset_List">
                    <div class="box box-default">
                        <div class="box-body">
                            <div class="row">

                                <div class="box-header with-border col-md-2">
                                    <a href="{{ route('assetassign.create') }}" class="btn btn-success btn-sm button pull-left" style="font-size:12px;font-weight:bold">Create New Assign</a>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label">From Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control pull-right" id="from_date" name="from_date" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label">To Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control pull-right" id="to_date" name="to_date" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>

                                <!-- Excel Button Container -->
                                <div class="col-md-6 text-right mt-4" style="margin-top:23px;margin-bottom:10px;;">
                                    <div id="excel_btn" style="background:green"></div>
                                </div>

                                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">
                                    <table id="data-table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%">Employee/Department</th>
                                                <th style="width: 25%">Asset Details</th>
                                                <th style="width: 15%">Asset Quantity</th>
                                                <th style="width: 15%">#Assign No</th>
                                                <th style="width: 15%">Assign Date</th>
                                                <th style="width: 10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject/Reuse List Tab -->
                <div class="tab-pane" id="Asset_Type">
                    <div class="box box-default">
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                                    <table id="data-table-inactive" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%">Employee/Department</th>
                                                <th style="width: 25%">Asset Details</th>
                                                <th style="width: 10%">Assign Date</th>
                                                <th style="width: 20%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /.tab-content -->
        </div> <!-- /.nav-tabs-custom -->
    </div>
</div>

@endsection

@section('script')
    <!-- jQuery -->
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <!-- DataTables Buttons & JSZip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <!-- Datepicker -->
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>

<script>
$(document).ready(function() {

    // Initialize Datepickers
    $('#from_date, #to_date').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        container: 'body'
    });

    // Initialize DataTable
    var table = $('#data-table').DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: true,
        bInfo: true,
        responsive: true,
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
            type: 'POST',
            url: "{{ url('/asset_assign_list') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: function(d){
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            }
        },
        columns: [
            { data: "employee" },
            { data: "asset" },
            { data: "qty_stockout" },
            { data: "store_no" },
            { data: "created_at" },
            { data: "Link" }
        ],
        order: [[0,'asc']]
    });

    // Move Excel button to custom div
    table.buttons().container().appendTo('#excel_btn');

    // Reload table on date change
    $('#from_date, #to_date').on('change', function(){
        table.ajax.reload();
    });

    // Lock asset action
    $(document).on('click', '.lock_asset', function(e){
        e.preventDefault();
        if(confirm('Are you sure to lock this..? After lock, you cannot edit or delete.')) {
            $.get($(this).attr('href')).then(function(data){
                alert(data.success);
                table.ajax.reload();
            });
        }
    });

});
</script>
@endsection
