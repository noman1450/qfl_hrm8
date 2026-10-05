<form action="{{ route('payregister.update', $master_data->id) }}" class="dynamicFormSubmit" data-table-name="#payRegisterDatatable" method="post">
    @csrf

    @method('patch')

    <!-- Start Master Data -->
    <div class="row">
        <div class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <label>Department</label>
                <input  class="form-control" type="text" name="depertment_name"  value="{{$master_data->depertment_name}}" required readonly>
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('designation_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Designation</label>
                <input  class="form-control" type="text" name="designation_name"  value="{{$master_data->designation_name}}" required readonly>
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('total_present') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Payable Days</label>
                <input  class="form-control" type="text" id="total_present" name="total_present"  value="{{$master_data->total_present}}" required readonly>

                <label id="att_deduct_amt" style="color: red; font-weight: bold; "></label>
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('salary_amount') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Basic/Gross Salary</label>
                <input  class="form-control" type="number" id="salary_amount" name="salary_amount"  value="{{$master_data->salary_amount}}" required readonly>

                @if ($errors->has('salary_amount'))
                    <span class="help-block">
                        <strong>{{ $errors->first('salary_amount') }}</strong>
                    </span>
                @endif

            </div>
        </div>

        <div class="col-lg-12">
        </div>

        <div class="form-group has-feedback {{ $errors->has('accounts_code') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>A/C Code</label>
                <input  class="form-control" type="text" name="accounts_code"  value="{{$master_data->accounts_code}}" placeholder="Accounts Code">

                @if ($errors->has('accounts_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('accounts_code') }}</strong>
                    </span>
                @endif

            </div>
        </div>


        <div class="form-group has-feedback {{ $errors->has('payment_mode') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Payment Mode</label>
                <select class="form-control" name="payment_mode" id="payment_mode" >
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


        <div class="form-group has-feedback {{ $errors->has('bank_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Bank Name</label>
                <select class="form-control" name="hrm_bank_id" id="bank_name" >
                    @foreach ($bank_name as $keys)
                        @if ($master_data->hrm_bank_id == $keys->id)
                            <option value={{$keys->id}} selected>{{$keys->bank_name}}</option>
                        @else
                            <option value={{$keys->id}}>{{$keys->bank_name}}</option>
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

        <div class="form-group has-feedback {{ $errors->has('account_no') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Account No</label>
                <input  class="form-control" type="text" name="account_no"  id="account_no" value="{{$master_data->account_no}}" >

                @if ($errors->has('account_no'))
                    <span class="help-block">
                        <strong>{{ $errors->first('account_no') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input type="hidden" name="hrm_salary_generate_master_id" value="{{$master_data->hrm_salary_generate_master_id}}">


        <div class="form-group has-feedback {{ $errors->has('by_bank_percent') ? ' has-error' : '' }} col-md-3 bank">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>By Bank Payment (%)</label>
            </div>

            <div class="col-lg-12 col-md-12 col-xs-12">
                <input  class="form-control" type="text"  id="by_bank_percent" name="by_bank_percent"  value="{{$master_data->by_bank_percent}}" >
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('note') ? ' has-error' : '' }} col-md-6" id="noteCol">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Note</label>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <input class="form-control" type="text"  placeholder="Note...."  name="note"  value="{{$master_data->note}}" >
            </div>
        </div>
    </div>

    <input type="text" name="id" value="{{$master_data->id}}" hidden>
    <input type="text" name="hrm_job_info_id" value="{{$master_data->hrm_job_info_id}}" hidden>
    <input type="text" id="day_of_month" name="day_of_month" value="{{$master_data->day_of_month}}" hidden>

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
                            <td>{{$value->salary_head}}</td>

                            <td>
                                <input  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; background: white;" type="text"  class="form-control"   id="amountaddition"    name="amountaddition[]"  value="{{ $value->amount }}" readonly>
                            </td>

                            <td>
                                <select style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; background: white;"  class="form-control " id="typeaddition"             name="typeaddition[]" readonly>
                                    <option value="1" selected>%</option>
                                </select>
                                <input  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;" type="hidden"       id="addition_id"     name="addition_id[]"  value="{{ $value->salary_head_id }}">
                            </td>

                            <td>
                                <input  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;" type="text"   placeholder="actual amount"  class="form-control txt"  id="addition_actual_amount"    name="addition_actual_amount[]"  value="{{ $value->actual_amount }}" @if(!$value->editable) readonly @endif>
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
            <table id="list_table2" class="cell-border table table-bordered table-hover" cellspacing="0" width="100%">
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
                            <td>{{$value->salary_head}}</td>

                            <td>
                                <input  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; background: white;" type="text"   class="form-control"   id="amountdeduction"    name="amountdeduction[]"  value="{{ $value->amount }}"  readonly>
                            </td>

                            <td>
                                <select style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; background: white"  id="typededuction"    class="form-control" name="typededuction[]" readonly>
                                @if ($value->amount_type == 1){
                                    <option value="2">Tk</option>
                                    <option value="1" selected>%</option>
                                }@else
                                    <option value="2" selected>Tk</option>
                                    <option value="1">%</option>
                                @endif
                                </select>

                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;" type="hidden"       id="deduction_id"     name="deduction_id[]"  value="{{ $value->salary_head_id }}" readonly>
                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;" type="hidden"       id="hrm_loan_application_id"     name="hrm_loan_application_id[]"  value="{{ $value->hrm_loan_application_id }}" readonly>


                            </td>

                            <td>
                                <input style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;" class="form-control de_txt"  type="text"   placeholder="actual amount"    id="deduction_actual_amount"    name="deduction_actual_amount[]"  value="{{ $value->actual_amount }}" @if(!$value->editable) readonly @endif>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right;color: red;">Deduction Total :</th>
                        <th id="deduction_total"  style="text-align: center; color: red;font-weight: 900;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- End Deduction Datatable -->
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label style="font-size: 20px" >Net Salary</label>
                <input type="text" name="netsalary" id="netsalary"  style="color:blue;font-size: 20px" disabled>
            </div>

            <div class="col-md-6">
                <input type="submit" class="btn btn-success block btn-flat  pull-right"  id="btnSubmit" style="width: 30%;" value="Update">
            </div>
        </div>
    </div>
</form>



<script>
    $(document).ready(function() {

        $(document).on('change','#payment_mode',function(){
            if($(this).val() == 2){
                $("#bank_name").attr('required',true);
                $("#account_no").attr('required',true);
            }else{
                $("#bank_name").attr('required',false);
                $("#account_no").attr('required',false);
            }
        })

        $("#payment_mode").trigger('change');

        $('.bank').hide();

        //     if($('#payment_mode').val() == 2) {
        //         $('.bank').show();
        //         $("#by_bank_percent").val(100);
        //         $('#noteCol').addClass('col-md-9').removeClass('col-md-6');
        //     } else {
        //         $('.bank').hide();
        //         $("#by_bank_percent").val(0);
        //         $('#noteCol').addClass('col-md-6').removeClass('col-md-9');
        //     }

        // $('#payment_mode').on('change', function() {
        //     if($('#payment_mode').val() == 2) {
        //         $('.bank').show();
        //         $("#by_bank_percent").val(100);
        //         $('#noteCol').addClass('col-md-9').removeClass('col-md-6');
        //     } else {
        //         $('.bank').hide();
        //         $("#by_bank_percent").val(0);
        //         $('#noteCol').addClass('col-md-6').removeClass('col-md-9');
        //     }
        // });

        if ($('#payment_mode').val() == 2) {
            $('.bank').show();
            $('#noteCol').addClass('col-md-9').removeClass('col-md-6');
        } else {
            $('.bank').hide();
            $('#noteCol').addClass('col-md-6').removeClass('col-md-9');
        }


        $('#payment_mode').on('change', function() {
            if ($(this).val() == 2) {
                $('.bank').show();
                $("#by_bank_percent").val(100);
                $('#noteCol').addClass('col-md-9').removeClass('col-md-6');
            } else {
                $('.bank').hide();
                $("#by_bank_percent").val(0);
                $('#noteCol').addClass('col-md-6').removeClass('col-md-9');
            }
        });
             // Start Addition Datatable js

        list_table = $('#list_table').DataTable({
            "searching": false,
            "paging": false,
            "ordering": false,
            "autoWidth": false,
            "bInfo": false,
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                    // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
            }
        });

        var sum = 0;
        $(".txt").each(function() {
            if(!isNaN(this.value) && this.value !=0){
                sum +=parseFloat(this.value);
            }
        });




        $('#total_present').on('keyup', function() {

            var amount = $("#salary_amount").val();
            var day_of_month = $("#day_of_month").val();
            var total_present = $(this).val();
            var net_days = parseFloat(day_of_month-total_present);
            var onedayamt = parseFloat(amount/day_of_month);
            var actual_amount = parseFloat(onedayamt*net_days);


            if(parseFloat(total_present)>parseFloat(day_of_month)){
               alert('Wrong days input!!');
               $('#total_present').val(day_of_month);
               $('#att_deduct_amt').text(0);
               return;
            }

            $("#att_deduct_amt").text( 'Attendance Deduction: '+actual_amount.toFixed(2));

            // var rowCount = $('#list_table tbody tr').length;
            // for (i = 0; i < rowCount; i++) {
            //     var rate = list_table.cell(i, 1).nodes().to$().find('input').val();
            //     var amount = $("#salary_amount").val();
            //     if (list_table.cell(i, 2).nodes().to$().find(':selected').val() == 1) {
            //         list_table.cell(i, 3).nodes().to$().find('input').val(amount * rate / 100);
            //     } else {
            //         list_table.cell(i, 3).nodes().to$().find('input').val(rate);
            //     }
            // }
        });


        $("#addition_total").html(sum)
        addition_total = sum;
        net_total();
               // console.log(addition_total);

        $(".txt").each(function(){
            $(this).keyup(function(){
                var columnnumber = ($('#list_table tbody td').length/$('#list_table tbody tr').length);
                var rowCount = $('#list_table tbody tr').length - 1;
                calculateSum(columnnumber,rowCount);
            });
        });

        function calculateSum(column,rowCount) {
            var sum = 0;
            $(".txt").each(function(){
                if(!isNaN(this.value) && this.value !=0){
                    sum +=parseFloat(this.value);
                }
            });

            sumQ = [];
            for (var i = 1; i<(column); i++) {
                sumQ[i] = 0;
                $('td:nth-child('+(i+1)+')').find(".txt").each(function() {
                    if(!isNaN(this.value) && this.value !=0){
                        sumQ[i] +=parseFloat(this.value);
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
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
            }
        });

        var sum = 0;
        $(".de_txt").each(function() {
            if(!isNaN(this.value) && this.value !=0){
                sum +=parseFloat(this.value);
            }
        });

        $("#deduction_total").html(sum)
        deduction_total = sum;
        net_total();

        $(".de_txt").each(function() {
            $(this).keyup(function() {
                var columnnumber = ($('#list_table2 tbody td').length/$('#list_table2 tbody tr').length);
                var rowCount = $('#list_table2 tbody tr').length - 1;
                calculateSumDeduction (columnnumber,rowCount);
            });
        });

        function calculateSumDeduction(column,rowCount) {
            var sum = 0;

            $(".de_txt").each(function(){
                if(!isNaN(this.value) && this.value !=0){
                    sum +=parseFloat(this.value);
                }
            });

            sumQ = [];
            for (var i = 1; i<(column); i++) {
                sumQ[i] = 0;
                $('td:nth-child('+(i+1)+')').find(".de_txt").each(function() {
                    if(!isNaN(this.value) && this.value !=0) {
                        sumQ[i] +=parseFloat(this.value);
                    }
                    // console.log(sumQ[i]);
                    $("#deduction_total").html(sumQ[i]);
                    // $("#netsalary").val(sumQ[i]);
                    deduction_total = sumQ[i];
                    net_total();
                });
            }
        }

        // End deduction Datatable js
        function net_total() {
            $("#netsalary").val(addition_total - deduction_total);
        }
    });
</script>
