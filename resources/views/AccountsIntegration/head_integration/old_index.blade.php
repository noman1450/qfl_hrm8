
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style>
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
    }
    table.dataTable thead > tr > th {
      padding-right: 25px;
    }
    .table>tbody{
      font-size: small;
    }
    /* .table>thead{
      font-size: smaller;
    } */
    td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
    }
    tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
    }
</style>
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Head Integration List</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>


    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <a
                    href="{{ route('account_head_integration.create') }}"
                    footer-none
                    data-title="Create Head Integration"
                    data-modal-size="lg"
                    class="btn-success btn btn-sm button pull-left btn-flat modalLink"
                    style="font-size:12px;font-weight:bold;margin-bottom:20px"
                >
                    Create
                </a>
            </div>

            <div class="form-group col-md-8" style="overflow: auto;">
                <table id="headIntegrationDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Head Name</th>
                            <th>Ledger Head Name</th>
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
    $(document).ready(function($) {
        function dataLoad() {
            table = $('#headIntegrationDatatable').DataTable( {
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                "aoColumnDefs": [ { "bVisible": false, "aTargets": [0] } ],

                ajax: {
                    url: "{{ route('account_head_integration.index') }}",
                    type: "GET",
                },
                columns: [
                    { "data": "id" },
                    { "data": "head_name" },
                    { "data": "accounts_ledger_head_name" },
                    { "data": "Link", name: 'action', orderable: false, searchable: false},
                ],

                "order": [[0, 'desc']]
            });
        }

        dataLoad();

        confirmationWithAjaxReload({
            selector: '.deleteHeadIntegration',
            refreshTable: dataLoad
        })
    });
</script>
@endsection
