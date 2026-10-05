@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<style type="text/css">
	.select2-selection__choice {
		color: white;
	}
</style>
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Employee Bonus Process <span style="color: red;">(CW Employee Only) </span> </h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

    <form  method="POST" enctype="multipart/form-data" action="{{url('bonusprocessforcw')}}" class="form-horizontal" style="" id="frm_process">


	{{ csrf_field() }}
	<div class="box-body">



		<div class="row">

			<div class="col-lg-6 col-md-6 col-xs-6 personal-info">
				<div class="form-group has-feedback {{ $errors->has('declaration_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Declaration Date<span style="color: red">*</span> </label>
					<div class="col-lg-9">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="declaration_date" name="declaration_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('declaration_date') }}" required readonly>
							
						</div>
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('location_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Location <span style="color: red">*</span></label>
					<div class="col-lg-9">
						
						<select style="width: 100%;" class="form-control select2 location"  name="location_name" required>
						</select>
						@if ($errors->has('location_name'))
						<span class="help-block">
							<strong>{{ $errors->first('location_name') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('bonus_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Bonus Name <span style="color: red">*</span></label>
					<div class="col-lg-9">
						
						<select style="width: 100%;" class="form-control select2 bonus"  name="bonus_name"  class="bonus"required>
						</select>
						@if ($errors->has('bonus_name'))
						<span class="help-block">
							<strong>{{ $errors->first('bonus_name') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('basedMonth') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Based On Month <span style="color: red">*</span></label>
					<div class="col-lg-9">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="basedMonth" name="basedMonth" data-date-format="dd-mm-yyyy"   required readonly>
							
						</div>
					</div>
				</div>


				<div class="form-group has-feedback {{ $errors->has('total_amount') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label" style="color: red;">CW Gross Salary <span style="color: red">*</span></label>
					<div class="col-lg-3">
						<input type="text"  oninput="sum();" class="form-control" name="rate" id="rate" placeholder="Rate" value="{{ old('rate') }}" required >
					</div>
					<div class="col-lg-3">
						<input type="text"  oninput="sum();" class="form-control" name="days" id="days"  placeholder="Days" value="{{ old('days') }}" required >
					</div>
					<div class="col-lg-3">
						<input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="Total Amount" value="{{ old('total_amount') }}" required readonly="" >
					</div>

				</div>



				<div class="form-group has-feedback {{ $errors->has('zero_to_six_months') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label" style="color: blue;">CW (0-6 Months) <span style="color: blue">*</span></label>
					<div class="col-lg-3">
						<input type="number" step="any" class="form-control" oninput="zero_to_six_months();" name="zero_to_six_months" id="zero_to_six_months" placeholder="Percentage" value="{{ old('zero_to_six_months') }}" required autofocus >
						@if ($errors->has('zero_to_six_months'))
						<span class="help-block">
							<strong>{{ $errors->first('zero_to_six_months') }}</strong>
						</span>
						@endif
					</div>
					<div class="col-lg-6">
						<input type="text" class="form-control" name="zero_to_six_months_amount" id="zero_to_six_months_amount" placeholder="Bonus Amount" value="{{ old('zero_to_six_months_amount') }}" required readonly="" >
					</div>
				</div>


				<div class="form-group has-feedback {{ $errors->has('six_to_twelve_months') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label" style="color: blue;">CW (6-12 Months) <span style="color: red">*</span></label>
					<div class="col-lg-3">
						<input type="number" step="any"  oninput="six_to_twelve_months();"  class="form-control" name="six_to_twelve_months" id="six_to_twelve_months" placeholder="Percentage" value="{{ old('six_to_twelve_months') }}" required autofocus >
						@if ($errors->has('six_to_twelve_months'))
						<span class="help-block">
							<strong>{{ $errors->first('six_to_twelve_months') }}</strong>
						</span>
						@endif
					</div>
					<div class="col-lg-6">
						<input type="text" class="form-control" name="six_to_twelve_months_amount" id="six_to_twelve_months_amount" placeholder="Bonus Amount" value="{{ old('six_to_twelve_months_amount') }}" required readonly="" >
					</div>
				</div>


				<div class="form-group has-feedback {{ $errors->has('more_than_one_year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label" style="color: blue;">CW (1 Year(+)) <span style="color: red">*</span></label>
					<div class="col-lg-3">
						<input type="number" step="any"  oninput="more_than_one_year();"  class="form-control" name="more_than_one_year" id="more_than_one_year" placeholder="Percentage" value="{{ old('more_than_one_year') }}" required autofocus >
						@if ($errors->has('more_than_one_year'))
						<span class="help-block">
							<strong>{{ $errors->first('more_than_one_year') }}</strong>
						</span>
						@endif
					</div>
					<div class="col-lg-6">
						<input type="text" class="form-control" name="more_than_one_year_amount" id="more_than_one_year_amount" placeholder="Bonus Amount" value="{{ old('more_than_one_year_amount') }}" required readonly="" >
					</div>
				</div>




				<div class="form-group has-feedback {{ $errors->has('employeestatus') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Eligible Employees <span style="color: red">*</span> </label>
					<div class="col-lg-9">
						
						<select  class="form-control" name="employeestatus[]" multiple="multiple"  id="employeestatus" style="width: 100%;"  required>
						</select>
					</div>
				</div>

                <div class="form-group has-feedback {{ $errors->has('payment_term') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Payment Term <span style="color: red">*</span> </label>
					<div class="col-lg-9">
						
						<select  class="form-control" name="payment_term"   style="width: 100%;"  required>
							<option value="Payment Method As Per Salary">Payment Method As Per Salary</option>
							<option value="Payment By Cash">Payment By Cash</option>
						</select>
					</div>
				</div>


				<div class="form-group has-feedback {{ $errors->has('basic_salary_of_gross') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label" style="color: blue;">Basic Salary(% Gross)<span style="color: red">*</span></label>
					<div class="col-lg-6">
						<input type="number" step="any" class="form-control" name="basic_salary_of_gross" placeholder="Percentage" value="{{ old('basic_salary_of_gross') }}" required autofocus >
						@if ($errors->has('amount'))
						<span class="help-block">
							<strong>{{ $errors->first('amount') }}</strong>
						</span>
						@endif
					</div>
					<div class="col-lg-3">
						<select class="form-control" name="" >
							<option value="%">%</option>
						</select>
					</div>
				</div>



				<div class="form-group col-lg-12 col-md-12 col-xs-12">
					<div id="alert-danger"></div>
				</div>
				<div class="form-group col-lg-12 col-md-12 col-xs-12">
					<div id="alert-success"></div>
				</div>


				<div class="col-lg-12 col-md-12 col-xs-12 form-group">
					<div class="form-group">
							<input type="submit" class="btn btn-success block btn-flat pull-right" value="Bonus Process">
					</div>
				</div>
			</div>

</form>

		</div>

@endsection
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>

<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
$(document).ready(function($) {


    $('#declaration_date').datepicker({
      autoclose: true
    });



	$("#basedMonth").datepicker( {
	    format: "dd-mm-yyyy",
	    viewMode: "months", 
	    minViewMode: "months",
        autoclose: true

	});



    $('.location').select2({
    	placeholder: 'Enter Location',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/location_list_data",
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

     

    $('.bonus').select2({
    	placeholder: 'Enter Bonus Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/bonus_name_list",
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






var colors = ["red", "blue", "green", "orange", "yellow"];


    $('#employeestatus').select2({
      placeholder: 'Enter employee status',
      width: "100%",
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employeestatus_list_data',
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
      },
    templateSelection: function (data, container) {
      var selection = $('#employeestatus').select2('data');
      var idx = selection.indexOf(data);
      console.log(">>Selection",data.text, data.idx, idx);
      data.idx = idx;
      
      $(container).css("background-color", colors[data.idx]);
      return data.text;
    },


  });


  $("#employeestatus").on("select2:select", function (evt) {
    var element = evt.params.data.element;
    var $element = $(element);

    $element.detach();
    $(this).append($element);
    $(this).trigger("change");
  });
  


 $( "#frm_process" ).submit(function(event){
      	event.preventDefault();

        if(confirm('Do you want to submit?')){
          var $form   = $( this ),
          url         = $form.attr( "action" ); 
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : url, // the url where we want to POST
            data        : $form.serialize(),
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true,
            _token      : token
          })
          .done(function(data) {  
              console.log(data.messages);
              if(data['success']) {

                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);   
                $('#alert-success').show(0).delay(4000).hide(0); 

              }else{
              	
				  var erreurs ='<div class="alert alert-danger"><ul>';
				      erreurs += '<li>'+data.messages+'</li>';
				      erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);   
                  $('#alert-danger').show(0).delay(4000).hide(0);  
              }
          });
        }else{
          return;
        }      
    });


});






</script>

<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script type="text/javascript">
    	
        function sum() {
            var txtFirstNo = document.getElementById('rate').value;
            var txtSecondNo = document.getElementById('days').value;

 

            var result = parseInt(txtFirstNo) * parseInt(txtSecondNo);
            if (!isNaN(result)) {
                document.getElementById('total_amount').value = result;
            }
        }


        function BasicPersent() {

            var txtFirstNo = document.getElementById('total_amount').value;
            var txtSecondNo = document.getElementById('zero_to_six_months').value;

            var result = parseInt(txtFirstNo) * parseInt(txtSecondNo)/100;
            if (!isNaN(result)) {
                document.getElementById('basic_of_gross_amount').value = result;
            }
        }



        // function BonusPersent() {

        //     var txtFirstNo = document.getElementById('total_amount').value;
        //     var txtSecondNo = document.getElementById('bonus_of_gross').value;

        //     var result = parseInt(txtFirstNo) * parseInt(txtSecondNo)/100;
        //     if (!isNaN(result)) {
        //         document.getElementById('bonus_of_gross_amount').value = result;
        //     }
        // }




    </script>
		@endsection
