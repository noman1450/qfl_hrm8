@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<form class="dynamicFormSubmit" data-table-name="#new_enrollment_list" action="{{ route('employee.enrollment.confirmation') }}" redirectUrl="{{ route('employee.enroll') }}" method="post">
    @csrf

    <div class="box box-default">
        <div class="box-body">
            <input type="hidden" name="hrm_employee_job_info_id" value="{{ $employee_data->hrm_employee_job_info_id }}">
            <input type="hidden" name="hrm_designation_id" value="{{ $employee_data->hrm_designation_id }}">

            <div class="row" style="margin-top: 15px;">

                <div class="col-md-3">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Employee Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Name of Employee:</strong> {{ $employee_data->employee_name }}</p>
                                    <p><strong>Designation Name:</strong> {{ $employee_data->designation_name }}</p>
                                    <p><strong>Job Placement:</strong> {{ $employee_data->location_name }}</p>
                                    <p><strong>Job Placement:</strong> {{ $employee_data->location_name }}</p>
                                    <p><strong>Joining Date:</strong> {{ $employee_data->joining_date }}</p>
                                    <p><strong>Salary Amount:</strong> {{ $employee_data->gross_salary }}</p>
                                </div>
                                {{-- <div class="col-md-6">
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="box box-default">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Salary Grade <span class="text-danger">*</span></label>
                                <select id="salary_grade" class="form-control" name="salary_grade" style="width: 100%;">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Input Salary Amount <span class="text-danger">*</span></label>
                                <input type="number" step="any" value="" class="form-control" id="new_salary" name="new_salary" placeholder="Input Salary Amount">
                            </div>
                            <div class="form-group">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select id="payment_mode" class="form-control" name="payment_mode" style="width: 100%;">
                                    <option value="1">Cash</option>
                                    <option value="2" {{ ($bank_info && $bank_info->hrm_bank_id) ? 'selected' : '' }}>Bank</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 show_hide" style="display:none;">
                    <div class="box box-default">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Bank <span class="text-danger">*</span></label>
                                <select id="bank_id" class="form-control" name="hrm_bank_id" style="width: 100%;">
                                    @if($bank_info)
                                        <option value="{{$bank_info->hrm_bank_id}}" selected>{{ $bank_info->bank_name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Account No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $bank_info->account_number ?? '' }}" id="account_no" name="account_no" placeholder="Account No.">
                            </div>
                            <div class="form-group">
                                <label>By Bank Payment (%)</label>
                                <input type="text" class="form-control" id="by_bank_percent" name="by_bank_percent" value="100" max="100" placeholder="By Bank Payment (%)">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row" style="margin-top: 15px;">
                <div class="col-lg-12">
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Addition List</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group" style="overflow: auto;">
                                        <table id="addition_list" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">Select</th>
                                                    <th style="width: 40%">Salary Head</th>
                                                    <th style="width: 20%">Amount</th>
                                                    <th style="width: 15%">Type</th>
                                                    <th style="width: 20%">Actual Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" style="text-align:right">Total:</th>
                                                    <th id="addition_total">0.00</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Deduction List</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group" style="overflow: auto;">
                                        <table id="deduction_list" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">Select</th>
                                                    <th style="width: 40%">Salary Head</th>
                                                    <th style="width: 20%">Amount</th>
                                                    <th style="width: 15%">Type</th>
                                                    <th style="width: 20%">Actual Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" style="text-align:right">Total:</th>
                                                    <th id="deduction_total">0.00</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div style="margin-top: 10px;">Net Salary: <span id="net_salary"> </span></div>

        </div>

        <div class="box-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-success btn-sm submitBtn">
                    <span class="ladda-label">Enroll Approve</span>
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {

        $('#salary_grade').select2({
            placeholder: 'Enter a Salary Grade',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/salarygrade_list",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            }
        });

        $('#bank_id').select2({
            placeholder: 'Bank',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/get_bank_list",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            }
        });

        $(document).on('change', '#salary_grade', function() {
            let salaryGradeId = $(this).val();
            addition_list(salaryGradeId)
            deduction_list(salaryGradeId)
        });

        let payment_mode = $('#payment_mode option:selected').val();
        payment_func(payment_mode);

        function payment_func(payment_mode){
            if(payment_mode == 2){
                $('.show_hide').show();
            } else {
                $('.show_hide').hide();
            }
        }

        $(document).on('change', '#payment_mode', function() {
            let payment_mode = $(this).val();
            payment_func(payment_mode);
        });

        function addition_list(master_id) {
            $("#addition_list").DataTable({
                destroy: true
                , responsive: true
                , processing: true
                , serverSide: true
                , paging: false
                , lengthChange: true
                , searching: true
                , ordering: true
                , info: false
                , autoWidth: false
                , width: "100%",
                ajax: {
                    url: "{{ url('find_salary_heads') }}"
                    , type: "GET"
                    , dataType: "json"
                    , data: {
                        generate_type: 1
                        , master_id: master_id
                    }
                },
                initComplete: function() {
                    let fields = $('.select-head');
                    $.each(fields, function(_, field) {
                        if ($(field).is(':checked')) {
                            $(field).parents('tr').find('.field_show_hide').show()
                        } else {
                            $(field).parents('tr').find('.field_show_hide').hide()
                        }
                    })
                },
                columns: [
                    { data: "Select", orderable: false, searchable: false }
                    , { data: "salary_head" }
                    , { data: "Amount", orderable: false, searchable: false }
                    , { data: "Type", orderable: false, searchable: false }
                    , {
                        data: null, orderable: false, searchable: false,
                        render: function(data, type, row) {
                            return `<input type="number" style="height:30px" readonly class="did-floating-input actual-amount form-control field_show_hide" value="0" />`;
                        }
                    }
                ]
                , order: [[0, 'desc']]
            })
        }

        function deduction_list(master_id) {
            $("#deduction_list").DataTable({
                destroy: true
                , responsive: true
                , processing: true
                , serverSide: true
                , paging: false
                , lengthChange: true
                , searching: true
                , ordering: true
                , info: false
                , autoWidth: false
                , width: "100%"
                , ajax: {
                    url: "{{ url('find_salary_heads') }}"
                    , type: "GET"
                    , dataType: "json"
                    , data: {
                        generate_type: 2
                        , master_id: master_id
                    }
                },
                initComplete: function() {
                    let fields = $('.select-head');
                    $.each(fields, function(_, field) {
                        let $row = $(field).closest('tr');
                        let amount = $row.find('.input-amount').val();
                        let isChecked = $row.find('input[type="checkbox"]').is(':checked');
                        if (isChecked > 0) {
                            $(field).parents('tr').find('.field_show_hide').show()
                        } else {
                            $(field).parents('tr').find('.field_show_hide').hide()
                        }
                    })
                },
                columns: [
                    { data: "Select", orderable: false, searchable: false }
                    , { data: "salary_head" }
                    , { data: "Amount", orderable: false, searchable: false }
                    , { data: "Type", orderable: false, searchable: false }
                    , {
                        data: null, orderable: false, searchable: false,
                        render: function(data, type, row) {
                            return `<input type="number" style="height:30px" readonly class="did-floating-input actual-amount form-control field_show_hide" value="0" />`;
                        }
                    }
                ]
                , order: [[0, 'desc']]
            })
        }

        $(document).on('click', '.select-head', function() {
            if ($(this).is(':checked')) {
                $(this).parents('tr').find('.field_show_hide').show()
            } else {
                $(this).parents('tr').find('.field_show_hide').hide()
            }
        })


    });

    $(document).on('input change', '#new_salary, .amount_type,.input-amount', function() {
        let new_salary = parseFloat($('#new_salary').val()) || 0;

        let total_addition = 0;
        $('#addition_list tbody tr').each(function() {
            let $row = $(this);
            let amount = parseFloat($row.find('.input-amount').val()) || 0;
            let type = $row.find('.amount_type option:selected').val();
            console.log(type, amount)
            let actual = (type == 1) ?
                (amount * new_salary / 100) :
                amount;
            total_addition += actual;
            $row.find('.actual-amount').val(actual.toFixed(2));
            $('#addition_total').text(total_addition.toFixed(2));
        });

        let total_deduction = 0;
        $('#deduction_list tbody tr').each(function() {
            let $row = $(this);
            let amount = parseFloat($row.find('.input-amount').val()) || 0;
            let type = $row.find('.amount_type option:selected').val();
            let actual = (type == 1) ?
                (amount * new_salary / 100) :
                amount;
            total_deduction += actual;
            $row.find('.actual-amount').val(actual.toFixed(2));
            $('#deduction_total').text(total_deduction.toFixed(2));
        });

        $('#net_salary').text((total_addition - total_deduction).toFixed(2))
    });
</script>
@endsection
