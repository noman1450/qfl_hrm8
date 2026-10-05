{{-- new_enrollment_list --}}
@extends('layouts.main')

{{-- styles --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <style type="text/css">
        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 5px;
        }

        table.dataTable thead>tr>th {
            padding-right: 25px;
        }

        .table>tbody {
            font-size: small;
        }

        .table>thead {
            font-size: smaller;
        }
    </style>
@endsection

@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Waiting For Enrollment</h3>
        </div>
        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <select class="form-control col-lg-12 onchange" id="location" name="location" style="width: 100%;"
                    required>
                    <option value="{{ $default_user_location->id }}" selected>{{ $default_user_location->location_name }}
                    </option>
                </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <select class="form-control" id="department" name="department" style="width: 100%;"></select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <select class="form-control section" id="section" name="section" style="width: 100%;"></select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <select class="form-control category" id="category" name="category" style="width: 100%;"></select>
            </div>

        </div>

        <div class="box-body">
            <div class="row">
                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                    <table id="new_enrollment_list" class="table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 0%"></th>
                                <th style="width: 50px"></th>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Location</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            $(".onchange").change(function() {
                loadTable();
            });

            $department = $('#department').select2({
                placeholder: 'Enter Department',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/depertment_list_data',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $department.on('select2:select', function(e) {
                loadTable();
            });
            $department.on('select2:unselect', function(e) {
                $('#department').val(null).trigger("change");
                loadTable();
            });

            $section = $('#section').select2({
                placeholder: 'Enter Sub-department',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/section_list_data',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $section.on('select2:select', function(e) {
                loadTable();
            });
            $section.on('select2:unselect', function(e) {
                $('#section').val(null).trigger("change");
                loadTable();
            });

            $category = $('#category').select2({
                placeholder: 'Enter Employee Category',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/category_list_data',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $category.on('select2:select', function(e) {
                loadTable();
            });
            $category.on('select2:unselect', function(e) {
                $('#category').val(null).trigger("change");
                loadTable();
            });

            $role = $('#location').select2({
                placeholder: 'Choose Location',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/location_list_data',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            $role.on('select2:select', function(e) {
                loadTable();
            });
            $role.on('select2:unselect', function(e) {
                $('#location').val(null).trigger("change");
                loadTable();
            });

            function loadTable() {
                table = $('#new_enrollment_list').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ordering: true,
                    bInfo: true,
                    paging: true,
                    autoWidth: false,
                    aoColumnDefs: [{
                        "bVisible": false,
                        "aTargets": [0]
                    }],
                    ajax: {
                        url: "{{ url()->current() }}",
                        type: "GET",
                        dataType: "json",
                        data: function(query) {
                            query.location = $("#location").val();
                            query.department = $("#department").val();
                            query.section = $("#section").val();
                            query.category = $("#category").val();
                        }
                    },
                    columns: [
                        {data: "id"},
                        {
                            render: function(data, type, row) {
                                if (row.Images) {
                                    return '<img src="{{ asset('uploads') }}/' + row.Images +
                                        '" style="height:30px; width:30px; border-radius:30px;">';
                                }
                                return '<img src="{{ asset('dummy.jpg') }}" style="height:30px; width:30px; border-radius:30px;">';
                            },
                            orderable: false,
                            searchable: false
                        },
                        {data: "EmployeeName"},
                        {data: "employee_code"},
                        {data: "depertment_name"},
                        {data: "designation_name"},
                        {data: "location_name"},
                        {data: "Action", orderable: false, searchable: false}
                    ],
                    order: [
                        [0, 'desc']
                    ]
                });
            }

            loadTable();

        });
    </script>
@endsection
