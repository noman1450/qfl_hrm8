{{-- {!! Form::open(array('route' => array('employeesalary.update', $master_data->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'frm_process', 'method'=>'PUT')) !!} --}}
<form action="{{ route('employee_salary.create') }}" class="dynamicFormSubmit"
    data-table-name="#employeeSalaryDatatable" method="post">
    @csrf




    <div class="row">
        <input type="hidden" name="employee_name" value="{{ $master_data->employee_id }}">
        <input type="hidden" name="location_id" value="{{ $location }}">
        <input type="hidden" name="hrm_employee_job_info_id" value="{{ $master_data->hrm_employee_job_info_id }}">

        <div
            class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Year</label>
                <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>
                    <option value={{ $year_id}} selected>{{ $year_id}}</option>
               </select>
            </div>
        </div>
        <div
            class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Month</label>
                <select  class="form-control col-lg-12 onchange" id="month" name="month_name" style="width: 100%;" >
                    <option value={{ $month_id}} selected> {{ $month_name}}</option>
               </select>
            </div>
        </div>



        <div
            class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Employee Name</label>
                <input type="text" class="form-control" value="{{ $master_data->employee_name }}" disabled>
            </div>
        </div>

        <div
            class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Department</label>
                <input class="form-control" type="text" name="depertment_name"
                    value="{{ $master_data->depertment_name }}" required disabled>
            </div>
        </div>

        <div
            class="form-group has-feedback {{ $errors->has('designation_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Designation</label>
                <input class="form-control" type="text" name="designation_name"
                    value="{{ $master_data->designation_name }}" required disabled>
            </div>
        </div>

        <div
            class="form-group has-feedback {{ $errors->has('salary_amount') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Basic/Gross Salary</label>
                <input class="form-control" onchange="calculation()" type="text" id="salary_amount"
                    name="salary_amount" value="{{ $master_data->salary_amount }}" required>

                @if ($errors->has('salary_amount'))
                    <span class="help-block">
                        <strong>{{ $errors->first('salary_amount') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        <div
            class="form-group has-feedback {{ $errors->has('payment_mode') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Payment Mode</label>
                <select class="form-control" name="payment_mode" id="payment_mode">
                    @if ($master_data->payment_mode == 1)
                        <option value="1" selected>Cash</option>
                        <option value="2">Bank</option>
                    @else
                        <option value="1">Cash</option>
                        <option value="2" selected>Bank</option>
                    @endif
                </select>

                @if ($errors->has('payment_mode'))
                    <span class="help-block">
                        <strong>{{ $errors->first('payment_mode') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        <div
            class="form-group has-feedback {{ $errors->has('accounts_code') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Acoounts Code</label>
                <input class="form-control" onchange="cal()" type="text" name="accounts_code"
                    value="{{ $master_data->accounts_code }}" placeholder="Accounts Code">

                @if ($errors->has('accounts_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('accounts_code') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        <div
            class="form-group has-feedback {{ $errors->has('bank_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Bank Name</label>
                <select class="form-control" name="hrm_bank_id" id="bank_name">
                    @foreach ($bank_name as $keys)
                        @if ($master_data->hrm_bank_id == $keys->id)
                            <option value={{ $keys->id }} selected>{{ $keys->bank_name }}</option>
                        @else
                            <option value={{ $keys->id }}>{{ $keys->bank_name }}</option>
                        @endif
                    @endforeach
                </select>

                @if ($errors->has('bank_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('bank_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        <div
            class="form-group has-feedback {{ $errors->has('account_no') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Account No</label>
                <input class="form-control" type="text" placeholder="A/C Number"
                    onkeyup="this.value=this.value.replace(/[^\d]/,'')" id="account_no" name="account_no"
                    value="{{ $master_data->account_no }}">
                @if ($errors->has('account_no'))
                    <span class="help-block">
                        <strong>{{ $errors->first('account_no') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div
            class="form-group has-feedback {{ $errors->has('by_bank_percent') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>By Bank Payment (%)</label>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <input class="form-control" type="text" id="by_bank_percent" name="by_bank_percent"
                    value="{{ $master_data->by_bank_percent }}">
            </div>
        </div>
        <div
            class="form-group has-feedback {{ $errors->has('total_present') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Payable Days</label>
                <input class="form-control" type="text" id="total_present" name="total_present" value=""
                    required>

                <label id="att_deduct_amt" style="color: red; font-weight: bold; "></label>
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('note') ? ' has-error' : '' }} col-md-6" id="noteCol">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Note</label>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <input class="form-control" type="text"  placeholder="Note...."  name="note"  value="" >
            </div>
        </div>

        <input type="text" name="id" value="{{ $master_data->id }}" hidden>
    </div>

    <!-- End Master Data -->
    <div class="row">
        <!-- Start Addition Datatable -->
        <div class="form-group col-lg-6 col-md-6 col-xs-12">
            <label class="form-control" style="color: black; background-color: gray">Addition List</label>
            <table id="list_table" class="cell-border table table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="width: 30%">Salary Head</th>
                        <th style="width: 20%">Amount</th>
                        <th style="width: 20%">Type</th>
                        <th style="width: 30%">Actual Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($edit_data as $value)
                        <tr>
                            <td>
                                {{ $value->salary_head }}
                                <!-- <input type="hidden" id="{{ $value->fixed_salary_head }}" name="fixed_salary_head" value="{{ $value->fixed_salary_head }}"> -->
                            </td>

                            <td>
                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    type="text" class="form-control" placeholder="amount" id="amountaddition"
                                    name="amountaddition[]" value="{{ $value->amount }}">
                            </td>

                            <td>
                                <select style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    class="form-control" name="typeaddition[]">
                                    @if ($value->amount_type == 1)
                                        <option value="2">Tk</option>
                                        <option value="1" selected>%</option>
                                    @else
                                        <option value="2" selected>Tk</option>
                                        <option value="1">%</option>
                                    @endif
                                </select>

                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    type="hidden" name="addition_id[]" value="{{ $value->salary_head_id }}">
                            </td>

                            <td>
                                <input
                                    style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;"
                                    type="text" placeholder="actual amount" class="form-control txt"
                                    id="{{ $value->fixed_salary_head }}" name="addition_actual_amount[]"
                                    value="{{ $value->actual_amount }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right; color: green;">Addition Total :</th>
                        <th id="addition_total" style="text-align: center; color: green;font-weight: 900;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- End Addition Datatable -->


        <!-- Start Deduction Datatable -->
        <div class="form-group col-lg-6 col-md-6 col-xs-12">
            <label class="form-control" style="color: black; background-color: gray">Deduction List</label>
            <table id="list_table2" class="cell-border table table-bordered table-hover" cellspacing="0"
                width="100%">
                <thead>
                    <tr>
                        <th style="width: 30%">Salary Head</th>
                        <th style="width: 20%">Amount</th>
                        <th style="width: 20%">Type</th>
                        <th style="width: 30%">Actual Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($edit_data_deduction as $value)
                        <tr>
                            <td>
                                {{ $value->salary_head }}
                                <input type="hidden" id="{{ $value->fixed_salary_head }}" name="fixed_salary_head"
                                    value="{{ $value->fixed_salary_head }}">
                            </td>

                            <td>
                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    type="text" placeholder="amount" class="form-control"
                                    id="{{ $value->fixed_salary_head . '_amount' }}" name="amountdeduction[]"
                                    value="{{ $value->amount }}">
                            </td>

                            <td>
                                <select style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    id="{{ $value->fixed_salary_head . '_type' }}" class="form-control"
                                    name="typededuction[]">
                                    @if ($value->amount_type == 1)
                                        <option value="2">Tk</option>
                                        <option value="1" selected>%</option>
                                    @else
                                        <option value="2" selected>Tk</option>
                                        <option value="1">%</option>
                                    @endif
                                </select>

                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"
                                    type="hidden" name="deduction_id[]" value="{{ $value->salary_head_id }}">
                            </td>

                            <td>
                                <input
                                    style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;"
                                    class="form-control de_txt" type="text" placeholder="actual amount"
                                    name="deduction_actual_amount[]"
                                    id="{{ $value->fixed_salary_head . '_actualamount' }}"
                                    value="{{ $value->actual_amount }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right;color: red;">Deduction Total :</th>
                        <th id="deduction_total" style="text-align: center; color: red;font-weight: 900;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label style="font-size: 20px">Net Salary</label>
                <input type="text" name="netsalary" id="netsalary" style="color:blue;font-size: 20px" disabled>
            </div>

            <div class="col-md-6">
                <input type="submit" class="btn btn-success block btn-flat submit-button pull-right"
                    style="width: 30%;" value="Save">
            </div>
        </div>
    </div>
</form>


<div class="modal fade" id="modal_create_file_type" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeModal(event)">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="groupAddLabel">Create New Bank Name</h4>
            </div>

            <form method="POST" action="{{ url('bankname_create') }}">
                {{ csrf_field() }}
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label>Bank Name</label>
                                <input class="form-control" type="text"
                                    placeholder="Islami Bank Bangladesh Limited" name="bank_name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <label>Short Name</label>
                                <input class="form-control" type="text" placeholder="IBBL" name="short_name"
                                    required>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="button_sunmit" class="btn btn-success btn-flat">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        for (i = new Date().getFullYear(); i > 2018; i--){
           $('.year').append($('<option />').val(i).html(i));
      }

      $('#month').select2({
            placeholder: 'Enter Month  Name',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/monthlist',
            delay: 250,
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
            cache: true
            }
      });

        $(document).on('change', '#payment_mode', function() {
            if ($(this).val() == 2) {
                $("#bank_name").attr('required', true);
                $("#account_no").attr('required', true);
            } else {
                $("#bank_name").attr('required', false);
                $("#account_no").attr('required', false);
            }
        });

        $("#payment_mode").trigger('change');

        function closeModal(e) {
            e.preventDefault();

            $('#modal_create_file_type').modal('hide')
        }

        var temp_basic_salary;

        $(document).ready(function() {

            deduction_total = 0;
            addition_total = 0;
            $('.bank').hide();


            if ($('#payment_mode').val() == 2) {
                $('.bank').show();
                $("#by_bank_percent").val(100);
            } else {
                $('.bank').hide();
                $("#by_bank_percent").val(0);
            }

            $('#payment_mode').on('change', function() {
                if ($('#payment_mode').val() == 2) {
                    $('.bank').show();
                    $("#by_bank_percent").val(100);
                } else {
                    $('.bank').hide();
                    $("#by_bank_percent").val(0);

                }
            });

            // Start Addition Datatable js
            list_table = $('#list_table').DataTable({
                "searching": false,
                "paging": false,
                "ordering": false,
                "autoWidth": false,
                "bInfo": false,
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                }
            });

            var sum = 0;
            $(".txt").each(function() {
                if (!isNaN(this.value) && this.value != 0) {
                    sum += parseFloat(this.value);
                }
            });

            $("#addition_total").html(sum)
            addition_total = sum;
            net_total();
            // console.log(addition_total);
            $(".txt").each(function() {
                $(this).keyup(function() {
                    var columnnumber = ($('#list_table tbody td').length / $(
                        '#list_table tbody tr').length);
                    var rowCount = $('#list_table tbody tr').length - 1;
                    calculateSum(columnnumber, rowCount);
                });
            });

            function calculateSum(column, rowCount) {
                var sum = 0;
                $(".txt").each(function() {
                    if (!isNaN(this.value) && this.value != 0) {
                        sum += parseFloat(this.value);
                    }
                });

                sumQ = [];
                for (var i = 1; i < (column); i++) {
                    sumQ[i] = 0;
                    $('td:nth-child(' + (i + 1) + ')').find(".txt").each(function() {
                        if (!isNaN(this.value) && this.value != 0) {
                            sumQ[i] += parseFloat(this.value);
                        }

                        $("#addition_total").html(sumQ[i]);
                        addition_total = sumQ[i];
                        net_total();
                        // console.log(addition_total);
                    });
                }
            }
            // End Addition Datatable js

            // Start deduction Datatable js
            list_table2 = $('#list_table2').DataTable({
                "searching": false,
                "paging": false,
                "ordering": false,
                "autoWidth": false,
                "bInfo": false,
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                }
            });

            var sum = 0;
            $(".de_txt").each(function() {
                if (!isNaN(this.value) && this.value != 0) {
                    sum += parseFloat(this.value);
                }
            });

            $("#deduction_total").html(sum)
            deduction_total = sum;
            net_total();

            $(".de_txt").each(function() {
                $(this).keyup(function() {
                    var columnnumber = ($('#list_table2 tbody td').length / $(
                        '#list_table2 tbody tr').length);
                    var rowCount = $('#list_table2 tbody tr').length - 1;
                    calculateSumDeduction(columnnumber, rowCount);
                });

            });

            function calculateSumDeduction(column, rowCount) {
                var sum = 0;

                $(".de_txt").each(function() {
                    if (!isNaN(this.value) && this.value != 0) {
                        sum += parseFloat(this.value);
                    }
                });

                sumQ = [];
                for (var i = 1; i < (column); i++) {
                    sumQ[i] = 0;
                    $('td:nth-child(' + (i + 1) + ')').find(".de_txt").each(function() {

                        if (!isNaN(this.value) && this.value != 0) {
                            sumQ[i] += parseFloat(this.value);
                        }
                        // console.log(sumQ[i]);
                        $("#deduction_total").html(sumQ[i]);
                        // $("#netsalary").val(sumQ[i]);
                        deduction_total = sumQ[i];
                        net_total();
                    });
                }
            }

            function net_total() {
                $("#netsalary").val(addition_total - deduction_total);
            }


            $('#list_table tbody').on('keyup', 'tr', function() {
                var rate = $(this).find('td:eq(1)').find('input').val();
                var amount = $("#salary_amount").val();

                if ($(this).find('td:eq(2)').find(":selected").val() == 1) {
                    $(this).find('td:eq(3)').find('input').val(amount * rate / 100);
                } else {
                    $(this).find('td:eq(3)').find('input').val(rate);
                }

                var columnnumber = ($('#list_table tbody td').length / $('#list_table tbody tr')
                    .length);
                var rowCount = $('#list_table tbody tr').length - 1;
                calculateSum(columnnumber, rowCount);
            });

            $('#list_table tbody').on('click', 'tr', function() {

                var rate = $(this).find('td:eq(1)').find('input').val();
                var amount = $("#salary_amount").val();
                if ($(this).find('td:eq(2)').find(":selected").val() == 1) {
                    $(this).find('td:eq(3)').find('input').val(amount * rate / 100);
                } else {
                    $(this).find('td:eq(3)').find('input').val(rate);
                }

                var columnnumber = ($('#list_table tbody td').length / $('#list_table tbody tr')
                    .length);
                var rowCount = $('#list_table tbody tr').length - 1;
                calculateSum(columnnumber, rowCount);
            });


            $('#salary_amount').on('keyup', function() {

                var rowCount = $('#list_table tbody tr').length;
                for (i = 0; i < rowCount; i++) {
                    var rate = list_table.cell(i, 1).nodes().to$().find('input').val();
                    var amount = $("#salary_amount").val();
                    if (list_table.cell(i, 2).nodes().to$().find(':selected').val() == 1) {
                        list_table.cell(i, 3).nodes().to$().find('input').val(amount * rate /
                            100);
                    } else {
                        list_table.cell(i, 3).nodes().to$().find('input').val(rate);
                    }
                }

                // temp_basic_salary = ($("#salary_amount").val()*list_table.cell(0,1).nodes().to$().find('input').val()/100);
                temp_basic_salary = ($("#basic_salary").val());

                var provident_fund_amount = $('#provident_fund_amount').val();
                var provident_fund_type = $('#provident_fund_type').val();
                var provident_fund_actualamount = $('#provident_fund_actualamount').val();

                // console.log(provident_fund_type, provident_fund_amount);
                if (provident_fund_type == 1) {
                    provident_fund_actualamount = temp_basic_salary * provident_fund_amount /
                        100;
                } else {
                    provident_fund_actualamount = provident_fund_amount;
                }

                $('#provident_fund_actualamount').val(provident_fund_actualamount);

                var rowCount2 = $('#list_table2 tbody tr').length;
                console.log(rowCount2);

                var columnnumber = ($('#list_table tbody td').length / $('#list_table tbody tr')
                    .length);
                var rowCount = $('#list_table tbody tr').length - 1;
                calculateSum(columnnumber, rowCount);
            });
        });
    })
</script>
