<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<style>
.kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
    text-align: center;
}
.kv-avatar {
    display: inline-block;
}
.kv-avatar .file-input {
    display: table-cell;
    width: 213px;
}
.kv-reqd {
    color: red;
    font-family: monospace;
    font-weight: normal;
}
.btn-secondary {
	margin-top: 5px;
}
.btn-file{
	margin-top: 5px;
}
</style>
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Leave Application</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    {!! Form::open(array('route'=>'employeeleave.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
        {{ csrf_field() }}

        <div class="box-body">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-xs-12 personal-info">
                    <div class="form-group has-feedback {{ $errors->has('applied') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Apply Date</label>
                        <div class="col-lg-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="applied" name="applied" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('applied') }}" required readonly>
                            @if ($errors->has('applied'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('applied') }}</strong>
                                </span>
                            @endif
                        </div>
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Employee Name</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
                            </select>

                            @if ($errors->has('employee_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('employee_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('employee_leave_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Leave Type</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control select2" id="employee_leave_type" name="employee_leave_type" required>
                            </select>

                            @if ($errors->has('employee_leave_type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('employee_leave_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Date From</label>
                        <div class="col-lg-9">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_from') }}" onchange="cal()" required readonly>

                                @if ($errors->has('date_from'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('date_from') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Date To</label>
                        <div class="col-lg-9">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_to') }}" onchange="cal()" required readonly  >
                                @if ($errors->has('date_to'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('date_to') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('days_duration') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Days Duration</label>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" id="days_duration" name="days_duration"  style="font-size: 18px;" disabled>
                        </div>
                        <div class="col-lg-2" style="margin-left: -27px;margin-top: 6px;">
                            <label class="control-label">Day(s)</label>
                        </div>
                        <div class="col-lg-2">
                            <input type="number" step="any" class="form-control" id="leave_balance" name="leave_balance"  style="font-size: 18px;" disabled>
                        </div>
                        <div class="col-lg-2" style="margin-left: -27px;margin-top: 6px;">
                            <label class="control-label">Balance</label>
                        </div>
                    </div>
                    

                    <div class="form-group has-feedback {{ $errors->has('duration') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12  DurationForOne">
                        <label class="col-lg-3 control-label ">Duration</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="duration" id="duration" required>
                                <option value="2">Full Day</option>
                                <option value="1">Half Day</option>
                                <option value="3">Quarter Day</option>
                            </select>

                            @if ($errors->has('duration'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('duration') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Comment</label>
                        <div class="col-lg-9">
                            <textarea rows="4" cols="62" class="form-control" name="comment" placeholder="Describe the reason..."></textarea>
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('payment_mode') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Payment Mode</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control" id="payment_mode" name="payment_mode" required>
                                <option value="1">Pay</option>
                                <option value="2">Without Pay</option>
                            </select>

                            @if ($errors->has('payment_mode'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('payment_mode') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('applytousername') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Apply To</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control select2" id="applytousername" name="applytousername" required>
                            </select>

                            @if ($errors->has('applytousername'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('applytousername') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8">
                            <input type="submit" class="btn btn-success block btn-flat" id="save" value="Submit">
                            <span></span>
                            <!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#applied').datepicker({
      autoclose: true
    });
    $('#date_from').datepicker({
      autoclose: true
    });
    $('#date_to').datepicker({
      autoclose: true
    });


    $('.DurationForOne').hide();


	if($("#date_from").val() == $("#date_to").val()){
	    $('.DurationForOne').show();
	}else{
	    $('.DurationForOne').hide();
	}

	$('#date_from').on('change', function(){
		if($("#date_from").val() == $("#date_to").val()){
			$('.DurationForOne').show();
               balanceCheck()
		}else{
			$('.DurationForOne').hide();
               balanceCheck()
		}
	});

	$('#date_to').on('change', function(){
		if($("#date_from").val() == $("#date_to").val()){
			$('.DurationForOne').show();
               balanceCheck()
			}else{
			$('.DurationForOne').hide();
               balanceCheck()
		}
	});

    $(document).on('change','#duration',function(){
        $
    })



  $employee =  $('#employee_name').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/join_employee_list",
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



  $leave_type = $('#employee_leave_type').select2({
    	placeholder: 'Enter a Leave type',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/employee_leave_type",
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

let balance = 0;

$(document).on('change', '#employee_leave_type, #employee_name', function(e) {
    let employee_id   = $('#employee_name').val(); // অথবা option:selected.val()
    let leave_type = $('#employee_leave_type').val();

    


    if(employee_id && leave_type) { // optional: ensure both selected
        $.ajax({
            url: '{{ url("/get_leave_balance") }}', // Laravel route
            type: 'GET', // বা POST
            dataType: 'json',
            data: {
                employee_id: employee_id,
                leave_type: leave_type
            },
            success: function(response) {
                
                if(response.balance) {
                    balance = response.balance;
                    $('#leave_balance').val(response.balance); 
                   
                } else {
                    $('#leave_balance').val('0');
                    balance = 0;
                }
                balanceCheck()
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
});

$('#duration').on('change',function(){
    $duration = $('#duration').val();
    if($duration == 1){
        $('#days_duration').val(.5)
    }else if($duration == 3){
         $('#days_duration').val(.25)
    }else{
         $('#days_duration').val(1);
    }

    balanceCheck()

})

function balanceCheck(){
    let days_duration = Number($('#days_duration').val());
    
    console.log(days_duration,balance)
    if(days_duration > balance){
        $('#save').prop('disabled', true);
    }else{
        $('#save').prop('disabled', false);
    }
}



  $('#applytousername').select2({
    	placeholder: 'Enter Approved By Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/userlist",
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

var startDate= $("#date_from").datepicker('getDate');
var endDate= $("#date_to").datepicker('getDate');

var diffInDays = Math.round((endDate.getTime()- startDate.getTime())/(1000*60*60*24));
$('#days_duration').val(diffInDays+1);


});
</script>


 <script type="text/javascript">


		function cal(){
				var startDate= $("#date_from").datepicker('getDate');
				var endDate= $("#date_to").datepicker('getDate');

				var diffInDays = Math.round((endDate.getTime()- startDate.getTime())/(1000*60*60*24));
				$('#days_duration').val(diffInDays+1);
		}

</script>

@endsection
