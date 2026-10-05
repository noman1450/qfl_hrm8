@extends('layouts.main')
@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Auto Shift</h3>

            @if(Session::has('message'))
                <p class="alert alert-{{ session('success') == true ? 'success' : 'danger' }} mt-2 mb-2">{{ Session::get('message') }}</p>
            @endif

            <div class="row" style="margin-left:10px; ">

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control onchange" id="location" name="location" style="width: 100%;">
                        @foreach ($default_user_location as $keys)
                            <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control working_shift onchange" id="working_shift" name="working_shift"
                            style="width: 100%;">
                    </select>
                </div>

{{--                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">--}}
{{--                    <select class="form-control " id="weekends" name="weekends"--}}
{{--                            style="width: 100%;">--}}
{{--                    </select>--}}
{{--                </div>--}}

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control onchange" id="employee_name" name="employee_name" style="width: 100%;">
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control onchange" id="department" name="department" style="width: 100%;">
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control onchange" id="designation" name="designation" style="width: 100%;">
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0; padding-top: 10px;">
                    <select class="form-control onchange" id="section" name="section" style="width: 100%;">
                    </select>
                </div>


            </div>


            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">

            <div class="row">
                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto">
                    <table id="autoShiftTable" class="table table-hover table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th style="width: 30%">Employee Name</th>
                            {{-- <th style="width: 15%">Department</th> --}}
                            {{-- <th style="width: 15%">Designation</th> --}}
                            <th style="width: 30%">Shift</th>
                            <th style="width: 20%">Last Change</th>
                            <th style="width: 20%">Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>


{{--            <input type="submit" value="Submit" class=" btn-sm btn-primary"--}}
{{--                   style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">--}}

        </div>
    </div>

@endsection


@section('script')

    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>

    <script>

        $(document).ready(function ($) {

            $('.working_shift').select2({
                placeholder: 'Enter working shift',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/shift_list_data',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });

            $('#employee_name').select2({
                placeholder: 'Enter Employee Name',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/join_employee_list',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });

            $('#designation').select2({
                placeholder: 'Enter designation',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/designation_list_data',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });

            $('#section').select2({
                placeholder: 'Enter Sub-department',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/section_list_data',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });

            //  $role = $('#location').select2({
            //     placeholder: 'Choose Location Mandatory',
            //     allowClear: true,
            //     ajax: {
            //         dataType: 'json',
            //         url: '{{URL::to('/')}}/location_list_data',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 term: params.term
            //             }
            //         },
            //         processResults: function (data, params) {
            //             params.page = params.page || 1;
            //             return {
            //                 results: data
            //             };
            //         },
            //         cache: true
            //     }
            // });

            // $role.on('select2:select', function (e) {
            //     loadTable();
            // });

            // $role.on('select2:unselect', function (e) {
            //     $('#location').val(null).trigger("change");
            //     loadTable();
            // });

            $(".onchange").change(function(){
                loadTable();
            });



            $('#department').select2({
                placeholder: 'Enter department',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/depertment_list_data',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });
            loadTable();

            function loadTable() {
                table = $('#autoShiftTable').DataTable({
                    destroy: true,
                    response: true,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    width: '100%',
                    aoColumnDefs: [{"bVisible": false, "aTargets": [0]}],

                    ajax: {
                        type: 'POST',
                        url: "{{URL::to('/')}}/auto_shift_list",
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        dataType: 'json',
                        data: function (query) {
                            query.hrm_employee_id = $("#employee_name").val()
                            query.hrm_shift_id = $("#working_shift").val()
                            query.hrm_depertment_id = $("#department").val()
                            query.hrm_designation_id = $("#designation").val()
                            query.hrm_section_id = $("#section").val()
                            query.hrm_location_id = $("#location").val()
                        },
                    },
                    columns: [
                        {data: "employee_name"},
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                            }
                        },
                        // {data: "id"},
                        // {data: "depertment_name"},
                        // {data: "designation_name"},
                        {data: "shift_name"},
                        {data: "last_change"},
                        {data: "Link", name: "link", orderable: false, searchable: false}

                    ],
                    "order": [[0, 'asc']]
                })
            }
        });
    </script>
@endsection
