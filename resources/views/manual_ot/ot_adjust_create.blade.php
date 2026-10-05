<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/clockpicker/bootstrap-clockpicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/clockpicker/clockpicker.css')}}">

<style type="text/css">
.popover.left {
    margin-left: 183px;
}

</style>


@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">OT Adjust Entry(CW)</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('ot_adjust_submit')}}">
        {{ csrf_field() }}


			<div class="box-body">
				<div class="row">

					<div class="col-lg-8 col-md-8 col-xs-12 personal-info">

				        <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
				          <label class="col-lg-3 control-label">Year</label>
				          <div class="col-lg-9">
				            <select  class="form-control col-lg-12 year" id="year" name="year" style="width: 100%;" >
				            </select>
				          </div>
				        </div>

				        <div class="form-group has-feedback {{ $errors->has('month_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
				          <label class="col-lg-3 control-label">Month Name</label>
				          <div class="col-lg-9">
				            <select  class="form-control col-lg-12" id="month_name" name="month_name" style="width: 100%;" >
				              @foreach ($month as $keys)
				              <option value={{$keys->id}}>{{$keys->month_name}}</option>
				              @endforeach
				            </select>
				          </div>
				        </div>
				        

				        <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
				            <label class="col-lg-3 control-label">Location Name</label>
				            <div class="col-lg-9">
								<select style="width: 100%;" class="form-control select2" id="location" name="location" required autofocus >
								</select>
					            @if ($errors->has('location'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('location') }}</strong>
					                </span>
					            @endif 	                
				            </div>
				        </div>



						<div class="form-group  col-lg-12 col-md-12 col-xs-12">
							<div class="col-lg-12">
								<input type="submit" class="btn btn-success block btn-flat pull-right" value="Make Adjust">
								<input type="button" id="frm_process" class="btn btn-warning block btn-flat pull-right" value="Reverse">
							</div>
						</div>

				</div>
			</div>




		</form>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>

<script src="{{asset('plugins/clockpicker/bootstrap-clockpicker.min.js')}}"></script>
<script src="{{asset('plugins/clockpicker/clockpicker.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>


<script type="text/javascript">
	$('.clockpicker').clockpicker();
</script>


<script>
$(document).ready(function($) {



    for (i = new Date().getFullYear(); i > 2022; i--){
      $('.year').append($('<option>').val(i).html(i));
    }




    $employee = $('#location').select2({
    	placeholder: 'Enter an Location Name',
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



    $( "#frm_process" ).click(function(event){
        event.preventDefault();

        if(confirm('Do you want to submit?')){

          $("#btnSubmit").attr("disabled", true);
          $("#btnSubmit").val('Please wait..');

 
          var $form   = $(this).closest('form'),
          url         = $form.attr( "action" ) + '?reserve';
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', 
            url         : url, 
            data        : $form.serialize(),
            dataType    : 'json',
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

                $('#btnSubmit').attr("disabled", false);
                $("#btnSubmit").val('Submit');


              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);
                  $('#alert-danger').show(0).delay(4000).hide(0);

                  $('#btnSubmit').attr("disabled", false);
                  $("#btnSubmit").val('Submit');


              }
          });
        }else{
          return;
        }
    });



</script>


@endsection
