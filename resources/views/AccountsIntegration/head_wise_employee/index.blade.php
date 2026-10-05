
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
        <div class=" col-xs-12 ">
            <h3 class="box-title">Head Wise Employee List</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group">
            <a
                href="{{ route('account_head_wise_employee_setup.create') }}"
                class="btn-success btn btn-sm button pull-left btn-flat"
                style="font-size:12px;font-weight:bold;margin-bottom:20px"
            >
                Create
            </a>
        </div>

        <div class="col-lg-4 col-md-4 col-xs-12 form-group" >
          <select  class="form-control col-lg-12 onchange" id="hrm_acc_journal_type_id" name="hrm_acc_journal_type_id" style="width: 100%;"  required>
          </select>
        </div>

    </div>


    <div class="box-body">
        <div class="row">


            <div class="form-group col-md-10" style="overflow: auto;">
                <table id="headWiseEmpDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Head Name</th>
                            <th>Journal Type</th>
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
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script>
    $(document).ready(function() {
        function format(d) {
            console.log(d);
            return `
                <table class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <tr>
                        <td>Location Name</td>
                        <td>Category Name</td>
                        <td>Salary Head Name</td>
                    </tr>
                    <tr>
                        <td>${d.LocationName}</td>
                        <td>${d.CategoryName}</td>
                        <td>${d.SalaryHeadName}</td>
                    </tr>
                </table>
            `
        }

        function dataLoad() {
            table = $('#headWiseEmpDatatable').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     false,
                "aoColumnDefs": [ { "bVisible": false, "aTargets": [0] } ],

                ajax: {
                    url: "{{ route('account_head_wise_employee_setup.index') }}",
                    type: "GET",

                    data: function (query) {
                    query.hrm_acc_journal_type_id = $("#hrm_acc_journal_type_id").val()
                    },

                },
                columns: [
                    { "data": "id" },
                    {
                        "className":      'details-control',
                        "orderable":      true,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "head_name" },
                    { "data": "journal_type_name" },
                    { "data": "Link", orderable: false, searchable: false },
                ],

                "order": [[0, 'desc']]
            });
        }

        dataLoad();

        $('#headWiseEmpDatatable tbody').on('click', 'td.details-control', function () {
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

        confirmationWithAjaxReload({
            selector: '.deleteHeadWiseEmployeeSetup',
            refreshTable: dataLoad
        })




        $(".onchange").change(function() {
            dataLoad();
        });



        $("#hrm_acc_journal_type_id").select2({
            placeholder: "Search Journal Type",
            width: '100%',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ route('account_journal_type.dropdown') }}",
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






    });
</script>
@endsection
