<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Employee Bonus Generate</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>



 <form class="form-horizontal" method="POST"  id="frm_process" action="{{url('bonusgeneratesubmit')}}">
        {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


    			<div class="col-lg-6 col-md-6 col-xs-6 personal-info">

            <div id="alert-danger"></div>
            <div id="alert-success"></div>
            


              <div class="form-group has-feedback {{ $errors->has('hrm_employee_bonus_master_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Bonus Name</label>
                <div class="col-lg-9">
                  
                  <select  class="form-control col-lg-12 bonus_master"  name="hrm_employee_bonus_master_id"  style="width: 100%;" required>
                  </select>
                  
                </div>
              </div>

              <div class="form-group has-feedback {{ $errors->has('payment_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Payment Date<span style="color: red">*</span> </label>
                <div class="col-lg-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="payment_date" name="payment_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('payment_date') }}" required readonly>
                    
                  </div>
                </div>
              </div>

              <div class="form-group has-feedback {{ $errors->has('bonus_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Write Note </label>
                <div class="col-lg-9">
                  
                    <input type="text" name="note" class="form-control"  maxlength="95" placeholder="Write Note..." required>
                  
                </div>
              </div>

                <div class="form-group">
                    <label class="col-md-12 control-label"></label>
                    <div class="col-md-11">
                        <input type="submit" class="btn btn-success block btn-flat pull-right" value="Final Generate">
                        <span></span>
                    </div>
                </div>

    		</div>
	</div>

</div>
</form>

@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#payment_date').datepicker({
      autoclose: true
    });



    $('.bonus_master').select2({
      placeholder: 'Enter Bonus Name',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/process_bonus_list",
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
              console.log(data);
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
                $('#alert-success').html(erreurs);   
                $('#alert-success').show(0).delay(4000).hide(0); 
              }
          });
        }else{
          return;
        }      
    });



});
</script>
@endsection


