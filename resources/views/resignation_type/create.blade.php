<!-- create_resignation_type -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Resignation Type</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('resignation_type')}}" id="frm_resignation_type">
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('resignation_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Resignation Type</label>
	                <input type="text" class="form-control" name="resignation_type" placeholder="resignation type" value="{{ old('resignation_type') }}" required autofocus >
		            @if ($errors->has('resignation_type'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('resignation_type') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>
		</div>
	</div>

	<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Submit</button>

	            </div>
	        </div>
        </div>
	</div>
    </form>
	{{-- {!! Form::close() !!} --}}
</div>
@endsection


@section('script')


<script>

    $( document ).ready(function() {
        $(document).on('submit','#frm_resignation_type',function(e){
            if(confirm('Do you want to save?')){
                return true;
            }else{
                e.preventDefault()
            }
        })
    });

	    // $( "#frm_resignation_type" ).submit(function(event){
	    //     event.preventDefault();

	    //     if(confirm('Do you want to save?')){
		// 		var $form   = $( this ),
		// 		url         = $form.attr( "action" );
		// 		token 		= $("[name='_token']").val();
		// 		$.ajax({
		// 			type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
		// 			url         : url, // the url where we want to POST
		// 			data 		: $form.serialize(),
		// 			dataType    : 'json', // what type of data do we expect back from the server
		// 			encode      : true,
		// 			_token 		: token
		// 		})
		// 		.done(function(data) {
		// 			console.log(data);
		// 			if(data['success']) {

		// 				window.location.replace("{{ URL::to('resignation_type')}}");
		// 				var erreurs ='<div class="alert alert-success"><ul>';
		//                 $.each(data.errors, function(i,error){
		//                     erreurs += '<li>'+error+'</li>';
		//                 });
		//                 erreurs += '</ul></div>';
		//                 $('#alert-success').html(erreurs);
		// 			}else{
		//                 var erreurs ='<div class="alert alert-danger"><ul>';
		//                 $.each(data.errors, function(i,error){
		//                     erreurs += '<li>'+error+'</li>';
		//                 });
		//                 erreurs += '</ul></div>';
		//                 $('#alert-danger').html(erreurs);
		// 			}
		// 		});
		//     }else{
		//     	return;
		//     }
	    // });


</script>

@endsection
