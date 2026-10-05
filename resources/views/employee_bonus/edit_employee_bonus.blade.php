<!-- create_employee -->

@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">


<style>
img{
  width: 210px;
  height: 210px;
}
input[type=file]{
	padding:10px;
	/*background:#2d2d2d;*/
}
</style>
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee Bonus</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('bonusprocess.update', $employee[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}

	<div class="box-body">

		<div class="row">


			<div class="col-lg-6 col-md-6 col-xs-12 personal-info">

		        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Employee Name</label>
		            <div class="col-lg-9">
		
						<select style="width: 100%;" class="form-control select2" id="id" name="id" readonly>
								<option value="{{$employee[0]->id}}">{{$employee[0]->employee_name}}</option>
						</select>

			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>




		        <div class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Department</label>
		            <div class="col-lg-9">
		                <input type="text" class="form-control"   value="{{$employee[0]->depertment_name}} "  readonly >
			              
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('designation_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Designation</label>
		            <div class="col-lg-9">
		                <input type="text" class="form-control"    value="{{$employee[0]->designation_name}} "  readonly >
			              
		            </div>
		        </div>

		        <div class="form-group has-feedback {{ $errors->has('salary_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="color:red">Gross Salary*</span></label>
		            <div class="col-lg-9">
		                <input type="text" style="color: green " class="form-control" name="salary_amount"   value="{{$employee[0]->salary_amount}} "   required >
			              
		            </div>
		        </div>

		        <div class="form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="color:red">Bonus Percent/Tk.*</span></label>
		            <div class="col-lg-6">
		                <input type="text" step="any" style="color: red " class="form-control" name="amount"   value="{{$employee[0]->amount}} "    required>
			              
		            </div>
		            <div class="col-lg-3">

		                <select class="form-control" style="color: red " name="amount_type"  required>
		                @if ($employee[0]->amount_type=='%'){
		                	<option value="%" selected>%</option>
		                	<option value="Tk.">Tk.</option>
		                }@else{
		                	<option value="Tk." selected>Tk.</option>
		                	<option value="%">%</option>
		                }@endif
		                </select>
			              
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('bonus_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="color:red">Bonus Amount*</span></label>
		            <div class="col-lg-9">
		                <input type="text" style="color: red " class="form-control" name="bonus_amount"   value="{{$employee[0]->bonus_amount}} "  required >
			              
		            </div>
		        </div>

		</div>

		<div class="col-lg-6 col-md-6 col-xs-12 ">



		        <div class="form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="">Payment Mode</span></label>
		            <div class="col-lg-9">

		                <select class="form-control" style="color: red " name="payment_mode"  required>
		                @if ($employee[0]->payment_mode==1){
		                	<option value="1" selected>Cash</option>
		                	<option value="2">Bank</option>
		                }@else{
		                 	<option value="1" >Cash</option>
		                	<option value="2" selected>Bank</option>
		                }@endif
		                </select>
			              
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('account_no') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="">Bank Name</span></label>
		            <div class="col-lg-9">
				                             
		                <select class="form-control" name="hrm_bank_id" id="bank_name" >
		                	<option value="0">N/A</option>
		                    
		                    @foreach ($bank_name as $keys)
		                      @if ($employee[0]->hrm_bank_id == $keys->id)
		                        <option value={{$keys->id}} selected>{{$keys->bank_name}}</option>
		                      @else
		                        <option value={{$keys->id}}>{{$keys->bank_name}}</option>
		                      @endif
		                    @endforeach

		                </select>
			              
		            </div>
		        </div>





		        <div class="form-group has-feedback {{ $errors->has('account_no') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label"><span style="">A/C Number</span></label>
		            <div class="col-lg-9">
		                <input type="text" style="color: red " class="form-control" name="account_no" placeholder="Account Number"   value="{{$employee[0]->account_no}} "    >
			              
		            </div>
		        </div>




		</div>




		<div class="col-md-12">
		<div class="col-md-6">

			<input type="submit" class="btn btn-success block btn-flat pull-right" value="Update">
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


<script src="{{asset('js/fileinput.js')}}"></script>
<script>
 function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
$(document).ready(function($) {

 $('#employee_name').select2({
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
});
</script>
@endsection
