
<form action="{{ route('cwpayregister.update', $master_data[0]->id) }}" class="dynamicFormSubmit" data-table-name="#cw_payregister_list_table" method="post">
    @csrf
    @method('put')

    <!-- Start Master Data -->
    <div class="row">
        <div class="col-md-6">
            {{-- <h2>{{$master_data[0]->employee_name}}</h2> --}}

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Name</label>
                <label class="col-lg-9 control-label text-primary">{{$master_data[0]->employee_name}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Department</label>
                <label class="col-lg-9 control-label">{{$master_data[0]->depertment_name}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Designation</label>
                <label class="col-lg-9 control-label">{{$master_data[0]->designation_name}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Location</label>
                <label class="col-lg-9 control-label">{{$master_data[0]->location_name}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Payment Mode</label>
                <select name="payment_mode"  id="payment_mode" >
                    @if ($master_data[0]->payment_mode == 1)
                        <option value="1" selected>Cash</option>
                        <option value="2">Bank</option>
                    @else
                        <option value="1">Cash</option>
                        <option value="2" selected>Bank</option>
                    @endif

                </select>
            </div>


            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12 bank">
                <label class="col-lg-3 control-label">Bank Name</label>
                <select class="col-lg-9" name="hrm_bank_id" id="bank_name" >
                    @foreach ($bank_name as $keys)
                        @if ($master_data[0]->hrm_bank_id == $keys->id)
                            <option value={{$keys->id}} selected>{{$keys->bank_name}}</option>
                        @else
                            <option value={{$keys->id}}>{{$keys->bank_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12 bank">
                <label class="col-lg-3 control-label">Account No</label>
                <input  class="" type="text" name="account_no" placeholder="Enter Account No"  value="{{$master_data[0]->account_no}}" >
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Year</label>
                <label class="col-lg-9 control-label">{{$master_data[0]->year_id}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Month Name</label>
                <label class="col-lg-9 control-label">{{$master_data[0]->month_name}}</label>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Day of Month</label>
                <input type="text" name="dayofmonth" id="dayofmonth" value="{{$master_data[0]->day_of_month}}"  readonly>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Present Day(s)</label>
                <input  class="col-lg-9 " type="text" id="total_present" name="total_present"  value="{{$master_data[0]->total_present}}" oninput="calculate();" required >
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Per Day Rate</label>
                <input  class="col-lg-9 control-label" type="text" id="salary_amount" name="salary_amount"  value="{{$master_data[0]->salary_amount}}" oninput="calculate();" required >
            </div>


            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Total Salary</label>
                <input  class="col-lg-9 control-label text-danger" type="text" id="total_salary" name="total_salary"  value="{{$master_data[0]->total_salary}}"  required  readonly locked>
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Adv. Adjust</label>
                <input  class="col-lg-9 control-label" type="text" id="adv_adjust" name="adv_adjust"  value="{{$master_data[0]->adv_adjust}}" oninput="calculate();" required >
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Due Adjust</label>
                <input  class="col-lg-9 control-label" type="text" id="due_adjust" name="due_adjust"  value="{{$master_data[0]->due_adjust}}"  oninput="calculate();"  required >
            </div>

            <div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Net Salary</label>
                <input  class="col-lg-9 control-label text-danger" type="text" id="net_salary" name="net_salary"  value="{{$master_data[0]->net_salary}}"  required  readonly locked>
            </div>




            {{-- <div class="form-group has-feedback {{ $errors->has('total_present') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Present Day(s)</label>
                <input  class="form-control" type="text" id="total_present" name="total_present"  value="{{$master_data[0]->total_present}}" oninput="calculate();" required >
            </div>
            </div> --}}

            {{-- <div class="form-group has-feedback {{ $errors->has('salary_amount') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Per Day Rate</label>
                <input  class="form-control" type="text" id="salary_amount" name="salary_amount"  value="{{$master_data[0]->salary_amount}}" oninput="calculate();" required>

                @if ($errors->has('salary_amount'))
                <span class="help-block">
                <strong>{{ $errors->first('salary_amount') }}</strong>
                </span>
                @endif
            </div>
            </div>
            <div class="form-group has-feedback {{ $errors->has('total_salary') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Total Salary</label>
                <input  class="form-control" type="text" id="total_salary" name="total_salary"  value="{{$master_data[0]->total_salary}}" required readonly>

                @if ($errors->has('total_salary'))
                <span class="help-block">
                <strong>{{ $errors->first('total_salary') }}</strong>
                </span>
                @endif
            </div>
            </div> --}}

        </div>
    </div>

    <input type="text" name="id" value="{{$master_data[0]->id}}" hidden>
    <input type="text" name="hrm_job_info_id" value="{{$master_data[0]->hrm_job_info_id}}" hidden>
    <input type="text" name="day_of_month" value="{{$master_data[0]->day_of_month}}" hidden>
    <!-- End Master Data -->

    <div class="form-group">
        <div class="row">
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <input type="submit" class="btn btn-success block btn-flat  pull-right" style="width: 30%;" value="Submit">
            </div>
        </div>
    </div>
</form>


<script>

function calculate() {

    var total_present = document.getElementById('total_present').value;
    var salary_amount = document.getElementById('salary_amount').value;
    var adv_adjust    = document.getElementById('adv_adjust').value;
    var due_adjust    = document.getElementById('due_adjust').value;
    var myResult      = total_present * salary_amount - adv_adjust ;


    document.getElementById('total_salary').value = total_present * salary_amount;
    document.getElementById('net_salary').value = due_adjust-adv_adjust + (total_present * salary_amount);


    balance         = $("#dayofmonth").val();
    total_quantity  = $("#total_present").val();


    if (parseFloat(balance) < parseFloat(total_quantity) ){
        alert("You can't be input more day of month");
        $("#total_present").val(balance);
        return;
    }
}


$('.bank').hide();


if($('#payment_mode').val() == 2) {
    $('.bank').show();
    $("#by_bank_percent").val(100);
} else {
    $('.bank').hide();
    $("#by_bank_percent").val(0);
}


$('#payment_mode').on('change', function() {
    if($('#payment_mode').val() == 2){
        $('.bank').show();
        $("#by_bank_percent").val(100);
    } else {
        $('.bank').hide();
        $("#by_bank_percent").val(0);
    }
});
</script>
