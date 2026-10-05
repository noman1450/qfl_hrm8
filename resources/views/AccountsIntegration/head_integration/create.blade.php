
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


<form class="dynamicFormSubmit" data-table-name="#headIntegrationDatatable" action="{{ route('account_head_integration.store') }}" method="post">
    @csrf

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">A/C Ledger Head Integration </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>



        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <label>Head Name</label>
                </div>

                <div class="col-md-6">
                    <label>Accounts Ledger Name</label>
                </div>
            </div>

                @foreach ($parentHeads as $key)

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{ $key->head_name_details }}" disabled>
                                <input type="hidden" class="form-control" name="hrm_acc_head_title_id[]" value="{{ $key->id }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control accounts_ledger_head_id" name="accounts_ledger_head_id[]" >
                                    <option value="{{$key->accounts_ledger_head_id}}">{{$key->accounts_ledger_head_name}}</option>
                                </select>
                            </div>
                        </div>



                    </div>
                @endforeach


        </div>

    </div>



<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Cost Center Setup </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <label>Cost Center Head Name</label>
            </div>

            <div class="col-md-6">
                <label>Cost Center Name</label>
            </div>
        </div>

            @foreach ($costCenter as $key)

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" value="{{ $key->head_name_details }}" disabled>
                            <input type="hidden" class="form-control" name="hrm_acc_head_title_id[]" value="{{ $key->id }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control costcenter" name="accounts_ledger_head_id[]" >
                                <option value="{{$key->accounts_ledger_head_id}}">{{$key->accounts_ledger_head_name}}</option>
                            </select>
                        </div>
                    </div>



                </div>
            @endforeach

    </div>

</div>


    <div class="box-footer">
        <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
    </div>

</form>


@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $(".accounts_ledger_head_id").select2({
        placeholder: "Search Accounts Ledger Head",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('getAccountsLedgerHeadId') }}",
            delay: 100,
            data: function(params) {
                return {
                    term: params.term
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data
                };
            },
        },
    });


    $(".costcenter").select2({
        placeholder: "Search Cost Center Name",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('accCostCenter') }}",
            delay: 100,
            data: function(params) {
                return {
                    term: params.term
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data
                };
            },
        },
    });



</script>

@endsection



