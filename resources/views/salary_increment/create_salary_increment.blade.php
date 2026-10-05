<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <style>
        #designation_list_table {
            width: 100% !important;
            table-layout: fixed;
        }

        #designation_list_table th,
        #designation_list_table td {
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle !important;
        }
    </style>
@endsection
<!-- content -->
@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Create Salary Increment & Promotion</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <form id="salaryIncrementForm">
            {{ csrf_field() }}
            <div class="row">

                <div
                    class="form-group has-feedback {{ $errors->has('apply_for') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Apply For</label>
                        <select class="form-control" name="apply_for" id="apply_for">
                            <option value="1">Increment</option>
                            <option value="2">Promotion</option>
                        </select>
                    </div>
                </div>


                <div
                    class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Effective Month</label>
                        <input type="text" class="form-control" placeholder="Month From" name="date_from" id="date_from"
                            value="" readonly required>
                    </div>
                </div>

                <div
                    class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Note</label>
                        <input type="text" class="form-control" placeholder="Note" name="note" id="note"
                            value="">
                    </div>
                </div>
                <div
                    class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-6 col-md-6 col-xs-12">
                        <label>Amount</label>
                        <input type="text" class="form-control" placeholder="amount" name="amount" id="amount">
                    </div>

                    <div class="col-lg-6 col-md-6 col-xs-12">
                        <label>Amount Type</label>
                        <select class="form-control" name="amount_type" id="amount_type">
                            <option value="1">%</option>
                            <option value="2">Tk.</option>
                        </select>
                    </div>

                </div>

                <div
                    class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Location Name</label>
                        <select class="form-control" name="location" id="location">
                            @foreach ($user_location as $keys)
                                @if ($keys->default_location == 1)
                                    <option value={{ $keys->id }} selected> {{ $keys->location_name }}</option>
                                @endif
                                <option value={{ $keys->id }}> {{ $keys->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div
                    class="form-group has-feedback {{ $errors->has('department') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Department Name</label>
                        <select class="form-control" id="department" name="department" style="width: 100%;">
                        </select>
                    </div>
                </div>
                <div
                    class="form-group has-feedback {{ $errors->has('category') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Employee Category</label>
                        <select class="form-control" id="category" name="category" style="width: 100%;">
                        </select>
                    </div>
                </div>


                <div
                    class="form-group has-feedback {{ $errors->has('section') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Sub-Department</label>
                        <select class="form-control" id="section" name="section" style="width: 100%;">
                        </select>
                    </div>
                </div>

                <div
                    class="form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Designation</label>
                        <select class="form-control designation" id="designation" name="designation" style="width: 100%;">
                        </select>
                    </div>
                </div>



                <div
                    class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Employee Name</label>
                        <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;">
                        </select>
                    </div>
                </div>
                <div
                    class="form-group has-feedback {{ $errors->has('salary_amount_search') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-8 col-md-8 col-xs-12">
                        <label>Salary Amount</label>
                        <input class="form-control col-lg-6" type="text" placeholder="Salary Amount"
                            id="salary_amount_search" name="salary_amount_search">
                    </div>
                    <div class="col-lg-4 col-md-4 col-xs-12">
                        <label>Search Type</label>
                        <select class="form-control" id="search_type" name="search_type" style="width: 100%;">
                            <option value="=">=</option>
                            <option value=">">
                                << /option>
                            <option value="<">></option>
                        </select>
                    </div>
                </div>
                <div
                    class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <label>Action</label>
                        <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn btn-info"
                            style="width: 100%;">
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow-x:auto;">
                                <table id="designation_list_table" class="cell-border table table-bordered table-hover"
                                    cellspacing="0" style="width: 100%; min-width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%"><input name="select_all" value="1"
                                                    id="example-select-all" type="checkbox" />_All</th>
                                            <th style="width: 10%">Employee Name</th>
                                            <th style="width: 10%">Joining Date</th>
                                            <th style="width: 10%">Department</th>
                                            <th style="width: 15%">Designation</th>
                                            <th style="width: 10%">Location</th>
                                            <th style="width: 8%">Salary</th>
                                            <th style="width: 8%">increment</th>
                                            <th style="width: 8%">amount</th>
                                            <th style="width: 8%">Grade Name</th>
                                            <th style="width: 8%">Note(Last Increment)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            {{-- <input type="submit" value="Submit" class=" btn-sm btn-success block btn-flat btn"
                                style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;"> --}}
                            <input type="button" id="submitBtn" value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
<!-- script -->
@section('script')
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('dist/js/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(document).ready(function($) {


            $('#date_from').datepicker({
                autoclose: true,
                minViewMode: 1,
                format: 'dd-mm-yyyy'
            });



            $('#department').select2({
                placeholder: 'Enter department',
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

            $('.designation').select2({
                placeholder: 'Enter designation',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/designation_list_data',
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

            $('#employee_name').select2({
                placeholder: 'Enter Employee Name',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ URL::to('/') }}/join_employee_list',
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

            $('#category').select2({
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


            $('#section').select2({
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


            $("#search").click(function() {

                if ($("#date_from").val() == '') {
                    alert("Please Select Month From");
                    return;
                }


                if ($("#amount").val() == '') {
                    alert("Please Fillup Amount");
                    return;
                }


                if ($("#note").val() == '') {
                    alert("Please Fillup Note");
                    return;
                }

                if ($("#location").val() == null) {
                    location_id = 0;
                } else {
                    location_id = $("#location").val();
                }

                if ($("#employee_name").val() == null) {
                    employee_id = 0;
                } else {
                    employee_id = $("#employee_name").val();
                }

                if ($("#department").val() == null) {
                    department_id = 0;
                } else {
                    department_id = $("#department").val();
                }

                if ($("#designation").val() == null) {
                    designation_id = 0;
                } else {
                    designation_id = $("#designation").val();
                }

                if ($("#category").val() == null) {
                    category_id = 0;
                } else {
                    category_id = $("#category").val();
                }

                if ($("#section").val() == null) {
                    section_id = 0;
                } else {
                    section_id = $("#section").val();
                }





                $.ajax({
                    type: 'POST',
                    url: "{{ URL::to('/') }}/salary_increment_listdata",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        punch_date: $("#process_date").val(),
                        location: location_id,
                        department_id: department_id,
                        designation_id: designation_id,
                        employee_id: employee_id,
                        category_id: category_id,
                        section_id: section_id,
                        amount_type: $("#amount_type").val(),
                        amount: $("#amount").val(),
                        salary_amount_search: $("#salary_amount_search").val(),
                        search_type: $("#search_type").val(),
                    },
                    dataType: 'json',
                    success: function(data) {
                        var dataSet = data.data;
                        table = $('#designation_list_table').DataTable({
                            destroy: true,
                            paging: false,
                            searching: true,
                            ordering: true,
                            bInfo: true,
                            autoWidth: false,
                            "data": dataSet,

                            drawCallback: function() {
                                $('.designation_name').select2({
                                    placeholder: 'Enter Designation',
                                    allowClear: true,
                                    ajax: {
                                        dataType: 'json',
                                        url: '{{ URL::to('/') }}/designation_list_data',
                                        delay: 250,
                                        data: function(params) {
                                            return {
                                                term: params.term
                                            }
                                        },
                                        processResults: function(data,
                                            params) {
                                            params.page = params.page ||
                                                1;
                                            return {
                                                results: data
                                            };
                                        },
                                        cache: true
                                    }
                                });

                                $('.grade_name').select2({
                                    placeholder: 'Enter Grade',
                                    allowClear: true,
                                    ajax: {
                                        dataType: 'json',
                                        url: "{{ URL::to('/') }}/salarygrade_list?status=1",
                                        delay: 250,
                                        data: function(params) {
                                            return {
                                                term: params.term
                                            };
                                        },
                                        processResults: function(data) {
                                            return {
                                                results: data
                                            };
                                        }
                                    }
                                });
                            },
                            "columns": [

                                {
                                    "data": "checkbox",
                                    "mRender": function(data, type, full) {
                                        return '<input type="checkbox" name="id[]" value="' +
                                            full.id + '">';
                                    }
                                },
                                {
                                    "data": "employee_name"
                                },
                                {
                                    "data": "joining_date"
                                },
                                {
                                    "data": "depertment_name"
                                },

                                {
                                    "data": null,
                                    render: function(data, type, row) {
                                        return '<select class="form-control designation_name" style="width: 100%;" name="hrm_designation_id[' +
                                            row.id + ']"><option value="' + row
                                            .hrm_designation_id + '">' + row
                                            .designation_name +
                                            '</option></select>';
                                    }
                                },
                                {
                                    "data": "location_name"
                                },
                                {
                                    "data": "text",
                                    "mRender": function(data, type, full) {
                                        return '<input type="text" class="salary"  style="width:100px"  name="salaryamount[' +
                                            full.id + ']"  value="' + full
                                            .salary_amount + '" readonly>';
                                    }
                                },

                                {
                                    "data": "text",
                                    "mRender": function(data, type, full) {
                                        let increment = full.increment_amount -
                                            full.salary_amount;
                                        return '<input type="number" step="any" class="increment" style="width:100px" name="increment[' +
                                            full.id + ']"  value="' +
                                            increment + '">';
                                    }
                                },
                                {
                                    "data": "text",
                                    "mRender": function(data, type, full) {
                                        return '<input type="text" readonly class="dateto" style="width:100px" name="new_salary_amount[' +
                                            full.id + ']"  value="' + full
                                            .increment_amount + '">';
                                    }
                                },
                                //   { "data": "grade_name" },
                                {
                                    "data": null,
                                    render: function(data, type, row) {

                                        return `
                                            <select class="form-control grade_name"
                                                    style="width:100%;"
                                                    name="hrm_salary_grade_id[${row.id}]">
                                                <option value="${row.hrm_salary_grade_id}" selected>
                                                    ${row.grade_name}
                                                </option>
                                            </select>
                                        `;

                                    }
                                },

                                    {
                                        "data": null,
                                        "mRender": function(data, type, full) {
                                            return '<label>' + full.last_increment_note + '</label>' +
                                                   '<input type="hidden" name="last_increment_note[' + full.id + ']" value="' + full.last_increment_note + '">';
                                        }
                                    }
                            ],
                        });
                    }
                });

            });


            $(document).on('input', '.increment', function() {
                let $input = $(this);
                let $tr = $input.closest('tr');
                let increment = $input.val();
                let salary = $tr.find('.salary').val();
                let new_salary = Number(salary) + Number(increment);
                $tr.find('.dateto').val(new_salary)
                // console.log('Increment Value:', increment);
                // console.log('Second Column Value:', salary);
            });

            // Handle click on "Select all" control
            $('#example-select-all').on('click', function() {
                // Check/uncheck all checkboxes in the table
                var rows = table.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });


            // Handle click on checkbox to set state of "Select all" control
            $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function() {
                // If checkbox is not checked
                if (!this.checked) {
                    var el = $('#example-select-all').get(0);
                    // If "Select all" control is checked and has 'indeterminate' property
                    if (el && el.checked && ('indeterminate' in el)) {
                        // Set visual state of "Select all" control
                        // as 'indeterminate'
                        el.indeterminate = true;
                    }
                }
            });


            $('#submitBtn').on('click', function() {

                if ($("#date_from").val() == '') {
                    alert("Please Select Month From");
                    return;
                }
                if ($("#amount").val() == '') {
                    alert("Please Fillup Amount");
                    return;
                }
                if ($("#note").val() == '') {
                    alert("Please Fillup Note");
                    return;
                }

                var payload = {
                    apply_for: $('#apply_for').val(),
                    date_from: $('#date_from').val(),
                    note: $('#note').val(),
                    amount: $('#amount').val(),
                    amount_type: $('#amount_type').val(),
                    location: $('#location').val() || 0,

                    id: [],
                    hrm_designation_id: {},
                    salaryamount: {},
                    increment: {},
                    new_salary_amount: {},
                    hrm_salary_grade_id: {},
                    last_increment_note: {}
                };

                var rows = table.rows({ 'search': 'applied' }).nodes();

                $(rows).each(function() {
                    var $row = $(this);
                    var $checkbox = $row.find('input[type="checkbox"]');

                    if ($checkbox.is(':checked')) {
                        var jobid = $checkbox.val();

                        payload.id.push(jobid);
                        payload.hrm_designation_id[jobid]   = $row.find('.designation_name').val();
                        payload.salaryamount[jobid]         = $row.find('.salary').val();
                        payload.increment[jobid]            = $row.find('.increment').val();
                        payload.new_salary_amount[jobid]    = $row.find('.dateto').val();
                        payload.hrm_salary_grade_id[jobid]  = $row.find('.grade_name').val();
                        payload.last_increment_note[jobid]  = $row.find('input[name^="last_increment_note"]').val();
                    }
                });

                if (payload.id.length === 0) {
                    alert("Please Select Employee and Resubmit!");
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('salaryincrement') }}",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    dataType: 'text',   // <-- গুরুত্বপূর্ণ: response জোর করে JSON parse করতে দেব না
                    beforeSend: function() {
                        $('#submitBtn').prop('disabled', true).val('Submitting...');
                    },
                    success: function(response) {

                        var json = null;
                        try {
                            json = JSON.parse(response);
                        } catch (e) {
                            json = null;
                        }

                        if (json && json.success === false) {
                            // Validation fail / Exception case (Controller থেকে JSON আসছে)
                            var errMsg = '';
                            if (json.errors) {
                                if (typeof json.errors === 'string') {
                                    errMsg = json.errors;
                                } else {
                                    $.each(json.errors, function(key, val) {
                                        errMsg += (Array.isArray(val) ? val.join('\n') : val) + '\n';
                                    });
                                }
                            }
                            alert(errMsg || 'Something went wrong!');
                            $('#submitBtn').prop('disabled', false).val('Submit');

                        } else {
                            // Success case: response হলো redirect-follow হওয়া
                            // index page-এর সম্পূর্ণ HTML, flash message সমেত।
                            // এটাকেই পুরো ডকুমেন্ট হিসেবে বসিয়ে দিচ্ছি।
                            document.open();
                            document.write(response);
                            document.close();
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Something went wrong. Please try again.');
                        $('#submitBtn').prop('disabled', false).val('Submit');
                    }
                });

            });



        });
    </script>
@endsection
