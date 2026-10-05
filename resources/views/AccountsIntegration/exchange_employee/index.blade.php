
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
    <div class="box-body">
        <div class="nav-tabs-custom" style="margin-top:20px">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active">
                    <a href="#pending" data-toggle="tab">
                        Available Employee Exchange List
                    </a>
                </li>

                <li class="">
                    <a href="#processed" data-toggle="tab">
                        Employee Exchanged List
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="pending">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_acc_head_title_id">Head Name</label>
                                <select class="form-control hrm_acc_head_title_id" name="hrm_acc_head_title_id" id="hrm_acc_head_title_id">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_location_id">Location Name</label>
                                <select class="form-control hrm_location_id" name="hrm_location_id" id="hrm_location_id">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_category_id">Category Name</label>
                                <select class="form-control hrm_category_id" name="hrm_category_id" id="hrm_category_id">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12" style="overflow: auto;">
                            <table id="availableExchangeEmpDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>Acc Head Name</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Change Head</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="processed">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_acc_head_title_id2">Head Name</label>
                                <select class="form-control hrm_acc_head_title_id" name="hrm_acc_head_title_id2" id="hrm_acc_head_title_id2">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_location_id2">Location Name</label>
                                <select class="form-control hrm_location_id" name="hrm_location_id2" id="hrm_location_id2">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hrm_category_id2">Category Name</label>
                                <select class="form-control hrm_category_id" name="hrm_category_id2" id="hrm_category_id2">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12">
                            <table id="exchangedEmpDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Acc Head Name</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Change Head</th>
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
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $(".hrm_acc_head_title_id").select2({
            placeholder: "Search Head",
            width: '100%',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ route('account_head_title.dropdown.parent') }}",
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

        $(".hrm_location_id").select2({
            placeholder: "Search Location",
            width: '100%',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('location_list_data_all') }}",
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

        $(".hrm_category_id").select2({
            placeholder: "Search Category",
            width: '100%',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('category_list_data') }}",
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

        $('#hrm_acc_head_title_id, #hrm_location_id, #hrm_category_id').on('change', function() {
            availableExchangeEmpTable()
        })

        function availableExchangeEmpTable() {
            table = $('#availableExchangeEmpDatatable').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                // "aoColumnDefs": [ { "bVisible": false, "aTargets": [0] } ],
                "drawCallback": function() {
                    $(".datatable_head_title_id").select2({
                        placeholder: "Search Head",
                        width: '100%',
                        allowClear: true,
                        ajax: {
                            dataType: 'json',
                            url: "{{ route('account_head_title.dropdown.parent') }}",
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
                },

                ajax: {
                    url: "{{ route('change_head.index') }}",
                    type: "GET",
                    data: function(q) {
                        q.hrm_acc_head_title_id = $('#hrm_acc_head_title_id option:selected').val()
                        q.hrm_location_id = $('#hrm_location_id option:selected').val()
                        q.hrm_category_id = $('#hrm_category_id option:selected').val()
                    }
                },
                columns: [
                    // { "data": "id" },
                    { "data": "head_name" },
                    { "data": "employee_name" },
                    { "data": "depertment_name" },
                    { "data": "designation_name" },
                    { "data": "location_name" },
                    { "data": "category_name" },
                    { "data": "Link",
                       "mRender": function() {
                            return `
                                <select class="form-control datatable_head_title_id" name="hrm_acc_head_title_id" style="width:100%">
                                    <option value=""></option>
                                </select>
                            `
                       }
                    },
                    { "data": "Link",
                       "mRender": function() {
                            return `
                                <button type="button" class="changeHead btn btn-sm btn-success btn-flat" title="Update Record"><i class="fa fa-check"></i></button>
                            `
                       }
                    },
                ],

                "order": [[0, 'desc']]
            });
        }

        availableExchangeEmpTable();

        $('#availableExchangeEmpDatatable tbody').on('click', 'tr td .changeHead', function (e) {
            var employeeId = table.row($(this).parents('tr')).data().employee_id,
                accHead = $(this).parents('tr').find('.datatable_head_title_id option:selected').val(),
                datatable_head_title_id = $(this).parents('tr').find('.datatable_head_title_id');

                console.log(employeeId);

            if (isBlank(accHead)) {
                alert('Head can not be empty')

                $(datatable_head_title_id).focus()

                $(datatable_head_title_id).select2('open')

                return
            }

            else {
                $.post("{{ route('employee_change_head') }}", {
                    hrm_acc_head_title_id: accHead,
                    employee_id: employeeId
                }, function(data) {
                    if (data.status === true) {
                        $(".frmMsg").html("<div class='alert alert-success' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> "+ data.message +"</div>");

                        setTimeout(() => {
                            availableExchangeEmpTable();
                            exchangedEmpTable();
                        }, 500)

                        setTimeout(() => {
                            $(".frmMsg").html("")
                        }, 2000)
                    }

                    else if (data.status === false) {
                        $(".frmMsg").html("<div class='alert alert-danger' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error!</strong> "+ data.message +"</div>");
                    }
                })
            }
        })


        $('#hrm_acc_head_title_id2, #hrm_location_id2, #hrm_category_id2').on('change', function() {
            exchangedEmpTable()
        })
        function exchangedEmpTable() {
            table2 = $('#exchangedEmpDatatable').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                // "aoColumnDefs": [ { "bVisible": false, "aTargets": [0] } ],
                "drawCallback": function() {
                    $(".datatable_head_title_id").select2({
                        placeholder: "Search Head",
                        width: '100%',
                        allowClear: true,
                        ajax: {
                            dataType: 'json',
                            url: "{{ route('account_head_title.dropdown.parent') }}",
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
                },

                ajax: {
                    url: "{{ route('change_head.exchanged') }}",
                    type: "GET",
                    data: function(q) {
                        q.hrm_acc_head_title_id = $('#hrm_acc_head_title_id2 option:selected').val()
                        q.hrm_location_id = $('#hrm_location_id2 option:selected').val()
                        q.hrm_category_id = $('#hrm_category_id2 option:selected').val()
                    }
                },
                columns: [
                    // { "data": "id" },
                    { "data": "head_name" },
                    { "data": "employee_name" },
                    { "data": "depertment_name" },
                    { "data": "designation_name" },
                    { "data": "location_name" },
                    { "data": "category_name" },
                    { "data": "Link",
                       "mRender": function() {
                            return `
                                <select class="form-control datatable_head_title_id" name="hrm_acc_head_title_id" style="width:100%">
                                    <option value=""></option>
                                </select>
                            `
                       }
                    },
                    { "data": "Link",
                       "mRender": function(data, m, full) {
                            return `
                                <button type="button" class="changeHead btn btn-sm btn-success btn-flat" title="Update Record"><i class="fa fa-check"></i></button>
                                <a href="{{ url('exchanged_emp') }}/${full.id}/delete" class="deleteExchangedEmp btn btn-sm btn-danger btn-flat" title="Delete Record"><i class="fa fa-trash"></i></a>
                            `
                       }
                    },
                ],

                "order": [[0, 'desc']]
            });
        }

        exchangedEmpTable();

        $('#exchangedEmpDatatable tbody').on('click', 'tr td .changeHead', function (e) {
            var employeeId = table2.row($(this).parents('tr')).data().employee_id,
                accHead = $(this).parents('tr').find('.datatable_head_title_id option:selected').val(),
                datatable_head_title_id = $(this).parents('tr').find('.datatable_head_title_id');

            console.log(employeeId);

            if (isBlank(accHead)) {
                alert('Head can not be empty')

                $(datatable_head_title_id).focus()

                $(datatable_head_title_id).select2('open')

                return
            }

            else {
                $.post("{{ route('employee_change_head') }}", {
                    hrm_acc_head_title_id: accHead,
                    employee_id: employeeId
                }, function(data) {
                    if (data.status === true) {
                        $(".frmMsg").html("<div class='alert alert-success' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> "+ data.message +"</div>");

                        setTimeout(() => {
                            exchangedEmpTable()
                        }, 500)

                        setTimeout(() => {
                            $(".frmMsg").html("")
                        }, 2000)
                    }

                    else if (data.status === false) {
                        $(".frmMsg").html("<div class='alert alert-danger' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error!</strong> "+ data.message +"</div>");
                    }
                })
            }
        })

        $(document).on('click', '.deleteExchangedEmp', function (e) {
            e.preventDefault();

            if (confirm('Are you sure to delete this.?')) {
                $.get(this.href, function(data) {
                    if (data.status === true) {
                        $(".frmMsg").html("<div class='alert alert-success' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> "+ data.message +"</div>");

                        setTimeout(() => {
                            exchangedEmpTable()
                        }, 500)

                        setTimeout(() => {
                            $(".frmMsg").html("")
                        }, 2000)

                    } else if (data.status === false) {
                        $(".frmMsg").html("<div class='alert alert-danger' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error!</strong> "+ data.message +"</div>");
                    }
                })
            }
        })
    });
</script>
@endsection
