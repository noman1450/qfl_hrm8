
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
		<h3 class="box-title">Data Sync</h3>
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



    {!! Form::open(array('route'=>'data_sync', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_data_process')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="col-lg-12 col-md-12 col-xs-12 form-group">
		        <div class="col-lg-4 col-md-4 col-xs-12 form-group has-feedback {{ $errors->has('process_date') ? ' has-error' : '' }}">
	            	<label class="control-label">Sync Date</label>
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
	        	<div class="col-lg-4 col-md-4 col-xs-12 form-group">
	        		<input type="submit" class="btn btn-success btn-flat pull-right" value="Data Sync" id="btnSubmit" style="margin-right: 10px;">
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
<script>
$(document).ready(function($) {

    $('#process_date').datepicker({
       autoclose: true,
       endDate: '+0d',
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
