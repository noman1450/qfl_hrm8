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
		<h3 class="box-title">Create Loan Application</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'loanapplication.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
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


		        <div class="form-group has-feedback {{ $errors->has('loan_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Loan Type</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control select2" id="loan_type" name="loan_type" required>
						</select>

			            @if ($errors->has('loan_type'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('loan_type') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('opening_loan_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Loan Amount / Opening </label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control" type="text" name="opening_loan_amount" id="opening_loan_amount" placeholder="Opening Loan Amount" onkeypress="return isNumberKey(event)">

			            @if ($errors->has('opening_loan_amount'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('opening_loan_amount') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('paid_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Already Paid Amount</label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control" type="text" name="paid_amount" id="paid_amount" placeholder="Loan Paid Amount" value="0" onkeypress="return isNumberKey(event)">

			            @if ($errors->has('paid_amount'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('paid_amount') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>




		        <div class="form-group has-feedback {{ $errors->has('remaining_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Remaining Amount</label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control" type="text" name="remaining_amount" id="remaining_amount" readonly>

			            @if ($errors->has('remaining_amount'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('remaining_amount') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('instalment_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12 ">  
		            <label class="col-lg-3 control-label">Instalment Type</label>
		            <div class="col-lg-9">
			  	          
		            	<select class="form-control" id="instalment_type" name="instalment_type"  onchange="myFunction()"> 
		            		<option value="1">Instalment Size</option>
		            		<option value="2">No Of Instalment</option>
		            	</select>


			            @if ($errors->has('instalment_type'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('instalment_type') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('installment_size') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label" id="label1">Installment Size</label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control"   type="text" name="installment_size" id="installment_size">

			            @if ($errors->has('installment_size'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('installment_size') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>



		        <div class="form-group has-feedback {{ $errors->has('no_of_instalment') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label" id="label2" >No of Installment</label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control" type="text" name="no_of_instalment" id="no_of_instalment" readonly>

			            @if ($errors->has('no_of_instalment'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('no_of_instalment') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


               <div class="form-group has-feedback {{ $errors->has('start_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"> <span style="color: red;">Start From (Month)</span></label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="start_from" name="start_from"  required readonly>
			            @if ($errors->has('start_from'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('start_from') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>

		        <div class="form-group has-feedback {{ $errors->has('note') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Note</label>
		            <div class="col-lg-9">
		  	          
						<input  class="form-control" type="text" name="note" id="note" placeholder="Note" >

			            @if ($errors->has('note'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('note') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>


			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8" style="padding-left: 553px;">
					<input type="submit" class="btn btn-success btn-flat" value="Submit">
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



    $('#start_from').datepicker({
      autoclose: true,
      minViewMode: 1,
      format: 'dd-mm-yyyy'
    });


  	$( "#installment_size" ).keyup(function() {
  		calculate_installment_amount();
  	});


	calculate_installment_amount = function(){

          if ($.isNumeric($("#installment_size").val())) {
			installment_size = $("#installment_size").val();
          }else{
          	installment_size = 0;
          }

          if ($.isNumeric($("#remaining_amount").val())) {
			remaining_amount = $("#remaining_amount").val();
          }else{
          	remaining_amount = 0;
          }
		$('#no_of_instalment').val((parseFloat(remaining_amount)/parseFloat(installment_size)).toFixed());
	}


	$( "#opening_loan_amount" ).keyup(function() {
  		calculate_remaining_amount();
  	});

	$( "#paid_amount" ).keyup(function() {
  		calculate_remaining_amount();
  	});



	calculate_remaining_amount = function(){

          if ($.isNumeric($("#opening_loan_amount").val())) {
			opening_loan_amount = $("#opening_loan_amount").val();
          }else{
          	opening_loan_amount = 0;
          }

          if ($.isNumeric($("#paid_amount").val())) {
			paid_amount = $("#paid_amount").val();
          }else{
          	paid_amount = 0;
          }
		$('#remaining_amount').val((parseFloat(opening_loan_amount)-parseFloat(paid_amount)).toFixed());
	}






   $('#employee_name').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/joinemployeelist_accounts",
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




   $('#loan_type').select2({
    	placeholder: 'Enter a Loan Account',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/loantypes_list",
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


	




});
</script>

<script language=Javascript>
      
      function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         // if (charCode > 31 && (charCode < 48 || charCode > 57))
         if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
            return false;
 
         return true;
      }


      function myFunction() {

		    if($("#instalment_type").val() == 1){
						document.getElementById("label1").innerHTML = "Instalment Size";
						document.getElementById("label2").innerHTML = "No of Instalment";
			    
			}else{
						document.getElementById("label1").innerHTML = "No of Instalment";
						document.getElementById("label2").innerHTML = "Instalment Size";
			}

	  }
      
</script>


@endsection
