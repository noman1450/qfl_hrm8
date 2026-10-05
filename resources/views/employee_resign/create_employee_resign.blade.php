<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Resign Application</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>




    {!! Form::open(array('route'=>'employeeresign.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row flex-row flex-md-nowrap" style="position: relative;">

			<div class="col-lg-8 col-md-7 col-12 personal-info" style="margin-right: 0; padding-right: 0;">
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

                <div class="form-group has-feedback {{ $errors->has('resignation_list') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Resignation</label>
		            <div class="col-lg-9">

						<select style="width: 100%;" class="form-control select2" id="resignation_list" name="hrm_resignation_type_id" required>
						</select>

			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Department</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="department" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Designation</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="designation" readonly>
		            </div>
		        </div>

		        <div class="form-group  col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Current Shift</label>
		            <div class="col-lg-9">
		            	<input type="text" class="form-control" id="current_shift" readonly>
		            </div>
		        </div>

                <div class="form-group has-feedback {{ $errors->has('effictive_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Effective Date</label>
		            <div class="col-lg-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="effictive_date" name="effictive_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('effictive_date') }}" required readonly>
                            @if ($errors->has('effictive_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('effictive_date') }}</strong>
                                </span>
                            @endif
                        </div>
		            </div>
		        </div>

                <div class="form-group has-feedback {{ $errors->has('comment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Comment</label>
		            <div class="col-lg-9">
		   			  <textarea  class="form-control"  rows="4" width="100%" name="comment" placeholder="Comment."></textarea>
		            </div>
		        </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8">
                        <input type="submit" class="btn btn-success block btn-flat" value="Submit">
                        <span></span>
                        <!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
                    </div>
                </div>
		    </div>
            <div class="col-lg-4 col-md-7 col-12 personal-info" style="margin-right: 0; padding-right: 0; display: none;" id="loan_div">
                <table id="employee_loan_information" class="table table-bordered table-hover" style="margin-left: 5px; padding-left: 5px;">
                    <h4 style="margin-left: 5px; padding-left: 5px;">Employee Loan Information</h4>
                    <thead>
                      <tr style="font-size: 14px;">
                        <th style="width: 55%">Loan Type</th>
                        <th style="width: 15%">Loan Amount</th>
                        <th style="width: 15%">Paid</th>
                        <th style="width: 15%">Remaining</th>
                      </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tfoot>
                  </table>

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
      // startDate: new Date() ,
      autoclose: true
    });
    $('#effictive_date').datepicker({
      autoclose: true
    });

    $('#resignation_list').select2({
        placeholder: 'Resignation Type',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/resignation_list_data',
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


   $(document).on('change',"#employee_name",function(){
        dataLoad()
   })


   function dataLoad() {
            table = $('#employee_loan_information').DataTable( {
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  false,
                "ordering":   true,
                "bInfo":      false,
                "paging":     false,

                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    total_amount = api.column(1).data().reduce( function (a, b) {return intVal(a) + intVal(b);},0);
                    payment = api.column(2).data().reduce( function (a, b) {return intVal(a) + intVal(b);},0);
                    remaining_amount = api.column(3).data().reduce( function (a, b) {return intVal(a) + intVal(b);},0);

                    $( api.column( 0 ).footer() ).html('Total:');
                    $( api.column( 1 ).footer() ).html(total_amount);
                    $( api.column( 2 ).footer() ).html(payment);
                    $( api.column( 3 ).footer() ).html(remaining_amount);
                },
                ajax: {
                    url: "{{ url('/employee-wise-loan') }}",
                    type: "GET",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    data:{
                        employee_id : $("#employee_name").val()
                    },
                    dataSrc: function(json) {
                        if (json.data && json.data.length > 0) {
                            $('#loan_div').toggle(true);
                        } else {
                            $('#loan_div').toggle(false);
                        }
                        return json.data;
                    }
                },
                columns: [
                    { "data": "loan_type" },
                    { "data": "loan_amount" },
                    { "data": "payment" },
                    { "data": "remaining_amount" },
                ],
                "order": [[2, 'asc']]
            });
        }




 $employee = $('#employee_name').select2({
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

    $employee.on("select2:select", function (e) {
    	$("#department").val($(this).select2('data')['0']['depertment_name']);
    	$("#designation").val($(this).select2('data')['0']['designation_name']);
    	$("#current_shift").val($(this).select2('data')['0']['shift_name']);
    });

    $employee.on("select2:unselect", function (e) {
    	$("#department").val('');
    	$("#designation").val('');
    	$("#current_shift").val('');
    });





});
</script>
@endsection
