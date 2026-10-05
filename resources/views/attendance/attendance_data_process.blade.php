
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">

.disabled.day{
   background: #eee!important;
}

</style>


@endsection

@section('content')

<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Attendance Data Process</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <div class="form-group col-lg-12 col-md-12 col-xs-12">

	    <div class="form-group col-lg-6 col-md-6 col-xs-12">
	       <div id="alert-danger1"></div>
	       <div id="alert-success1"></div>
	    </div>

    </div>



    {!! Form::open(array('route'=>'data_process.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_data_process')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div style="text-align: right;">

	<a href="javascript:void(0);" onclick="refreshLog()" class="btn" style="font-size: 10px; padding: 2px 5px;">Refresh Log</a>


		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-xs-12 form-group">
		        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }}">
		        	<label class="control-label">Location</label>
		            <select class="form-control" id="location" name="location" style="width: 100%;" required>

						@foreach ($default_user_location as $keys)
								<option value={{$keys->id}} selected>{{$keys->location_name}}</option>
						@endforeach

		            </select>
		            @if ($errors->has('location'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('location') }}</strong>
		                </span>
		            @endif
		        </div>
	        </div>

	        <div class="col-lg-12 col-md-12 col-xs-12 form-group">
		        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('process_date') ? ' has-error' : '' }}">
	            	<label class="control-label">Process Date</label>
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
			            @if ($errors->has('process_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('process_date') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>
	        </div>


	        <div class="col-lg-12 col-md-12 col-xs-12 form-group">
	        	<div class="col-lg-6 col-md-6 col-xs-12 form-group">
	        		<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="btnSubmit" style="margin-right: 10px;">
	        	</div>
	        </div>


		</div>
	</div>
	{!! Form::close() !!}
</div>

@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>


<script type="text/javascript">
	    function refreshLog() {
        var locationVal = document.getElementById("location").value; // Make sure your location input has an id="location"
        window.location.href = `{{ URL::to('refresh_log') }}/${locationVal}`;
    }


</script>

<script>
$(document).ready(function($) {

    $('#process_date').datepicker({
       autoclose: true,
    //    endDate: '+0d',
    });

    $('#location').select2({
      placeholder: 'Enter a location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
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




	    $( "#frm_data_process" ).submit(function(event){
	        event.preventDefault();

	        if(confirm('Do you want to process?')){

	        	$("#btnSubmit").attr("disabled", true);
	        	$("#btnSubmit").val('Please wait..');
						var $form   = $( this ),
						url         = $form.attr( "action" );
						token 		= $("[name='_token']").val();
						$.ajax({
							type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
							url         : url, // the url where we want to POST
							data 		: $form.serialize(),
							dataType    : 'json', // what type of data do we expect back from the server
							encode      : true,
							_token 		: token
						})
						.done(function(data) {
							if(data['success']) {
								// window.open('{{URL::to('/')}}/journal_voucher/'+(data.master_id), '_blank');
								// window.location.replace("{{ URL::to('data_process')}}");
								// data['messages'];
								var erreurs ='<div class="alert alert-success"><ul>';
								erreurs += '<li>'+data.messages+'</li>';
								erreurs += '</ul></div>';
								$('#alert-success1').html(erreurs);
								$('#alert-success1').show(0).delay(4000).hide(0);

								$('#btnSubmit').attr("disabled", false);
								$("#btnSubmit").val('Submit');

							}else{

		                        var erreurs ='<div class="alert alert-danger"><ul>';
								erreurs += '<li>'+data.messages+'</li>';
								erreurs += '</ul></div>';
								$('#alert-danger1').html(erreurs);
								$('#alert-danger1').show(0).delay(8000).hide(0);

								$('#btnSubmit').attr("disabled", false);
								$("#btnSubmit").val('Submit');




							}
						});
		    }else{
		    	return;
		    }
	    });





});
</script>
@endsection
